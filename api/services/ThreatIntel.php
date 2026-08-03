<?php
class ThreatIntel {
    public static function checkThreatIntel($domain, $url) {
        $result = [
            'status' => "CLEAN",
            'checked' => true,
            'providers' => [],
            'malicious' => false,
            'phishing' => false,
            'malware' => false,
            'suspicious' => false,
            'blacklistMatch' => false,
            'detections' => 0,
            'checkedAt' => date('c'),
            'error' => null,
            'raw_details' => [
                'virustotal' => 'Not Configured',
                'google_safe_browsing' => 'Not Configured',
                'abuseipdb' => 'Not Configured'
            ]
        ];

        try {
            // 1. Read API keys
            $keysFile = __DIR__ . '/../keys.json';
            $keys = [];
            if (file_exists($keysFile)) {
                $keys = json_decode(file_get_contents($keysFile), true) ?: [];
            }

            $vtKey = $keys['virustotal'] ?? '';
            $gsbKey = $keys['google_safe_browsing'] ?? '';
            $abuseKey = $keys['abuseipdb'] ?? '';

            // Check if keys are actual keys or placeholders
            $isVtConfigured = !empty($vtKey) && $vtKey !== 'Google Cloud Console' && $vtKey !== 'virustotal.com';
            $isGsbConfigured = !empty($gsbKey) && $gsbKey !== 'Google Cloud Console' && $gsbKey !== 'virustotal.com';
            $isAbuseConfigured = !empty($abuseKey) && $abuseKey !== 'Google Cloud Console' && $abuseKey !== 'virustotal.com';

            // Resolve IP for AbuseIPDB
            $ip = gethostbyname($domain);
            $isValidIp = filter_var($ip, FILTER_VALIDATE_IP) !== false;

            // 2. Query VirusTotal API (v3)
            if ($isVtConfigured) {
                $urlId = rtrim(strtr(base64_encode($url), '+/', '-_'), '=');
                $vtUrl = "https://www.virustotal.com/api/v3/urls/" . $urlId;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $vtUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["x-apikey: " . $vtKey]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $vtResponse = curl_exec($ch);
                curl_close($ch);

                if ($vtResponse) {
                    $vtData = json_decode($vtResponse, true);
                    if (isset($vtData['data']['attributes']['last_analysis_stats'])) {
                        $stats = $vtData['data']['attributes']['last_analysis_stats'];
                        $result['raw_details']['virustotal'] = $stats;
                        $result['providers'][] = 'VirusTotal';

                        $malicious = $stats['malicious'] ?? 0;
                        $suspicious = $stats['suspicious'] ?? 0;

                        if ($malicious > 0) {
                            $result['malicious'] = true;
                            $result['status'] = "MALICIOUS";
                            $result['detections'] += $malicious;
                        }
                        if ($suspicious > 0) {
                            $result['suspicious'] = true;
                            if ($result['status'] !== 'MALICIOUS') $result['status'] = "SUSPICIOUS";
                        }
                    }
                }
            }

            // 3. Query Google Safe Browsing API (v4)
            if ($isGsbConfigured) {
                $gsbUrl = "https://safebrowsing.googleapis.com/v4/threatMatches:find?key=" . $gsbKey;
                $payload = [
                    'client' => ['clientId' => 'FraudEye', 'clientVersion' => '1.0.0'],
                    'threatInfo' => [
                        'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING', 'UNWANTED_SOFTWARE', 'POTENTIALLY_HARMFUL_APPLICATION'],
                        'platformTypes' => ['ANY_PLATFORM'],
                        'threatEntryTypes' => ['URL'],
                        'threatEntries' => [['url' => $url]]
                    ]
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $gsbUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $gsbResponse = curl_exec($ch);
                curl_close($ch);

                if ($gsbResponse) {
                    $gsbData = json_decode($gsbResponse, true);
                    $result['providers'][] = 'Google Safe Browsing';
                    if (isset($gsbData['matches']) && count($gsbData['matches']) > 0) {
                        $result['raw_details']['google_safe_browsing'] = $gsbData['matches'];
                        $result['phishing'] = true;
                        $result['blacklistMatch'] = true;
                        $result['status'] = "MALICIOUS";
                        $result['detections'] += count($gsbData['matches']);
                    } else {
                        $result['raw_details']['google_safe_browsing'] = 'Clean';
                    }
                }
            }

            // 4. Query AbuseIPDB API (v2)
            if ($isAbuseConfigured && $isValidIp) {
                $abuseUrl = "https://api.abuseipdb.com/api/v2/check?ipAddress=" . urlencode($ip) . "&maxAgeInDays=90";
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $abuseUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Key: " . $abuseKey,
                    "Accept: application/json"
                ]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $abuseResponse = curl_exec($ch);
                curl_close($ch);

                if ($abuseResponse) {
                    $abuseData = json_decode($abuseResponse, true);
                    if (isset($abuseData['data'])) {
                        $score = $abuseData['data']['abuseConfidenceScore'] ?? 0;
                        $result['raw_details']['abuseipdb'] = $abuseData['data'];
                        $result['providers'][] = 'AbuseIPDB';
                        
                        if ($score > 20) {
                            $result['suspicious'] = true;
                            if ($result['status'] !== 'MALICIOUS') $result['status'] = "SUSPICIOUS";
                        }
                        if ($score > 50) {
                            $result['malicious'] = true;
                            $result['status'] = "MALICIOUS";
                            $result['detections']++;
                        }
                    }
                }
            }

            // 5. Local Heuristics & Blacklist Fallback (if APIs are missing/empty or return nothing)
            if (empty($result['providers'])) {
                $blacklist = ['evil.com', 'phishingsite.net', 'fake-upi-update.com', 'scam.example.com', 'malicious.xyz'];
                $allowlist = ['google.com', 'github.com', 'example.com', 'wikipedia.org', 'openai.com', 'apple.com', 'microsoft.com'];

                $domainLower = strtolower($domain);
                $result['providers'][] = 'Local Heuristic Engine';

                // Check blacklist
                foreach ($blacklist as $b) {
                    if (strpos($domainLower, $b) !== false || $domainLower === $b) {
                        $result['status'] = "MALICIOUS";
                        $result['malicious'] = true;
                        $result['phishing'] = true;
                        $result['blacklistMatch'] = true;
                        $result['detections'] = 2;
                        return $result;
                    }
                }

                // Check allowlist
                foreach ($allowlist as $a) {
                    if (strpos($domainLower, $a) !== false || $domainLower === $a) {
                        $result['status'] = "CLEAN";
                        $result['detections'] = 0;
                        return $result;
                    }
                }

                // Generic Heuristic Check
                if (preg_match('/(secure|payment|login|banking|update|verify|account|wallet|otp|gift|prize|free|urgent)/i', $domainLower)) {
                    $result['status'] = "SUSPICIOUS";
                    $result['suspicious'] = true;
                }
            }

        } catch (Exception $e) {
            $result['status'] = "API_ERROR";
            $result['error'] = $e->getMessage();
        }

        return $result;
    }
}
?>

<?php
class ThreatIntel {
    public static function checkThreatIntel($domain, $url) {
        $result = [
            'status' => "NOT_CHECKED",
            'checked' => false,
            'providers' => [],
            'malicious' => false,
            'phishing' => false,
            'malware' => false,
            'suspicious' => false,
            'blacklistMatch' => false,
            'detections' => 0,
            'checkedAt' => date('c'),
            'error' => null
        ];

        try {
            // Local Heuristic Fallback Lists
            $blacklist = ['evil.com', 'phishingsite.net', 'fake-upi-update.com', 'scam.example.com', 'malicious.xyz'];
            $allowlist = ['google.com', 'github.com', 'example.com', 'reactjs.org', 'cloudflare.com', 'wikipedia.org', 'openai.com', 'python.org'];

            $domainLower = strtolower($domain);

            // Check if it's explicitly blacklisted
            foreach ($blacklist as $b) {
                if (strpos($domainLower, $b) !== false || $domainLower === $b) {
                    $result['status'] = "MALICIOUS";
                    $result['checked'] = true;
                    $result['malicious'] = true;
                    $result['phishing'] = true; // Assuming phishing for this demo
                    $result['blacklistMatch'] = true;
                    $result['detections'] = 2;
                    $result['providers'] = ['Local_Blacklist'];
                    return $result;
                }
            }

            // Check if it's explicitly allowlisted
            foreach ($allowlist as $a) {
                if (strpos($domainLower, $a) !== false || $domainLower === $a) {
                    $result['status'] = "CLEAN";
                    $result['checked'] = true;
                    $result['detections'] = 0;
                    $result['providers'] = ['Local_Allowlist'];
                    return $result;
                }
            }

            // Default fallback if no APIs configured and not in local lists
            $result['status'] = "NOT_CHECKED";
            $result['checked'] = false;
            $result['error'] = "No threat intelligence API keys configured.";

        } catch (Exception $e) {
            $result['status'] = "API_ERROR";
            $result['error'] = $e->getMessage();
        }

        return $result;
    }
}

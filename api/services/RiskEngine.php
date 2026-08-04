<?php
require_once 'Classifier.php';
require_once 'Analyzer.php';
require_once 'DomainChecker.php';
require_once 'SSLChecker.php';
require_once 'RedirectChecker.php';
require_once 'ThreatIntel.php';
require_once 'GeoIP.php';
require_once 'SecurityHeaders.php';
require_once 'PageScraper.php';

class RiskEngine {
    private $evidenceMap = [];
    private $totalRisk = 0;

    private function addEvidence($id, $source, $severity, $riskContribution, $description) {
        if (!isset($this->evidenceMap[$id])) {
            $this->evidenceMap[$id] = [
                'id' => $id, 
                'source' => [$source], 
                'severity' => $severity, 
                'riskContribution' => $riskContribution, 
                'description' => $description
            ];
            $this->totalRisk += $riskContribution;
        } else {
            if (!in_array($source, $this->evidenceMap[$id]['source'])) {
                $this->evidenceMap[$id]['source'][] = $source;
            }
        }
    }

    public function processPayload($payload, $qrImage = null) {
        $this->evidenceMap = [];
        $this->totalRisk = 0;

        $payloadClass = Classifier::classifyPayload($payload);
        
        $analysis = null;
        $domainCheck = null;
        $sslCheck = null;
        $threatIntel = null;
        $redirectCheck = null;
        $geoIP = null;
        $headersCheck = null;

        $checksCompleted = 0;
        $totalPossibleChecks = 7; // Classify, Domain, SSL, Redirect, ThreatIntel, GeoIP, Headers

        if ($payloadClass['type'] === 'url') {
            $analysis = Analyzer::analyzeUrl($payload);
            
            // Fetch live page metadata (Title, Description)
            $metadata = PageScraper::fetchMetadata($analysis['normalizedUrl']);
            $analysis['title'] = $metadata['title'];
            $analysis['description'] = $metadata['description'];
            
            if (isset($analysis['indicators']) && is_array($analysis['indicators'])) {
                foreach ($analysis['indicators'] as $ind) {
                    $this->addEvidence($ind['id'], 'URL_ANALYSIS', $ind['severity'], $ind['riskContribution'], $ind['description']);
                }
            }

            $domain = $analysis['domain'];
            
            // 1. Domain Check
            $domainCheck = DomainChecker::checkDomain($domain);
            if ($domainCheck['status'] !== 'NOT_CHECKED' && $domainCheck['status'] !== 'ERROR') $checksCompleted++;
            
            if ($domainCheck['status'] === 'NOT_RESOLVED') {
                $this->addEvidence('DOMAIN_NO_RESOLVE', 'DOMAIN_CHECK', 'high', 25, 'Domain does not resolve via DNS.');
            } else if ($domainCheck['status'] === 'INVALID_SYNTAX') {
                $this->addEvidence('DOMAIN_INVALID_SYNTAX', 'DOMAIN_CHECK', 'high', 35, 'Domain syntax is invalid.');
            }
            if ($domainCheck['newlyRegistered']) {
                $this->addEvidence('DOMAIN_NEWLY_REGISTERED', 'DOMAIN_CHECK', 'medium', 15, 'Very newly registered domain.');
            }
            if ($domainCheck['punycodeDetected']) {
                $this->addEvidence('PUNYCODE_HOST', 'DOMAIN_CHECK', 'medium', 10, 'Punycode domain detected.');
            }
            if ($domainCheck['rawIpHost']) {
                $this->addEvidence('RAW_IP_HOST', 'DOMAIN_CHECK', 'medium', 15, 'Raw IP host used instead of domain name.');
            }

            // 2. Redirect Check
            $redirectCheck = RedirectChecker::checkRedirects($analysis['normalizedUrl']);
            if ($redirectCheck['checked']) $checksCompleted++;

            if ($redirectCheck['crossDomainRedirect']) {
                $this->addEvidence('CROSS_DOMAIN_REDIRECT', 'REDIRECT_CHECK', 'low', 8, 'Redirects to a different domain.');
            }
            if ($redirectCheck['httpsDowngrade']) {
                $this->addEvidence('HTTPS_DOWNGRADE', 'REDIRECT_CHECK', 'high', 20, 'HTTPS to HTTP downgrade during redirect.');
            }
            if ($redirectCheck['excessiveRedirects']) {
                $this->addEvidence('EXCESSIVE_REDIRECTS', 'REDIRECT_CHECK', 'medium', 15, 'Excessive redirects detected (possibly hiding final payload).');
            }
            if ($redirectCheck['redirectLoop']) {
                $this->addEvidence('REDIRECT_LOOP', 'REDIRECT_CHECK', 'medium', 15, 'Redirect loop detected.');
            }

            // 3. SSL Check (on final URL)
            $urlToCheckSSL = $redirectCheck['finalUrl'] ?? $analysis['normalizedUrl'];
            $sslCheck = SSLChecker::checkSSL($urlToCheckSSL);
            if ($sslCheck['checked']) $checksCompleted++;
            
            if ($sslCheck['status'] === 'HTTP_ONLY') {
                $this->addEvidence('HTTP_ONLY', 'SSL_CHECK', 'low', 8, 'Connection is not encrypted (HTTP).');
            } else if ($sslCheck['status'] === 'EXPIRED') {
                $this->addEvidence('SSL_EXPIRED', 'SSL_CHECK', 'high', 20, 'SSL certificate has expired.');
            } else if ($sslCheck['status'] === 'HOSTNAME_MISMATCH') {
                $this->addEvidence('SSL_HOSTNAME_MISMATCH', 'SSL_CHECK', 'high', 25, 'SSL certificate hostname mismatch.');
            } else if ($sslCheck['status'] === 'SELF_SIGNED') {
                $this->addEvidence('SSL_SELF_SIGNED', 'SSL_CHECK', 'medium', 15, 'SSL certificate is self-signed.');
            } else if ($sslCheck['status'] === 'NO_CERTIFICATE') {
                $this->addEvidence('SSL_NO_CERTIFICATE', 'SSL_CHECK', 'high', 20, 'No certificate found on expected HTTPS port.');
            }

            // 4. GeoIP Check
            $geoIP = GeoIP::lookup($domain);
            if ($geoIP['country'] !== 'Unknown') $checksCompleted++;

            // 5. Security Headers Check
            $headersCheck = SecurityHeaders::checkHeaders($urlToCheckSSL);
            if ($headersCheck['checked']) $checksCompleted++;

            // 6. Threat Intel
            $threatIntel = ThreatIntel::checkThreatIntel($domain, $analysis['normalizedUrl']);
            if ($threatIntel['checked']) $checksCompleted++;

            if ($threatIntel['status'] === 'MALICIOUS') {
                $this->addEvidence('THREAT_MALICIOUS', 'THREAT_INTEL', 'critical', 80, 'Confirmed malicious by threat intelligence.');
            } else if ($threatIntel['status'] === 'SUSPICIOUS') {
                $this->addEvidence('THREAT_SUSPICIOUS', 'THREAT_INTEL', 'high', 30, 'Suspicious provider result.');
            }
            if ($threatIntel['blacklistMatch']) {
                $this->addEvidence('THREAT_BLACKLIST', 'THREAT_INTEL', 'critical', 65, 'Matches a known threat blacklist.');
            }
            if ($threatIntel['detections'] > 1) {
                $this->addEvidence('THREAT_MULTIPLE', 'THREAT_INTEL', 'high', 20, 'Multiple threat engines flagged this URL.');
            }

            // Brand Impersonation Check (Simple Heuristics)
            $brands = [
                'paytm' => ['paytm.com', 'paytm.in', 'paytm.me'],
                'paypal' => ['paypal.com', 'paypal.me'],
                'netflix' => ['netflix.com'],
                'amazon' => ['amazon.com', 'amazon.in', 'amazon.co.uk'],
                'apple' => ['apple.com'],
                'microsoft' => ['microsoft.com']
            ];
            
            $isSimulated = (str_ends_with($domain, '.example') || str_ends_with($domain, '.test'));

            foreach ($brands as $brand => $legitDomains) {
                if (strpos(strtolower($domain), $brand) !== false) {
                    $isLegit = false;
                    foreach ($legitDomains as $ld) {
                        if (strtolower($domain) === $ld || str_ends_with(strtolower($domain), '.' . $ld)) {
                            $isLegit = true;
                            break;
                        }
                    }
                    if (!$isLegit) {
                        $this->addEvidence('BRAND_IMPERSONATION', 'URL_ANALYSIS', 'critical', $isSimulated ? 75 : 85, "Possible brand impersonation ($brand) detected.");
                    }
                }
            }
            
            if ($isSimulated) {
                $this->addEvidence('SIMULATED_DOMAIN', 'DOMAIN_CHECK', 'high', 0, 'Domain is a simulated or reserved test domain.');
            }

            // --- WEIGHTED SCORING ENGINE ---
            $sslPoints = ($sslCheck && $sslCheck['status'] === 'VALID') ? 20 : (($sslCheck && $sslCheck['status'] === 'SELF_SIGNED') ? 10 : 0);
            
            $domainPoints = 15;
            if ($domainCheck) {
                if ($domainCheck['status'] === 'NOT_RESOLVED') $domainPoints = 0;
                elseif ($domainCheck['newlyRegistered']) $domainPoints = 5;
            }

            $gsbPoints = 20;
            if (isset($threatIntel['raw_details']['google_safe_browsing']) && is_array($threatIntel['raw_details']['google_safe_browsing'])) {
                $gsbPoints = 0;
            }

            $vtPoints = 15;
            if (isset($threatIntel['raw_details']['virustotal']) && is_array($threatIntel['raw_details']['virustotal'])) {
                $vtMalicious = $threatIntel['raw_details']['virustotal']['malicious'] ?? 0;
                if ($vtMalicious > 0) {
                    $vtPoints = max(0, 15 - ($vtMalicious * 3));
                }
            }

            $phishPoints = 10;
            $hasPhishing = false;
            foreach ($this->evidenceMap as $ev) {
                if ($ev['id'] === 'BRAND_IMPERSONATION') $hasPhishing = true;
            }
            if ($hasPhishing || ($threatIntel && $threatIntel['phishing'])) {
                $phishPoints = 0;
            }

            $blacklistPoints = 10;
            if ($threatIntel && ($threatIntel['blacklistMatch'] || $threatIntel['status'] === 'MALICIOUS')) {
                $blacklistPoints = 0;
            }

            $headersPoints = 0;
            if ($headersCheck && $headersCheck['checked']) {
                $grade = $headersCheck['grade'];
                if ($grade === 'A') $headersPoints = 5;
                elseif ($grade === 'B' || $grade === 'C') $headersPoints = 3;
            }

            $redirectPoints = 5;
            $hasSuspiciousRedirects = ($redirectCheck && ($redirectCheck['crossDomainRedirect'] || $redirectCheck['httpsDowngrade'] || $redirectCheck['excessiveRedirects'] || $redirectCheck['redirectLoop']));
            if ($hasSuspiciousRedirects) {
                $redirectPoints = 0;
            }

            // Calculate final trust score (out of 100)
            $trustScore = $sslPoints + $domainPoints + $gsbPoints + $vtPoints + $phishPoints + $blacklistPoints + $headersPoints + $redirectPoints;
            $trustScore = max(0, min(100, $trustScore));
            $riskScore = 100 - $trustScore;

            // VERDICT / RISK LEVEL
            if ($trustScore >= 90) {
                $verdict = 'SAFE'; // Low Risk
            } else if ($trustScore >= 70) {
                $verdict = 'LOW_RISK'; // Low Risk / Caution
            } else if ($trustScore >= 50) {
                $verdict = 'SUSPICIOUS'; // Medium Risk
            } else {
                $verdict = 'DANGEROUS'; // Critical Risk
            }

            // Hard overrides
            $isBlacklisted = ($threatIntel && ($threatIntel['blacklistMatch'] ?? false || $threatIntel['status'] === 'MALICIOUS'));
            if ($verdict === 'SAFE' && ($hasPhishing || $isBlacklisted || ($sslCheck && $sslCheck['status'] !== 'VALID'))) {
                $verdict = 'LOW_RISK';
            }

        } else if ($payloadClass['type'] === 'upi' || $payloadClass['type'] === 'upi_id_only') {
            $checksCompleted = 7;
            $trustScore = 100;
            $d = $payloadClass['data'];

            if ($payloadClass['type'] === 'upi') {
                if (isset($d['error'])) {
                    $this->addEvidence('UPI_FORMAT_ERROR', 'UPI_ANALYSIS', 'high', 80, 'Malformed UPI URI.');
                    $trustScore -= 60;
                } else {
                    if (empty($d['pa'])) {
                        $this->addEvidence('UPI_MISSING_PA', 'UPI_ANALYSIS', 'high', 50, 'Missing Payee Address (pa).');
                        $trustScore -= 30;
                    }
                    if (!empty($d['am'])) {
                        $amNum = floatval($d['am']);
                        if ($amNum > 100000) {
                            $this->addEvidence('UPI_LARGE_AMOUNT', 'UPI_ANALYSIS', 'medium', 15, 'Suspiciously large transaction amount requested.');
                            $trustScore -= 20;
                        }
                    }
                }
            } else { // upi_id_only
                $vpa = $d['vpa'] ?? '';
                if (strpos($vpa, '@') === false) {
                    $this->addEvidence('UPI_MISSING_AT', 'UPI_ANALYSIS', 'high', 50, 'Missing @ symbol in UPI ID.');
                    $trustScore -= 40;
                }
            }

            $trustScore = max(0, $trustScore);
            $riskScore = 100 - $trustScore;
            
            if ($trustScore >= 90) $verdict = 'SAFE';
            elseif ($trustScore >= 70) $verdict = 'LOW_RISK';
            elseif ($trustScore >= 50) $verdict = 'SUSPICIOUS';
            else $verdict = 'DANGEROUS';

            // UPI holder name - use what's in the QR code (self-declared by creator), do NOT fabricate
            if ($payloadClass['type'] === 'upi_id_only') {
                // For standalone UPI IDs, we have no payee name info
                $payloadClass['data']['pn'] = null;
                $payloadClass['data']['pa'] = $d['vpa'] ?? null;
                $payloadClass['data']['verification_status'] = 'Unable to Verify - No bank API available';
            } else {
                // For upi:// URIs, pn comes from the QR code itself (self-declared, NOT verified)
                $payloadClass['data']['verification_status'] = 'Unverified - Name is self-declared by QR creator';
            }

        } else {
            // Plain text
            $checksCompleted = 7;
            $trustScore = 70; // Plain text defaults to LOW_RISK/Caution
            $riskScore = 30;
            $verdict = 'LOW_RISK';
            $this->addEvidence('PLAIN_TEXT', 'TEXT_ANALYSIS', 'info', 5, 'Payload is plain text. Ensure it does not contain deceptive instructions.');
        }

        // Summary values for report
        $summary = [];
        $summary['ssl'] = ($sslCheck && $sslCheck['status'] === 'VALID') ? 'Valid Certificate' : ($sslCheck ? $sslCheck['status'] : 'No Certificate');
        $summary['domainAge'] = ($domainCheck && $domainCheck['domainAgeDays'] !== 'Unavailable') ? $domainCheck['domainAgeDays'] . ' Days' : 'Unavailable';
        $summary['blacklist'] = ($threatIntel && $threatIntel['blacklistMatch']) ? 'Blacklisted' : 'Clean';
        $summary['malware'] = ($threatIntel && $threatIntel['malicious']) ? 'Malware Detected' : 'Clean';
        $summary['phishing'] = $hasPhishing ? 'Suspicious Phishing' : 'No Phishing';
        $summary['redirect'] = $hasSuspiciousRedirects ? 'Redirect Warnings' : 'No Redirect Warnings';
        $summary['content'] = 'Analyzed';
        $summary['recommendation'] = $verdict === 'SAFE' ? 'Safe to visit.' : ($verdict === 'LOW_RISK' ? 'Visit with caution.' : 'Avoid this destination.');

        $confidence = round(($checksCompleted / $totalPossibleChecks) * 100);

        return [
            'payloadClass' => $payloadClass,
            'analysisResult' => $analysis,
            'domainCheck' => $domainCheck,
            'sslCheck' => $sslCheck,
            'threatIntel' => $threatIntel,
            'redirectCheck' => $redirectCheck,
            'geoIP' => $geoIP,
            'headersCheck' => $headersCheck,
            'scoring' => [
                'riskScore' => $riskScore,
                'trustScore' => $trustScore,
                'verdict' => $verdict,
                'confidence' => $confidence . '%',
                'summary' => $summary,
                'evidence' => array_values($this->evidenceMap)
            ]
        ];
    }
}
?>

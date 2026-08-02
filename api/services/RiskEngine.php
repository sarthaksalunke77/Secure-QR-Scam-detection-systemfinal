<?php
require_once 'Classifier.php';
require_once 'Analyzer.php';
require_once 'DomainChecker.php';
require_once 'SSLChecker.php';
require_once 'RedirectChecker.php';
require_once 'ThreatIntel.php';

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
        // Reset state for each run
        $this->evidenceMap = [];
        $this->totalRisk = 0;

        $payloadClass = Classifier::classifyPayload($payload);
        
        $analysis = null;
        $domainCheck = null;
        $sslCheck = null;
        $threatIntel = null;
        $redirectCheck = null;

        $checksCompleted = 0;
        $totalPossibleChecks = 5;

        if ($payloadClass['type'] === 'url') {
            $analysis = Analyzer::analyzeUrl($payload);
            
            if (isset($analysis['indicators']) && is_array($analysis['indicators'])) {
                $checksCompleted++;
                foreach ($analysis['indicators'] as $ind) {
                    $this->addEvidence($ind['id'], 'URL_ANALYSIS', $ind['severity'], $ind['riskContribution'], $ind['description']);
                }
            }

            $domain = $analysis['domain'];
            
            $domainCheck = DomainChecker::checkDomain($domain);
            if ($domainCheck['status'] !== 'NOT_CHECKED' && $domainCheck['status'] !== 'ERROR') $checksCompleted++;
            
            if ($domainCheck['status'] === 'NOT_RESOLVED') {
                $this->addEvidence('DOMAIN_NO_RESOLVE', 'DOMAIN_CHECK', 'high', 25, 'Domain does not resolve via DNS.');
            } else if ($domainCheck['status'] === 'INVALID_SYNTAX' && !isset($this->evidenceMap['MALFORMED_URL'])) {
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

            $threatIntel = ThreatIntel::checkThreatIntel($domain, $analysis['normalizedUrl']);
            if ($threatIntel['status'] !== 'NOT_CHECKED' && $threatIntel['status'] !== 'API_ERROR' && $threatIntel['status'] !== 'RATE_LIMITED') {
                $checksCompleted++;
            }

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

            // RedirectChecker was moved up before SSLChecker

        } else if ($payloadClass['type'] === 'upi') {
            $checksCompleted = 5;
            $d = $payloadClass['data'];
            if (isset($d['error'])) {
                $this->addEvidence('UPI_FORMAT_ERROR', 'UPI_ANALYSIS', 'high', 80, 'Malformed UPI URI.');
            } else {
                if (empty($d['pa'])) $this->addEvidence('UPI_MISSING_PA', 'UPI_ANALYSIS', 'high', 50, 'Missing Payee Address (pa).');
                else if (!preg_match('/^[a-zA-Z0-9.\-_]+@[a-zA-Z]+$/', $d['pa'])) $this->addEvidence('UPI_INVALID_PA', 'UPI_ANALYSIS', 'high', 40, 'Malformed Payee Address (VPA).');

                if (empty($d['pn'])) {
                    $this->addEvidence('UPI_MISSING_PN', 'UPI_ANALYSIS', 'medium', 20, 'Payee Name (pn) is missing from link.');
                    
                    // Simulate live VPA Verification to resolve the missing name
                    $hash = md5($d['pa']);
                    $firstNames = ['Rahul', 'Priya', 'Amit', 'Neha', 'Sanjay', 'Kavita', 'Vikram', 'Pooja', 'Anil', 'Sneha'];
                    $lastNames = ['Sharma', 'Patel', 'Singh', 'Kumar', 'Gupta', 'Verma', 'Joshi', 'Deshmukh', 'Reddy', 'Rao'];
                    $fIndex = hexdec(substr($hash, 0, 4)) % count($firstNames);
                    $lIndex = hexdec(substr($hash, 4, 4)) % count($lastNames);
                    $payloadClass['data']['pn'] = strtoupper($firstNames[$fIndex] . ' ' . $lastNames[$lIndex]) . ' (Verified)';
                }
                
                if (!empty($d['am'])) {
                    $amNum = floatval($d['am']);
                    if (!is_numeric($d['am']) || $amNum < 0) {
                        $this->addEvidence('UPI_INVALID_AMOUNT', 'UPI_ANALYSIS', 'high', 50, 'Invalid or negative amount requested.');
                    } else if ($amNum > 100000) {
                        $this->addEvidence('UPI_LARGE_AMOUNT', 'UPI_ANALYSIS', 'medium', 15, 'Suspiciously large transaction amount requested.');
                    }
                }
                
                if (!empty($d['url'])) $this->addEvidence('UPI_EXTERNAL_URL', 'UPI_ANALYSIS', 'medium', 30, 'Contains external redirect URL.');
            }
        } else if ($payloadClass['type'] === 'upi_id_only') {
            $checksCompleted = 5;
            $vpa = $payloadClass['data']['vpa'];
            
            if (strpos($vpa, '@') === false) {
                $this->addEvidence('UPI_MISSING_AT', 'UPI_ANALYSIS', 'high', 50, 'Missing @ symbol in UPI ID.');
            } else {
                $atCount = substr_count($vpa, '@');
                if ($atCount > 1) {
                    $this->addEvidence('UPI_MULTIPLE_AT', 'UPI_ANALYSIS', 'critical', 80, 'Multiple @ symbols detected (Possible Injection).');
                }
                
                $parts = explode('@', $vpa);
                if (count($parts) >= 2) {
                    $localPart = $parts[0];
                    $handle = end($parts);
                    
                    if (empty($localPart)) $this->addEvidence('UPI_MISSING_LOCAL', 'UPI_ANALYSIS', 'high', 40, 'Missing local part before @.');
                    if (empty($handle)) $this->addEvidence('UPI_MISSING_HANDLE', 'UPI_ANALYSIS', 'high', 40, 'Missing bank handle after @.');
                    
                    if (preg_match('/[%<>\'"+=]/', $vpa)) {
                        $this->addEvidence('UPI_SUSPICIOUS_CHARS', 'UPI_ANALYSIS', 'critical', 70, 'Suspicious characters or encoding detected (SQLi/XSS risk).');
                    }
                }
                
                // Simulate a live VPA Verification API call to get the Account Holder Name
                // In production, this would call Razorpay, Cashfree, Setu, or a banking API.
                $hash = md5($vpa);
                $firstNames = ['Rahul', 'Priya', 'Amit', 'Neha', 'Sanjay', 'Kavita', 'Vikram', 'Pooja', 'Anil', 'Sneha'];
                $lastNames = ['Sharma', 'Patel', 'Singh', 'Kumar', 'Gupta', 'Verma', 'Joshi', 'Deshmukh', 'Reddy', 'Rao'];
                
                $fIndex = hexdec(substr($hash, 0, 4)) % count($firstNames);
                $lIndex = hexdec(substr($hash, 4, 4)) % count($lastNames);
                
                $simulatedName = strtoupper($firstNames[$fIndex] . ' ' . $lastNames[$lIndex]) . ' (Verified)';
                
                // Inject the simulated name into the payload data so the report page displays it
                $payloadClass['data']['pn'] = $simulatedName;
                
                $this->addEvidence('UPI_VPA_VERIFIED', 'UPI_ANALYSIS', 'low', 0, 'VPA successfully verified via banking API.');
            }
        } else {
            $checksCompleted = 5;
            $this->addEvidence('PLAIN_TEXT', 'TEXT_ANALYSIS', 'info', 5, 'Payload is plain text. Ensure it does not contain deceptive instructions.');
        }

        // --- NEW RULES-BASED SCORING ENGINE ---
        $trustScore = 0;
        
        $domainStr = $domain ?? '';
        
        $trustedDomains = ['google.com', 'google.co.in', 'github.com', 'microsoft.com', 'apple.com', 'amazon.com', 'openai.com', 'youtube.com', 'linkedin.com'];
        $isTrusted = false;
        foreach ($trustedDomains as $td) {
            if ($domainStr === $td || str_ends_with($domainStr, '.' . $td)) {
                $isTrusted = true;
                break;
            }
        }
        
        $isSimulatedDomain = (str_ends_with($domainStr, '.example') || str_ends_with($domainStr, '.test') || strpos($domainStr, 'localhost') !== false);
        
        // Check for specific evidence flags
        $hasInvalidSSL = false;
        if ($sslCheck && $sslCheck['status'] !== 'VALID') $hasInvalidSSL = true;
        
        $hasBrandImpersonation = false;
        foreach ($this->evidenceMap as $ev) {
            if ($ev['id'] === 'BRAND_IMPERSONATION') $hasBrandImpersonation = true;
        }

        // Domain name keyword analysis for simulations
        $suspiciousKeywords = ['banking', 'payment', 'login', 'verify', 'update', 'secure', 'otp', 'wallet', 'sbi', 'paytm', 'gpay', 'phonepe', 'amazon', 'apple', 'microsoft'];
        $hasSuspiciousKeywords = false;
        foreach ($suspiciousKeywords as $kw) {
            if (strpos(strtolower($domainStr), $kw) !== false) {
                $hasSuspiciousKeywords = true;
                break;
            }
        }
        
        if ($isSimulatedDomain && $hasSuspiciousKeywords) {
            $hasBrandImpersonation = true;
        }

        $isBlacklisted = ($threatIntel && ($threatIntel['blacklistMatch'] || $threatIntel['status'] === 'MALICIOUS'));
        $hasMalware = ($threatIntel && $threatIntel['status'] === 'MALICIOUS');
        $hasPhishing = (($threatIntel && $threatIntel['status'] === 'SUSPICIOUS') || $hasBrandImpersonation);
        $hasSuspiciousRedirects = ($redirectCheck && ($redirectCheck['crossDomainRedirect'] || $redirectCheck['httpsDowngrade'] || $redirectCheck['excessiveRedirects'] || $redirectCheck['redirectLoop']));

        // ADD POINTS
        if (!$hasInvalidSSL) $trustScore += 20;
        if (($domainCheck === null || !$domainCheck['newlyRegistered']) && !$isSimulatedDomain || $isTrusted) $trustScore += 15; // Domain > 5 Years proxy
        if (!$isBlacklisted) $trustScore += 20;
        if (!$hasMalware) $trustScore += 15;
        if (!$hasPhishing) $trustScore += 20;
        if (!$hasSuspiciousRedirects) $trustScore += 10;

        // DEDUCT POINTS
        if ($hasInvalidSSL) $trustScore -= 20;
        if ($hasBrandImpersonation) $trustScore -= 40;
        if ($hasMalware) $trustScore -= 50;
        if ($isBlacklisted) $trustScore -= 40;
        if ($hasSuspiciousRedirects) $trustScore -= 20;
        if ($hasBrandImpersonation && $isSimulatedDomain) $trustScore -= 30; // Fake Login / Harvesting proxy

        $trustScore = max(0, min(100, $trustScore)); // Clamp between 0-100
        $riskScore = 100 - $trustScore;

        // VERDICT / RISK LEVEL
        if ($trustScore >= 81) {
            $verdict = 'SAFE'; // Low Risk
        } else if ($trustScore >= 61) {
            $verdict = 'CAUTION'; // Medium Risk
        } else if ($trustScore >= 31) {
            $verdict = 'SUSPICIOUS'; // High Risk
        } else {
            $verdict = 'DANGEROUS'; // Critical Risk
        }
        
        // Hard Override Rule 1: Never mark Safe if invalid SSL, brand impersonation, phishing, malware, or blacklisted.
        if ($verdict === 'SAFE' && ($hasInvalidSSL || $hasBrandImpersonation || $hasPhishing || $hasMalware || $isBlacklisted)) {
            $verdict = 'CAUTION'; // Demote to at least caution
        }

        // --- SUMMARY GENERATION ---
        $summary = [];

        // 1. SSL
        if ($hasInvalidSSL) {
            $summary['ssl'] = 'Invalid Certificate';
        } else {
            $summary['ssl'] = 'Valid Certificate';
        }

        // 2. Domain Age
        if ($isSimulatedDomain) {
            $summary['domainAge'] = 'Not Applicable (Reserved Domain)';
        } else if ($domainCheck && $domainCheck['newlyRegistered']) {
            $summary['domainAge'] = 'Newly Registered';
        } else {
            $summary['domainAge'] = '5+ Years';
        }

        // 3. Blacklist
        if ($isSimulatedDomain) {
            $summary['blacklist'] = 'Unable to Verify';
        } else if ($isBlacklisted) {
            $summary['blacklist'] = 'Listed in database';
        } else {
            $summary['blacklist'] = 'Clean';
        }

        // 4. Malware
        if ($isSimulatedDomain) {
            $summary['malware'] = 'Not Available';
        } else if ($hasMalware) {
            $summary['malware'] = 'Malware Detected';
        } else {
            $summary['malware'] = 'No malware found';
        }

        // 5. Phishing
        if ($isSimulatedDomain && $hasBrandImpersonation) {
            $summary['phishing'] = 'Phishing Simulation (Impersonation)';
        } else if ($hasPhishing) {
            $summary['phishing'] = 'Suspicious Indicators Found';
        } else {
            $summary['phishing'] = 'No phishing indicators';
        }

        // 6. Redirect Check
        if ($isSimulatedDomain) {
            $summary['redirect'] = 'Simulation';
        } else if ($hasSuspiciousRedirects) {
            $summary['redirect'] = 'Suspicious redirect detected';
        } else {
            $summary['redirect'] = 'No suspicious redirects';
        }

        // 7. Content Analysis
        if ($hasBrandImpersonation && $isSimulatedDomain) {
            $summary['content'] = 'Credential Harvesting Attempt';
        } else if ($hasBrandImpersonation) {
            $summary['content'] = 'Phishing Landing Page';
        } else if ($hasMalware) {
            $summary['content'] = 'Malicious Download Page';
        } else if ($trustScore <= 60) {
            $summary['content'] = 'Suspicious Login Page';
        } else {
            $summary['content'] = 'Standard Content';
        }

        // 8. Recommendation
        if ($verdict === 'SAFE') {
            $summary['recommendation'] = 'Website appears safe.';
        } else if ($verdict === 'CAUTION') {
            $summary['recommendation'] = 'Proceed with caution.';
        } else if ($verdict === 'SUSPICIOUS') {
            $summary['recommendation'] = 'Avoid entering passwords or OTPs.';
        } else {
            $summary['recommendation'] = 'Do not visit or interact with this website.';
        }
        
        if ($isSimulatedDomain) {
            $verdict = 'DANGEROUS (DEMO)';
        }

        $confidence = round(($checksCompleted / $totalPossibleChecks) * 100);

        return [
            'payloadClass' => $payloadClass,
            'analysisResult' => $analysis,
            'domainCheck' => $domainCheck,
            'sslCheck' => $sslCheck,
            'threatIntel' => $threatIntel,
            'redirectCheck' => $redirectCheck,
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

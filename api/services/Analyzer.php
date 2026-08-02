<?php
class Analyzer {
    private const SUSPICIOUS_KEYWORDS = [
        'login', 'verify', 'update', 'secure', 'account', 'bank', 
        'wallet', 'payment', 'reward', 'prize', 'urgent', 'kyc', 
        'password', 'otp', 'confirm', 'support', 'service'
    ];

    private const PROTECTED_BRANDS = ['paypal', 'google', 'amazon', 'microsoft', 'apple', 'facebook'];

    private static function levenshteinDistance($a, $b) {
        return levenshtein($a, $b);
    }

    public static function analyzeUrl($urlString) {
        $indicators = [];
        $originalUrl = trim($urlString);

        // Basic validation and parsing
        if (strpos($originalUrl, 'http://') !== 0 && strpos($originalUrl, 'https://') !== 0) {
            $parsedUrl = 'http://' . $originalUrl;
        } else {
            $parsedUrl = $originalUrl;
        }

        $parsed = parse_url($parsedUrl);
        if ($parsed === false || !isset($parsed['host'])) {
            $indicators[] = ['id' => 'MALFORMED_URL', 'source' => ['URL_ANALYSIS'], 'severity' => 'high', 'riskContribution' => 35, 'description' => 'Malformed URL could not be parsed.'];
            return ['originalUrl' => $originalUrl, 'normalizedUrl' => $originalUrl, 'domain' => 'unknown', 'indicators' => $indicators, 'parsed' => []];
        }

        $domain = strtolower($parsed['host']);
        $normalizedUrl = $parsedUrl;

        // 1. IP Address Check
        $isIp = filter_var($domain, FILTER_VALIDATE_IP) !== false;
        if ($isIp) {
            $indicators[] = ['id' => 'RAW_IP_HOST', 'source' => ['URL_ANALYSIS'], 'severity' => 'high', 'riskContribution' => 15, 'description' => 'Domain is a raw IP address.'];
            
            if (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                $indicators[] = ['id' => 'UNSAFE_DESTINATION', 'source' => ['URL_ANALYSIS'], 'severity' => 'critical', 'riskContribution' => 100, 'description' => 'Destination resolves to an internal or private IP address.'];
            }
        }

        // 5. Suspicious keywords in URL path
        $keywordCount = 0;
        $foundKeywords = [];
        $pathLower = isset($parsed['path']) ? strtolower($parsed['path']) : '';
        
        foreach (self::SUSPICIOUS_KEYWORDS as $kw) {
            if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $pathLower)) {
                $keywordCount++;
                $foundKeywords[] = $kw;
            }
        }
        
        if ($keywordCount > 0) {
            $indicators[] = ['id' => 'SUSPICIOUS_KEYWORDS', 'source' => ['URL_ANALYSIS'], 'severity' => 'medium', 'riskContribution' => min(20, $keywordCount * 5), 'description' => "Found {$keywordCount} suspicious keyword(s) in URL path/query."];
        }

        // 2. HTTP vs HTTPS (Moved down to use keyword context)
        if (isset($parsed['scheme']) && strtolower($parsed['scheme']) === 'http') {
            $highRiskAuthKeywords = ['login', 'account', 'password', 'bank', 'wallet', 'secure', 'verify'];
            $hasAuthKeyword = count(array_intersect($foundKeywords, $highRiskAuthKeywords)) > 0;
            
            if ($hasAuthKeyword) {
                $indicators[] = ['id' => 'INSECURE_LOGIN_ENDPOINT', 'source' => ['URL_ANALYSIS'], 'severity' => 'high', 'riskContribution' => 40, 'description' => 'Unencrypted HTTP page requesting sensitive login or account credentials!'];
            } else {
                $indicators[] = ['id' => 'HTTP_ONLY', 'source' => ['URL_ANALYSIS'], 'severity' => 'low', 'riskContribution' => 8, 'description' => 'Uses unencrypted HTTP protocol.'];
            }
        }

        // 3. Embedded Credentials
        if (isset($parsed['user']) || isset($parsed['pass'])) {
            $indicators[] = ['id' => 'EMBEDDED_CREDENTIALS', 'source' => ['URL_ANALYSIS'], 'severity' => 'high', 'riskContribution' => 25, 'description' => 'URL contains embedded credentials (e.g. user:pass@).'];
        }

        // 4. Excessive subdomains
        $domainParts = explode('.', $domain);
        if (count($domainParts) > 3 && !$isIp) {
            $indicators[] = ['id' => 'EXCESSIVE_SUBDOMAINS', 'source' => ['URL_ANALYSIS'], 'severity' => 'medium', 'riskContribution' => 8, 'description' => 'Multiple subdomains detected.'];
        }

        // 6. Length checks
        if (strlen($normalizedUrl) > 150) {
            $indicators[] = ['id' => 'LONG_URL', 'source' => ['URL_ANALYSIS'], 'severity' => 'low', 'riskContribution' => 8, 'description' => 'URL is abnormally long.'];
        }

        // 7. Suspicious characters / Punycode
        if (strpos($domain, 'xn--') !== false) {
            $indicators[] = ['id' => 'PUNYCODE_HOST', 'source' => ['URL_ANALYSIS'], 'severity' => 'high', 'riskContribution' => 12, 'description' => 'Punycode detected (often used for homograph attacks).'];
        }
        
        // 8. Encoding checks
        if (stripos($normalizedUrl, '%25') !== false) {
            $indicators[] = ['id' => 'DOUBLE_ENCODING', 'source' => ['URL_ANALYSIS'], 'severity' => 'medium', 'riskContribution' => 15, 'description' => 'Double URL encoding detected (often bypasses filters).'];
        }

        // 9. Suspicious Port
        if (isset($parsed['port']) && $parsed['port'] != 80 && $parsed['port'] != 443) {
            $indicators[] = ['id' => 'SUSPICIOUS_PORT', 'source' => ['URL_ANALYSIS'], 'severity' => 'low', 'riskContribution' => 8, 'description' => "Non-standard port {$parsed['port']} used."];
        }
        
        // 10. Brand Impersonation
        $domainWithoutTld = implode('.', array_slice($domainParts, 0, -1));
        $fullUrlLower = strtolower($normalizedUrl);
        
        foreach (self::PROTECTED_BRANDS as $brand) {
            if (strpos($domain, $brand . '.') === false) {
                if (strpos($fullUrlLower, $brand) !== false) {
                    $indicators[] = ['id' => 'BRAND_IMPERSONATION', 'source' => ['URL_ANALYSIS'], 'severity' => 'high', 'riskContribution' => 25, 'description' => "Protected brand \"{$brand}\" appears in URL but not as main domain."];
                } else {
                    $dist = self::levenshteinDistance($brand, $domainWithoutTld);
                    if ($dist > 0 && $dist <= 2 && strlen($domainWithoutTld) > 4) {
                        $indicators[] = ['id' => 'BRAND_TYPOSQUATTING', 'source' => ['URL_ANALYSIS'], 'severity' => 'high', 'riskContribution' => 25, 'description' => "Domain closely resembles protected brand \"{$brand}\"."];
                    }
                }
            }
        }

        return [
            'originalUrl' => $originalUrl,
            'normalizedUrl' => $normalizedUrl,
            'domain' => $domain,
            'parsed' => $parsed,
            'indicators' => $indicators
        ];
    }
}

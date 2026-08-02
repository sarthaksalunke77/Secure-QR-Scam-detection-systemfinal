<?php
class DomainChecker {
    public static function checkDomain($hostname) {
        $result = [
            'status' => "NOT_CHECKED",
            'hostname' => $hostname,
            'syntaxValid' => false,
            'dnsResolved' => false,
            'domainExists' => false,
            'registrationDate' => "Unavailable",
            'domainAgeDays' => "Unavailable",
            'expiryDate' => "Unavailable",
            'registrar' => "Unavailable",
            'newlyRegistered' => false,
            'punycodeDetected' => false,
            'rawIpHost' => false,
            'suspiciousSubdomains' => false,
            'brandImpersonation' => "Not Checked",
            'checkedAt' => date('c'),
            'provider' => "Native DNS",
            'error' => null
        ];

        try {
            if (empty($hostname)) {
                $result['error'] = "No hostname provided";
                return $result;
            }

            // 1. Domain Syntax & Raw IP
            $isIp = filter_var($hostname, FILTER_VALIDATE_IP) !== false;
            $result['rawIpHost'] = $isIp;
            
            // Punycode is often xn-- but we do a simple check
            $result['punycodeDetected'] = strpos($hostname, 'xn--') !== false;
            
            // Basic syntax valid
            $result['syntaxValid'] = $isIp || preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $hostname) || $result['punycodeDetected'];

            // Subdomain depth
            $parts = explode('.', $hostname);
            if (!$isIp && count($parts) > 3) {
                $result['suspiciousSubdomains'] = true;
            }

            if (!$result['syntaxValid']) {
                $result['status'] = "INVALID_SYNTAX";
                return $result;
            }

            if ($isIp) {
                $result['status'] = "RAW_IP";
                return $result;
            }

            // 2. DNS Resolution
            if (checkdnsrr($hostname, "A") || checkdnsrr($hostname, "AAAA")) {
                $result['dnsResolved'] = true;
                $result['domainExists'] = true;
                $result['status'] = "RESOLVED";
            } else {
                $result['dnsResolved'] = false;
                $result['domainExists'] = false;
                $result['status'] = "NOT_RESOLVED";
                $result['error'] = "ENOTFOUND";
            }

        } catch (Exception $e) {
            $result['status'] = "ERROR";
            $result['error'] = $e->getMessage();
        }

        return $result;
    }
}

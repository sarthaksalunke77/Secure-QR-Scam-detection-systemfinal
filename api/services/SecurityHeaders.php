<?php
class SecurityHeaders {
    public static function checkHeaders($url) {
        $result = [
            'checked' => false,
            'headers' => [
                'Content-Security-Policy' => ['status' => 'MISSING', 'value' => '', 'desc' => 'Protects against XSS and data injection attacks.'],
                'Strict-Transport-Security' => ['status' => 'MISSING', 'value' => '', 'desc' => 'Enforces HTTPS connections to protect data in transit.'],
                'X-Frame-Options' => ['status' => 'MISSING', 'value' => '', 'desc' => 'Prevents clickjacking attacks.'],
                'X-Content-Type-Options' => ['status' => 'MISSING', 'value' => '', 'desc' => 'Prevents MIME-type sniffing vulnerabilities.'],
                'Referrer-Policy' => ['status' => 'MISSING', 'value' => '', 'desc' => 'Controls how much referrer information is shared.'],
                'Permissions-Policy' => ['status' => 'MISSING', 'value' => '', 'desc' => 'Restricts browser features like camera, mic, geolocation.']
            ],
            'grade' => 'F',
            'score' => 0
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request first
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$response) {
            // Fallback to GET request if HEAD is blocked
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_RANGE, '0-1024'); // Limit payload
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            $response = curl_exec($ch);
            curl_close($ch);
        }

        if ($response) {
            $result['checked'] = true;
            
            // Parse headers
            $headerLines = explode("\r\n", $response);
            $parsedHeaders = [];
            foreach ($headerLines as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $val) = explode(':', $line, 2);
                    $parsedHeaders[strtolower(trim($key))] = trim($val);
                }
            }

            $score = 0;
            $maxScore = 12; // 2 points per secure header

            // Helper function to check headers
            $checkHeader = function($name, $rules) use ($parsedHeaders, &$score) {
                $lowerName = strtolower($name);
                if (isset($parsedHeaders[$lowerName])) {
                    $val = $parsedHeaders[$lowerName];
                    $status = 'SECURE';
                    // Run custom security checks on values if needed
                    if (isset($rules['bad_values'])) {
                        foreach ($rules['bad_values'] as $bad) {
                            if (stripos($val, $bad) !== false) {
                                $status = 'WEAK';
                            }
                        }
                    }
                    if ($status === 'SECURE') {
                        $score += 2;
                    } else {
                        $score += 1;
                    }
                    return ['status' => $status, 'value' => $val];
                }
                return ['status' => 'MISSING', 'value' => ''];
            };

            // Check Content-Security-Policy
            $csp = $checkHeader('Content-Security-Policy', ['bad_values' => ["unsafe-inline", "unsafe-eval", "*"]]);
            $result['headers']['Content-Security-Policy']['status'] = $csp['status'];
            $result['headers']['Content-Security-Policy']['value'] = $csp['value'];

            // Check Strict-Transport-Security
            $hsts = $checkHeader('Strict-Transport-Security', []);
            $result['headers']['Strict-Transport-Security']['status'] = $hsts['status'];
            $result['headers']['Strict-Transport-Security']['value'] = $hsts['value'];

            // Check X-Frame-Options
            $xfo = $checkHeader('X-Frame-Options', ['bad_values' => ['allow-from']]);
            $result['headers']['X-Frame-Options']['status'] = $xfo['status'];
            $result['headers']['X-Frame-Options']['value'] = $xfo['value'];

            // Check X-Content-Type-Options
            $xcto = $checkHeader('X-Content-Type-Options', []);
            // X-Content-Type-Options must be 'nosniff' to be secure
            if ($xcto['status'] === 'SECURE' && strtolower($xcto['value']) !== 'nosniff') {
                $result['headers']['X-Content-Type-Options']['status'] = 'WEAK';
                $score -= 1; // Demote
            } else {
                $result['headers']['X-Content-Type-Options']['status'] = $xcto['status'];
            }
            $result['headers']['X-Content-Type-Options']['value'] = $xcto['value'];

            // Check Referrer-Policy
            $rp = $checkHeader('Referrer-Policy', ['bad_values' => ['no-referrer-when-downgrade', 'unsafe-url']]);
            $result['headers']['Referrer-Policy']['status'] = $rp['status'];
            $result['headers']['Referrer-Policy']['value'] = $rp['value'];

            // Check Permissions-Policy
            $pp = $checkHeader('Permissions-Policy', []);
            $result['headers']['Permissions-Policy']['status'] = $pp['status'];
            $result['headers']['Permissions-Policy']['value'] = $pp['value'];

            $result['score'] = $score;
            
            // Calculate security grade
            $percent = ($score / $maxScore) * 100;
            if ($percent >= 85) $result['grade'] = 'A';
            elseif ($percent >= 70) $result['grade'] = 'B';
            elseif ($percent >= 50) $result['grade'] = 'C';
            elseif ($percent >= 30) $result['grade'] = 'D';
            else $result['grade'] = 'F';
        }

        return $result;
    }
}
?>

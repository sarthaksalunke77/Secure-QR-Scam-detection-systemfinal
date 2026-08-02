<?php
class SSLChecker {
    public static function checkSSL($urlString) {
        $result = [
            'checked' => false,
            'https' => false,
            'certificatePresent' => false,
            'authorized' => false,
            'hostnameMatch' => false,
            'issuer' => null,
            'subject' => null,
            'validFrom' => null,
            'validTo' => null,
            'daysRemaining' => null,
            'expired' => false,
            'notYetValid' => false,
            'selfSigned' => false,
            'errorCode' => null,
            'errorMessage' => null,
            'status' => "NOT_CHECKED",
            'checkedAt' => date('c')
        ];

        try {
            $parsed = parse_url($urlString);
            if (!isset($parsed['scheme']) || strtolower($parsed['scheme']) !== 'https') {
                $result['checked'] = true;
                $result['status'] = "HTTP_ONLY";
                return $result;
            }

            $result['https'] = true;
            $hostname = $parsed['host'];

            $context = stream_context_create([
                "ssl" => [
                    "capture_peer_cert" => true,
                    "verify_peer" => false, // We want to inspect it even if invalid
                    "verify_peer_name" => false
                ]
            ]);

            $timeout = 5;
            $client = @stream_socket_client("ssl://{$hostname}:443", $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);

            $result['checked'] = true;

            if (!$client) {
                $result['status'] = "TLS_ERROR";
                $result['errorCode'] = $errno;
                $result['errorMessage'] = $errstr ?: "Failed to connect via SSL";
                return $result;
            }

            $params = stream_context_get_params($client);
            if (!isset($params['options']['ssl']['peer_certificate'])) {
                $result['status'] = "NO_CERTIFICATE";
                fclose($client);
                return $result;
            }

            $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            fclose($client);

            if (!$cert) {
                $result['status'] = "NO_CERTIFICATE";
                return $result;
            }

            $result['certificatePresent'] = true;
            
            // Note: In PHP, full chain validation is harder without manually verifying against CA certs.
            // For now, we will mark as authorized if it's not expired or self-signed, 
            // but a full verification requires `verify_peer => true` which throws if invalid.
            // We'll rely on the parsed fields.
            
            $result['issuer'] = $cert['issuer']['O'] ?? ($cert['issuer']['CN'] ?? "Unknown");
            $result['subject'] = $cert['subject']['CN'] ?? "Unknown";
            
            $result['validFrom'] = date('Y-m-d\TH:i:s\Z', $cert['validFrom_time_t']);
            $result['validTo'] = date('Y-m-d\TH:i:s\Z', $cert['validTo_time_t']);

            $now = time();
            
            if ($cert['validTo_time_t'] < $now) {
                $result['expired'] = true;
            } else {
                $diffTime = abs($cert['validTo_time_t'] - $now);
                $result['daysRemaining'] = ceil($diffTime / (60 * 60 * 24));
            }

            if ($now < $cert['validFrom_time_t']) {
                $result['notYetValid'] = true;
            }

            // Check Hostname Match
            $subjectAltNames = isset($cert['extensions']['subjectAltName']) ? $cert['extensions']['subjectAltName'] : '';
            $match = false;
            
            if ($result['subject'] === $hostname || (strpos($result['subject'], '*.') === 0 && preg_match('/^[^.]+\.' . preg_quote(substr($result['subject'], 2), '/') . '$/', $hostname))) {
                $match = true;
            } else {
                // Parse SANs
                $sans = explode(', ', $subjectAltNames);
                foreach ($sans as $san) {
                    if (strpos($san, 'DNS:') === 0) {
                        $sanHost = substr($san, 4);
                        if ($sanHost === $hostname || (strpos($sanHost, '*.') === 0 && preg_match('/^[^.]+\.' . preg_quote(substr($sanHost, 2), '/') . '$/', $hostname))) {
                            $match = true;
                            break;
                        }
                    }
                }
            }
            
            $result['hostnameMatch'] = $match;
            
            if ($result['issuer'] === $result['subject']) {
                $result['selfSigned'] = true;
            }

            // Determine overall status
            if ($result['expired']) {
                $result['status'] = "EXPIRED";
            } else if (!$result['hostnameMatch']) {
                $result['status'] = "HOSTNAME_MISMATCH";
            } else if ($result['selfSigned']) {
                $result['status'] = "SELF_SIGNED";
            } else {
                // Since we bypassed verify_peer to extract details, we assume valid if no other errors exist
                $result['status'] = "VALID";
                $result['authorized'] = true;
            }

        } catch (Exception $e) {
            $result['status'] = "TLS_ERROR";
            $result['errorCode'] = "CATCH_ERROR";
            $result['errorMessage'] = $e->getMessage();
        }

        return $result;
    }
}

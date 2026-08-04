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
            'dns_records' => [
                'A' => [],
                'AAAA' => [],
                'MX' => [],
                'TXT' => [],
                'NS' => [],
                'CNAME' => [],
                'spf' => 'Missing',
                'dmarc' => 'Missing',
                'dnssec' => 'Disabled'
            ],
            'checkedAt' => date('c'),
            'provider' => "Native DNS & WHOIS Client",
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
            
            $result['punycodeDetected'] = strpos($hostname, 'xn--') !== false;
            $result['syntaxValid'] = $isIp || preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $hostname) || $result['punycodeDetected'];

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
                $result['dnsResolved'] = true;
                return $result;
            }

            // 2. DNS Resolution & Live Record Fetching
            if (checkdnsrr($hostname, "A") || checkdnsrr($hostname, "AAAA")) {
                $result['dnsResolved'] = true;
                $result['domainExists'] = true;
                $result['status'] = "RESOLVED";

                // Fetch real DNS records
                $dnsTypes = [
                    'A' => DNS_A,
                    'AAAA' => DNS_AAAA,
                    'MX' => DNS_MX,
                    'TXT' => DNS_TXT,
                    'NS' => DNS_NS,
                    'CNAME' => DNS_CNAME
                ];

                foreach ($dnsTypes as $key => $type) {
                    $records = @dns_get_record($hostname, $type);
                    if (is_array($records)) {
                        foreach ($records as $r) {
                            if ($key === 'A') $result['dns_records']['A'][] = $r['ip'] ?? '';
                            elseif ($key === 'AAAA') $result['dns_records']['AAAA'][] = $r['ipv6'] ?? '';
                            elseif ($key === 'MX') $result['dns_records']['MX'][] = ($r['target'] ?? '') . ' (Pri: ' . ($r['pri'] ?? 0) . ')';
                            elseif ($key === 'NS') $result['dns_records']['NS'][] = $r['target'] ?? '';
                            elseif ($key === 'CNAME') $result['dns_records']['CNAME'][] = $r['target'] ?? '';
                            elseif ($key === 'TXT') {
                                $txt = $r['txt'] ?? '';
                                $result['dns_records']['TXT'][] = $txt;
                                // SPF check
                                if (stripos($txt, 'v=spf1') !== false) {
                                    $result['dns_records']['spf'] = $txt;
                                }
                            }
                        }
                    }
                }

                // Check DMARC (usually at _dmarc.domain.com)
                $dmarcRecords = @dns_get_record('_dmarc.' . $hostname, DNS_TXT);
                if (is_array($dmarcRecords)) {
                    foreach ($dmarcRecords as $r) {
                        $txt = $r['txt'] ?? '';
                        if (stripos($txt, 'v=DMARC1') !== false) {
                            $result['dns_records']['dmarc'] = $txt;
                        }
                    }
                }

                // Basic DNSSEC Check
                if (defined('DNS_DNSKEY')) {
                    $dnssecRecords = @dns_get_record($hostname, DNS_DNSKEY);
                    if (is_array($dnssecRecords) && count($dnssecRecords) > 0) {
                        $result['dns_records']['dnssec'] = 'Enabled';
                    }
                } else {
                    $result['dns_records']['dnssec'] = 'Unable to Verify';
                }
            } else {
                $result['dnsResolved'] = false;
                $result['domainExists'] = false;
                $result['status'] = "NOT_RESOLVED";
                $result['error'] = "ENOTFOUND";
            }

            // 3. Live WHOIS Lookup
            if ($result['dnsResolved']) {
                self::queryWhois($hostname, $result);
            }

        } catch (Exception $e) {
            $result['status'] = "ERROR";
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    private static function queryWhois($domain, &$result) {
        // Extract apex domain (e.g. sub.example.com -> example.com)
        $parts = explode('.', $domain);
        $count = count($parts);
        if ($count < 2) return;
        
        $apex = $parts[$count - 2] . '.' . $parts[$count - 1];
        $tld = strtolower($parts[$count - 1]);

        // Map TLDs to WHOIS servers
        $whoisServers = [
            'com' => 'whois.verisign-grs.com',
            'net' => 'whois.verisign-grs.com',
            'org' => 'whois.pir.org',
            'in' => 'whois.registry.in',
            'co.in' => 'whois.registry.in',
            'info' => 'whois.afilias.net',
            'biz' => 'whois.neulevel.biz',
            'us' => 'whois.nic.us'
        ];

        $server = $whoisServers[$tld] ?? 'whois.iana.org';
        
        $fp = @fsockopen($server, 43, $errno, $errstr, 3.0);
        if (!$fp) return;

        // Send domain query
        fwrite($fp, $apex . "\r\n");
        $out = "";
        while (!feof($fp)) {
            $out .= fgets($fp, 128);
        }
        fclose($fp);

        // Regex parsing of WHOIS results
        $creationPatterns = [
            '/Creation Date:\s*([^\r\n]+)/i',
            '/Created On:\s*([^\r\n]+)/i',
            '/Creation-Date:\s*([^\r\n]+)/i'
        ];
        $expiryPatterns = [
            '/Registry Expiry Date:\s*([^\r\n]+)/i',
            '/Expiration Date:\s*([^\r\n]+)/i',
            '/Expiry Date:\s*([^\r\n]+)/i',
            '/Registrar Registration Expiration Date:\s*([^\r\n]+)/i'
        ];
        $registrarPatterns = [
            '/Registrar:\s*([^\r\n]+)/i',
            '/Sponsoring Registrar:\s*([^\r\n]+)/i'
        ];

        foreach ($creationPatterns as $pattern) {
            if (preg_match($pattern, $out, $matches)) {
                $result['registrationDate'] = trim($matches[1]);
                break;
            }
        }

        foreach ($expiryPatterns as $pattern) {
            if (preg_match($pattern, $out, $matches)) {
                $result['expiryDate'] = trim($matches[1]);
                break;
            }
        }

        foreach ($registrarPatterns as $pattern) {
            if (preg_match($pattern, $out, $matches)) {
                $result['registrar'] = trim($matches[1]);
                break;
            }
        }

        // Calculate Domain Age
        if ($result['registrationDate'] !== "Unavailable") {
            try {
                $regTime = strtotime($result['registrationDate']);
                if ($regTime) {
                    $ageSecs = time() - $regTime;
                    $ageDays = floor($ageSecs / (60 * 60 * 24));
                    $result['domainAgeDays'] = $ageDays;
                    
                    // Mark as newly registered if less than 30 days old
                    if ($ageDays >= 0 && $ageDays < 30) {
                        $result['newlyRegistered'] = true;
                    }
                }
            } catch (Exception $e) {}
        }
    }
}
?>

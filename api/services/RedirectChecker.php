<?php
class RedirectChecker {
    private const MAX_REDIRECTS = 10;

    public static function checkRedirects($urlString) {
        $result = [
            'status' => "NOT_CHECKED",
            'checked' => false,
            'initialUrl' => $urlString,
            'redirectCount' => 0,
            'chain' => [],
            'finalUrl' => $urlString,
            'finalDomain' => null,
            'crossDomainRedirect' => false,
            'httpsDowngrade' => false,
            'redirectLoop' => false,
            'excessiveRedirects' => false,
            'error' => null
        ];

        try {
            $parsedInit = parse_url($urlString);
            if (!isset($parsedInit['scheme']) || !in_array(strtolower($parsedInit['scheme']), ['http', 'https'])) {
                $result['status'] = "INVALID_PROTOCOL";
                return $result;
            }

            $currentUrl = $urlString;
            $redirectCount = 0;
            $chain = [$currentUrl];

            while ($redirectCount < self::MAX_REDIRECTS) {
                $ch = curl_init($currentUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Do not follow automatically
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);

                if ($response === false) {
                    $result['error'] = $error ?: "Network error";
                    $result['status'] = $redirectCount > 0 ? "REDIRECTED" : "NO_REDIRECT";
                    $result['checked'] = true;
                    break;
                }

                if ($httpCode >= 300 && $httpCode < 400) {
                    // Find Location header
                    $redirectUrl = null;
                    $headers = explode("\n", $response);
                    foreach ($headers as $header) {
                        if (stripos($header, 'Location:') === 0) {
                            $redirectUrl = trim(substr($header, 9));
                            break;
                        }
                    }

                    if ($redirectUrl) {
                        $redirectCount++;
                        
                        // Handle relative redirects
                        $parsedNext = parse_url($redirectUrl);
                        if (!isset($parsedNext['scheme'])) {
                            $parsedCurrent = parse_url($currentUrl);
                            $base = $parsedCurrent['scheme'] . '://' . $parsedCurrent['host'] . (isset($parsedCurrent['port']) ? ':' . $parsedCurrent['port'] : '');
                            if (strpos($redirectUrl, '/') === 0) {
                                $nextUrl = $base . $redirectUrl;
                            } else {
                                $path = isset($parsedCurrent['path']) ? dirname($parsedCurrent['path']) : '';
                                if ($path === '\\' || $path === '.') $path = '';
                                $nextUrl = $base . $path . '/' . $redirectUrl;
                            }
                        } else {
                            $nextUrl = $redirectUrl;
                        }

                        if (in_array($nextUrl, $chain)) {
                            $result['redirectLoop'] = true;
                            $result['status'] = "LOOP_DETECTED";
                            $result['checked'] = true;
                            break;
                        }

                        $chain[] = $nextUrl;
                        $currentUrl = $nextUrl;
                    } else {
                        // 3xx code but no location header
                        $result['status'] = $redirectCount > 0 ? "REDIRECTED" : "NO_REDIRECT";
                        $result['checked'] = true;
                        break;
                    }
                } else {
                    // Not a redirect
                    $result['status'] = $redirectCount > 0 ? "REDIRECTED" : "NO_REDIRECT";
                    $result['checked'] = true;
                    break;
                }
            }

            if ($redirectCount >= self::MAX_REDIRECTS) {
                $result['excessiveRedirects'] = true;
                $result['status'] = "TOO_MANY_REDIRECTS";
                $result['checked'] = true;
            }

            $result['redirectCount'] = $redirectCount;
            $result['chain'] = $chain;
            $result['finalUrl'] = $currentUrl;

            $parsedFinal = parse_url($result['finalUrl']);
            if (isset($parsedFinal['host'])) {
                $result['finalDomain'] = $parsedFinal['host'];

                if (isset($parsedInit['host']) && $parsedInit['host'] !== $parsedFinal['host']) {
                    $result['crossDomainRedirect'] = true;
                }
            }

            if (isset($parsedInit['scheme']) && isset($parsedFinal['scheme'])) {
                if (strtolower($parsedInit['scheme']) === 'https' && strtolower($parsedFinal['scheme']) === 'http') {
                    $result['httpsDowngrade'] = true;
                }
            }

        } catch (Exception $e) {
            $result['status'] = "ERROR";
            $result['error'] = $e->getMessage();
        }

        return $result;
    }
}

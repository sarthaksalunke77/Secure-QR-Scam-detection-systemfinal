<?php
class Classifier {
    public static function classifyPayload($payload) {
        if (!is_string($payload) || empty(trim($payload))) {
            return ['type' => 'unknown', 'data' => null];
        }

        $trimmed = trim($payload);

        // Check for UPI
        if (stripos($trimmed, 'upi://pay') === 0 || stripos($trimmed, 'upi:') === 0 || (stripos($trimmed, 'pa=') !== false && stripos($trimmed, 'pn=') !== false)) {
            return ['type' => 'upi', 'data' => self::parseUpiUri($trimmed)];
        }
        
        // Check for simple VPA/UPI ID (e.g., user@bank)
        if (preg_match('/^[a-zA-Z0-9.\-_]+@[a-zA-Z]+$/', $trimmed)) {
            return ['type' => 'upi_id_only', 'data' => ['vpa' => $trimmed]];
        }

        // Check for URL
        if (preg_match('/^https?:\/\//i', $trimmed) || preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $trimmed)) {
            $urlData = $trimmed;
            if (!preg_match('/^https?:\/\//i', $trimmed)) {
                $urlData = 'http://' . $trimmed; 
            }
            return ['type' => 'url', 'data' => ['url' => $urlData]];
        }

        // Default to plain text
        return ['type' => 'text', 'data' => $trimmed];
    }

    public static function parseUpiUri($uri) {
        $uri = str_replace('&amp;', '&', $uri);
        // Fix unencoded spaces that might break parse_url in some PHP versions
        $safeUri = str_replace(' ', '%20', $uri);
        $parsed = parse_url($safeUri);
        
        $params = [];
        if ($parsed !== false && isset($parsed['query'])) {
            parse_str($parsed['query'], $params);
        } else {
            // Fallback: manually extract query string if parse_url still fails
            $parts = explode('?', $uri, 2);
            if (count($parts) === 2) {
                parse_str($parts[1], $params);
            } else {
                return ['raw' => $uri, 'error' => 'Invalid UPI format'];
            }
        }

        // Make keys case-insensitive by lowercasing all keys
        $params = array_change_key_case($params, CASE_LOWER);
        
        return [
            'raw' => $uri,
            'pa' => $params['pa'] ?? null,
            'pn' => isset($params['pn']) ? urldecode(str_replace('+', ' ', $params['pn'])) : null,
            'am' => $params['am'] ?? null,
            'cu' => $params['cu'] ?? null,
            'tn' => isset($params['tn']) ? urldecode(str_replace('+', ' ', $params['tn'])) : null,
            'mc' => $params['mc'] ?? null,
            'tr' => $params['tr'] ?? null,
            'tid' => $params['tid'] ?? null,
            'url' => isset($params['url']) ? urldecode($params['url']) : null,
            'mam' => $params['mam'] ?? null,
            'mode' => $params['mode'] ?? null,
            'orgid' => $params['orgid'] ?? null,
            'purpose' => $params['purpose'] ?? null,
            'sign' => $params['sign'] ?? null
        ];
    }
}

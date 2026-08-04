<?php
class PageScraper {
    public static function fetchMetadata($url) {
        $result = [
            'title' => null,
            'description' => null
        ];

        // Only fetch HTTP/HTTPS
        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = 'http://' . $url;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // We only need the first ~15KB where the head usually is
        curl_setopt($ch, CURLOPT_RANGE, '0-15360');
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($html && $httpCode >= 200 && $httpCode < 400) {
            // Extract title
            if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
                $result['title'] = trim(strip_tags($matches[1]));
                // Decode HTML entities (e.g. &amp;)
                $result['title'] = html_entity_decode($result['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            // Extract meta description
            if (preg_match('/<meta[^>]+name=(["\'])description\1[^>]+content=(["\'])(.*?)\2[^>]*>/is', $html, $matches)) {
                $result['description'] = trim($matches[3]);
                $result['description'] = html_entity_decode($result['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            } elseif (preg_match('/<meta[^>]+content=(["\'])(.*?)\1[^>]+name=(["\'])description\3[^>]*>/is', $html, $matches)) {
                $result['description'] = trim($matches[2]);
                $result['description'] = html_entity_decode($result['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        return $result;
    }
}
?>

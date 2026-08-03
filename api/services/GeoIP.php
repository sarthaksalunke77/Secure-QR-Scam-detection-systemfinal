<?php
class GeoIP {
    public static function lookup($domainOrIp) {
        $result = [
            'country' => 'Unknown',
            'countryCode' => 'XX',
            'isp' => 'Unknown ISP',
            'asn' => 'Unknown ASN',
            'city' => 'Unknown City',
            'lat' => 0.0,
            'lon' => 0.0,
            'ip' => 'Unknown'
        ];

        // Resolve host to IP if it's a domain name
        $ip = $domainOrIp;
        if (!filter_var($domainOrIp, FILTER_VALIDATE_IP)) {
            $ip = gethostbyname($domainOrIp);
            if ($ip === $domainOrIp) {
                // Resolution failed
                return $result;
            }
        }

        $result['ip'] = $ip;

        // Skip private/reserved IPs
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $result['isp'] = 'Private/Internal Network';
            $result['country'] = 'Local Loopback / Private Network';
            return $result;
        }

        // Call ip-api.com (free tier, 45 requests per minute limit)
        $url = "http://ip-api.com/json/" . urlencode($ip) . "?fields=status,message,country,countryCode,city,lat,lon,isp,as";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_USERAGENT, 'FraudEye-Security-Scanner/1.0');
        
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $data = json_decode($response, true);
            if ($data && isset($data['status']) && $data['status'] === 'success') {
                $result['country'] = $data['country'] ?? 'Unknown';
                $result['countryCode'] = $data['countryCode'] ?? 'XX';
                $result['isp'] = $data['isp'] ?? 'Unknown ISP';
                $result['asn'] = $data['as'] ?? 'Unknown ASN';
                $result['city'] = $data['city'] ?? 'Unknown City';
                $result['lat'] = $data['lat'] ?? 0.0;
                $result['lon'] = $data['lon'] ?? 0.0;
            }
        }

        return $result;
    }
}
?>

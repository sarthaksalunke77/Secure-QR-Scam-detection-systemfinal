<?php

/**
 * UpiVerification Service
 * 
 * Provides an interface to verify UPI IDs and fetch account holder names.
 * Currently uses a mock implementation as a placeholder until a commercial 
 * Verification API (like Cashfree, Razorpay, or Setu) is integrated.
 */
class UpiVerification {
    
    /**
     * Verifies a UPI ID and attempts to fetch the holder's name.
     * 
     * @param string $upiId The UPI ID (VPA) to verify
     * @return array Result containing 'success' and 'accountHolderName'
     */
    public static function verify($upiId) {
        // Validate basic syntax before attempting network call
        if (!preg_match('/^[a-zA-Z0-9.\-_]+@[a-zA-Z]+$/', $upiId)) {
            return [
                'success' => false,
                'accountHolderName' => null,
                'status' => 'INVALID FORMAT'
            ];
        }

        // Load API Keys
        $keys = json_decode(file_get_contents(__DIR__ . '/../keys.json'), true);
        $apiKey = $keys['rapidapi_upi_key'] ?? '';
        $apiHost = $keys['rapidapi_upi_host'] ?? 'upi-verification1.p.rapidapi.com';

        // If no API key is configured, fallback to safe unverified state
        if (empty($apiKey)) {
            return [
                'success' => false,
                'accountHolderName' => null,
                'status' => 'UNVERIFIED',
                'error' => 'API Key not configured in keys.json'
            ];
        }

        // Build the cURL request to RapidAPI UPI Verification Endpoint
        $curl = curl_init();
        
        $payload = json_encode(["vpa" => $upiId]);

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://" . $apiHost . "/v3/tasks/sync/verify_with_source/ind_vpa",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-RapidAPI-Host: " . $apiHost,
                "X-RapidAPI-Key: " . $apiKey
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return [
                'success' => false,
                'accountHolderName' => null,
                'status' => 'API_ERROR'
            ];
        }

        $result = json_decode($response, true);
        
        // Handle standard RapidAPI UPI validation response formats
        if (isset($result['status']) && $result['status'] === 'success' && isset($result['data']['account_exists']) && $result['data']['account_exists'] === true) {
            return [
                'success' => true,
                'accountHolderName' => $result['data']['name_at_bank'] ?? 'Verified Account',
                'status' => 'VERIFIED'
            ];
        }
        
        // Alternative response format handler
        if (isset($result['isValid']) && $result['isValid'] === true && isset($result['name'])) {
            return [
                'success' => true,
                'accountHolderName' => $result['name'],
                'status' => 'VERIFIED'
            ];
        }

        // Fallback for failed verifications or non-existent VPAs
        return [
            'success' => false,
            'accountHolderName' => null,
            'status' => 'UNVERIFIED'
        ];
    }
}

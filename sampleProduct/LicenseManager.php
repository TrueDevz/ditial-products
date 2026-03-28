<?php
// sampleProduct/LicenseManager.php

class LicenseManager {
    /**
     * Verifies a purchase code against the marketplace API.
     * @param string $code The purchase code to verify.
     * @return array Response status and data.
     */
    public static function verify($code) {
        $ch = curl_init(API_URL . "?code=" . urlencode($code));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . API_TOKEN,
            'Accept: application/json'
        ]);
        
        // Handle self-signed certificates for local testing if needed
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['status' => 500, 'error' => $error_msg];
        }
        
        curl_close($ch);

        return [
            'status' => $http_code,
            'data' => json_decode($response, true)
        ];
    }
}
?>

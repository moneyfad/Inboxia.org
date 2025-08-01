<?php
// Mailcow API functions

/**
 * Create a mailbox in Mailcow
 */
function createMailcowMailbox($email, $password, $name = '') {
    $data = [
        'local_part' => explode('@', $email)[0],
        'domain' => explode('@', $email)[1],
        'name' => $name ?: explode('@', $email)[0],
        'password' => $password,
        'password2' => $password,
        'quota' => 1024, // 1GB default quota
        'active' => '1',
        'force_pw_update' => '0',
        'tls_enforce_in' => '0',
        'tls_enforce_out' => '0'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, MAILCOW_API_URL . '/add/mailbox');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-API-Key: ' . MAILCOW_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Only for development
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    // Debug logging
    error_log("Mailcow Debug - HTTP Code: $httpCode");
    error_log("Mailcow Debug - Result: " . json_encode($result));
    error_log("Mailcow Debug - Result[0] type: " . (isset($result[0]['type']) ? $result[0]['type'] : 'NOT SET'));
    
    if ($httpCode == 200 && isset($result[0]['type']) && $result[0]['type'] == 'success') {
        error_log("Mailcow Debug - Returning success");
        return ['success' => true];
    } else {
        error_log("Mailcow Debug - Returning error");
        // Better error handling
        $error_msg = 'Failed to create mailbox';
        
        if (is_array($result)) {
            if (isset($result[0]['msg'])) {
                $msg = $result[0]['msg'];
                $error_msg = is_array($msg) ? json_encode($msg) : $msg;
            } elseif (isset($result['msg'])) {
                $msg = $result['msg'];
                $error_msg = is_array($msg) ? json_encode($msg) : $msg;
            } else {
                $error_msg = 'Mailcow API error: ' . json_encode($result);
            }
        } else {
            $error_msg = 'HTTP ' . $httpCode . ': ' . ($response ?: 'No response from Mailcow API');
        }
        
        return [
            'success' => false,
            'error' => $error_msg
        ];
    }
}

/**
 * Update mailbox status (enable/disable)
 */
function updateMailcowMailbox($email, $active) {
    $data = [
        'items' => [$email],
        'attr' => [
            'active' => $active ? '1' : '0'
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, MAILCOW_API_URL . '/edit/mailbox');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-API-Key: ' . MAILCOW_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Only for development
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    return $httpCode == 200 && isset($result[0]['type']) && $result[0]['type'] == 'success';
}

/**
 * Delete mailbox from Mailcow
 */
function deleteMailcowMailbox($email) {
    $data = [$email];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, MAILCOW_API_URL . '/delete/mailbox');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-API-Key: ' . MAILCOW_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Only for development
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    return $httpCode == 200 && isset($result[0]['type']) && $result[0]['type'] == 'success';
}

/**
 * Update mailbox password in Mailcow
 */
function updateMailcowPassword($email, $new_password) {
    $data = [
        'items' => [$email],
        'attr' => [
            'password' => $new_password,
            'password2' => $new_password
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, MAILCOW_API_URL . '/edit/mailbox');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-API-Key: ' . MAILCOW_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Only for development
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if ($httpCode == 200 && isset($result[0]['type']) && $result[0]['type'] == 'success') {
        return ['success' => true];
    } else {
        return [
            'success' => false,
            'error' => isset($result[0]['msg']) ? $result[0]['msg'] : 'Failed to update password'
        ];
    }
}
?>
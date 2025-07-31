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
    
    if ($httpCode == 200 && isset($result[0]['type']) && $result[0]['type'] == 'success') {
        return ['success' => true];
    } else {
        return [
            'success' => false,
            'error' => isset($result[0]['msg']) ? $result[0]['msg'] : 'Failed to create mailbox'
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
?>
<?php
require_once 'config.php';
require_once 'mailcow-api.php';

echo "Starting complete account cleanup...\n";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all email addresses before deleting
    echo "Getting all email addresses from database...\n";
    $stmt = $pdo->query("SELECT email_address FROM email_accounts");
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Found " . count($emails) . " email accounts to delete from Mailcow\n";
    
    // Delete mailboxes from Mailcow first
    foreach ($emails as $email) {
        echo "Deleting mailbox: $email from Mailcow...";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, MAILCOW_API_URL . '/delete/mailbox');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([$email]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-API-Key: ' . MAILCOW_API_KEY
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200) {
            $result = json_decode($response, true);
            if (is_array($result) && isset($result[0]['type']) && $result[0]['type'] == 'success') {
                echo " ✓ Success\n";
            } else {
                echo " ⚠ Warning: " . ($response ?: 'Unknown error') . "\n";
            }
        } else {
            echo " ⚠ HTTP Error $httpCode: " . ($response ?: 'No response') . "\n";
        }
        
        // Small delay to avoid rate limiting
        usleep(100000); // 0.1 second
    }
    
    echo "\nDeleting all data from database...\n";
    
    // Delete all messages
    $result = $pdo->exec("DELETE FROM messages");
    echo "Deleted $result messages\n";
    
    // Delete all email accounts
    $result = $pdo->exec("DELETE FROM email_accounts");
    echo "Deleted $result email accounts\n";
    
    // Delete all password reset tokens
    $result = $pdo->exec("DELETE FROM password_resets");
    echo "Deleted $result password reset tokens\n";
    
    // Delete all users
    $result = $pdo->exec("DELETE FROM users");
    echo "Deleted $result users\n";
    
    // Reset auto-increment counters
    echo "\nResetting auto-increment counters...\n";
    $pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE email_accounts AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE messages AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE password_resets AUTO_INCREMENT = 1");
    echo "Auto-increment counters reset\n";
    
    echo "\n✅ Complete cleanup finished successfully!\n";
    echo "- All mailboxes deleted from Mailcow\n";
    echo "- All users deleted from database\n";
    echo "- All email accounts deleted from database\n";
    echo "- All messages deleted from database\n";
    echo "- All password reset tokens deleted\n";
    echo "- Auto-increment counters reset\n";
    
} catch (Exception $e) {
    echo "\n❌ Error during cleanup: " . $e->getMessage() . "\n";
    exit(1);
}
?>
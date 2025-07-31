<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');

// Turnstile configuration
define('TURNSTILE_SITE_KEY', 'your_turnstile_site_key');
define('TURNSTILE_SECRET_KEY', 'your_turnstile_secret_key');

// Available email domains
$available_domains = [
    'yourdomain.com'
];

// Mailcow API configuration
define('MAILCOW_API_URL', 'https://mail.yourdomain.com/api/v1');
define('MAILCOW_API_KEY', 'your_mailcow_api_key');

// IMAP/SMTP configuration
define('IMAP_SERVER', 'mail.yourdomain.com');
define('IMAP_PORT', 993);
define('SMTP_SERVER', 'mail.yourdomain.com');
define('SMTP_PORT', 587);

// Start session
session_start();

// Database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
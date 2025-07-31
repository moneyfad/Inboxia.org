<?php
require_once 'config.php';
$last_updated = "January 31, 2025"; // Fixed date
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inboxia Mail - Free Email Service</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Inboxia Mail Service</h1>
        
        <p>Welcome to Inboxia - Free anonymous email service</p>
        
        <p>Create your own email address @inboxia.org</p>
        
        <div class="actions">
            <a href="register.php" class="button">Create Account</a>
            <a href="login.php" class="button secondary">Login</a>
        </div>
        
        <div class="quick-links">
            <a href="donate.php">Support Us</a> | 
            <a href="changelog.php">Changelog</a> | 
            <a href="mail/">Webmail</a>
        </div>
        
        <hr>
        
        <div class="features">
            <h2>Features:</h2>
            <ul>
                <li>Anonymous email accounts</li>
                <li>Multiple domain choices</li>
                <li>Simple web interface</li>
                <li>Fast and reliable</li>
                <li>Enhanced security features</li>
                <li>No JavaScript required (except for captcha)</li>
            </ul>
        </div>
        
        <div class="footer-info">
            <p><small>Last updated: <?php echo $last_updated; ?> | Version 2.1.0</small></p>
        </div>
    </div>
</body>
</html>

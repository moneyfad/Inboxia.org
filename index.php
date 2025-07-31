<?php
require_once 'config.php';
$page_title = "Inboxia Mail - Free Email Service";
include 'header.php';
?>

<h1>Inboxia Mail Service</h1>

<p>Welcome to Inboxia - Free anonymous email service</p>
<p>Create your own email address @inboxia.org</p>

<div class="actions">
    <a href="register.php" class="button">Create Account</a>
    <a href="login.php" class="button secondary">Login</a>
</div>

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

<?php include 'footer.php'; ?>
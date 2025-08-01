<?php
require_once 'config.php';
$page_title = "Inboxia Mail - Free Email Service";
$extra_head = '
<!-- TrustBox script -->
<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
<!-- End TrustBox script -->
';
include 'header.php';
?>

<h1>Inboxia Mail Service</h1>

<p>Welcome to Inboxia - Free anonymous email service</p>
<p>Create your own email address @inboxia.org</p>

<div class="actions">
    <a href="register.php" class="button">Create Account</a>
    <a href="login.php" class="button secondary">Login</a>
</div>

<!-- Trustpilot Widget -->
<div class="trustpilot-section">
    <div class="trustpilot-widget" data-locale="en-US" data-template-id="5419b6ffb0d04a08610f38bd" data-businessunit-id="66e0d2c8733b0a5b4e5d2fb6" data-style-height="130px" data-style-width="100%" data-theme="light">
        <a href="https://www.trustpilot.com/review/inboxia.org" target="_blank" rel="noopener">Trustpilot reviews</a>
    </div>
</div>

<?php include 'footer.php'; ?>
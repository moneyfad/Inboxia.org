<?php
require_once 'config.php';
$page_title = "Free Email Service";
$extra_head = '
<!-- TrustBox script -->
<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
<!-- End TrustBox script -->
';
include 'header.php';
?>

<h1>Free Email Service</h1>

<p>Welcome to our free anonymous email service</p>
<p>Create your own email address with any of these domains:</p>
<ul>
<?php foreach($available_domains as $domain): ?>
    <li>@<?php echo htmlspecialchars($domain); ?></li>
<?php endforeach; ?>
</ul>

<div class="actions">
    <a href="register.php" class="button">Create Account</a>
    <a href="login.php" class="button secondary">Login</a>
</div>

<div class="trustpilot-section">
    <h2>What Our Users Say</h2>
    <p>Trusted by users worldwide for secure and reliable email services</p>
    
    <!-- Trustpilot widget -->
    <div class="trustpilot-widget" 
         data-locale="en-US" 
         data-template-id="5419b6a8b0d04a076446a9ad" 
         data-businessunit-id="66ad4a25c9db4b0e9f07e8f1" 
         data-style-height="24px" 
         data-style-width="100%" 
         data-theme="light">
        <a href="https://www.trustpilot.com/review/inboxia.org" target="_blank" rel="noopener">
            Trustpilot reviews
        </a>
    </div>
    
    <p>
        <a href="https://www.trustpilot.com/review/inboxia.org" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="button secondary">
            Read All Reviews
        </a>
    </p>
</div>

<?php include 'footer.php'; ?>
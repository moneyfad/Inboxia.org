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

<?php include 'footer.php'; ?>
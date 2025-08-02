<?php
require_once 'config.php';
$page_title = "Free Email Service";
$extra_head = '';
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
    
    <p>
        <a href="https://www.trustpilot.com/review/inboxia.org" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="button">
            ★★★★★ View Reviews on Trustpilot
        </a>
    </p>
    
    <p><small>Join thousands of satisfied users who trust Inboxia for their email needs</small></p>
</div>

<?php include 'footer.php'; ?>
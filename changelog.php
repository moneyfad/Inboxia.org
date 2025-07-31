<?php
require_once 'config.php';
$page_title = "Changelog - Inboxia Mail";
include 'header.php';
?>

<h1>Version Changelog</h1>

<p><a href="index.php">← Back to Home</a></p>

<div class="changelog-meta">
    <p><strong>Last Updated:</strong> July 31, 2025</p>
</div>

<div class="changelog">
    <div class="version">
        <h2>Version 2.1.0 - July 31, 2025</h2>
        <div class="changes">
            <h3>New Features</h3>
            <ul>
                <li>Added donation page with crypto payment support</li>
                <li>Version changelog system</li>
                <li>Last updated timestamps on all pages</li>
                <li>Enhanced navigation between pages</li>
                <li>Created header and footer includes</li>
            </ul>
            
            <h3>Payment Methods</h3>
            <ul>
                <li>Bitcoin (BTC) donations</li>
                <li>Ethereum (ETH) support</li>
                <li>Litecoin (LTC) integration</li>
            </ul>
            
            <h3>Interface Improvements</h3>
            <ul>
                <li>Cleaner page headers</li>
                <li>Improved navigation links</li>
                <li>Better organized content</li>
                <li>Modular header/footer system</li>
            </ul>
        </div>
    </div>

    <div class="version">
        <h2>Version 2.0.0 - July 31, 2025</h2>
        <div class="changes">
            <h3>New Features</h3>
            <ul>
                <li>Recovery email field for password recovery</li>
                <li>Password requirements (8-32 characters)</li>
                <li>Enhanced form validation</li>
                <li>Better user experience</li>
            </ul>
            
            <h3>Security Improvements</h3>
            <ul>
                <li>Fixed Cloudflare Turnstile captcha verification</li>
                <li>Improved password validation</li>
                <li>Enhanced input sanitization</li>
                <li>Better error handling</li>
            </ul>

            <h3>Database Changes</h3>
            <ul>
                <li>Added recovery_email column to users table</li>
                <li>Improved database schema</li>
            </ul>
        </div>
    </div>

    <div class="version">
        <h2>Version 1.0.0 - Initial Release</h2>
        <div class="changes">
            <h3>Core Features</h3>
            <ul>
                <li>User registration and authentication</li>
                <li>Email account creation via Mailcow API</li>
                <li>SquirrelMail webmail integration</li>
                <li>Multi-domain email support</li>
                <li>Basic user dashboard</li>
                <li>Secure session management</li>
            </ul>

            <h3>Security Features</h3>
            <ul>
                <li>Cloudflare Turnstile captcha protection</li>
                <li>Password hashing with PHP's password_hash()</li>
                <li>SQL injection protection via prepared statements</li>
                <li>XSS protection with htmlspecialchars()</li>
            </ul>

            <h3>Technical Stack</h3>
            <ul>
                <li>PHP 7.4+ backend</li>
                <li>MySQL/MariaDB database</li>
                <li>SquirrelMail 1.4.22</li>
                <li>Mailcow API integration</li>
                <li>Responsive CSS design</li>
            </ul>
        </div>
    </div>
</div>

<div class="changelog-footer">
    <p><strong>Update Frequency:</strong> We release updates regularly to improve security, add features, and fix bugs.</p>
    <p><strong>Feedback:</strong> Got suggestions? <a href="mailto:admin@inboxia.org">Contact us</a></p>
</div>

<?php include 'footer.php'; ?>
<?php
require_once 'config.php';
$page_title = "Changelog - Inboxia Mail";
include 'header.php';
?>

<h1>Version Changelog</h1>

<div class="changelog">
    <div class="version">
        <h2>Version 2.3.0 - August 2, 2025</h2>
        <div class="changes">
            <h3>User Experience Improvements</h3>
            <ul>
                <li>Made recovery email optional during registration for faster signup</li>
                <li>Improved form layout and visual design across all pages</li>
                <li>Enhanced responsive design for better mobile and tablet experience</li>
                <li>Widened container layout for better navbar display on desktop</li>
                <li>Made email input fields visually larger than other form fields</li>
            </ul>
            
            <h3>New Features</h3>
            <ul>
                <li>Added community join page for Signal and Telegram groups</li>
                <li>Integrated community links into site navigation</li>
                <li>Added comprehensive community guidelines and contact information</li>
                <li>Enhanced form styling consistency across registration and email creation</li>
            </ul>
            
            <h3>Design & Layout Enhancements</h3>
            <ul>
                <li>Improved container width from 800px to 1100px for better content display</li>
                <li>Enhanced navbar styling to display all links on single line</li>
                <li>Added responsive breakpoints for optimal viewing on all screen sizes</li>
                <li>Improved form input padding and sizing for better usability</li>
                <li>Unified design language across all site pages</li>
            </ul>
            
            <h3>Technical Improvements</h3>
            <ul>
                <li>Enhanced CSS responsiveness with multiple breakpoints</li>
                <li>Improved form validation and user feedback</li>
                <li>Better touch targets for mobile devices</li>
                <li>Optimized layout scaling from mobile to ultra-wide displays</li>
            </ul>
        </div>
    </div>

    <div class="version">
        <h2>Version 2.2.0 - August 2, 2025</h2>
        <div class="changes">
            <h3>Bug Fixes</h3>
            <ul>
                <li>Fixed Mailcow API configuration - corrected API endpoint URL</li>
                <li>Fixed "Failed to create mailbox: HTTP 200: Hello World" error during registration</li>
                <li>Fixed 404 error when clicking "Add New Email Account" from dashboard</li>
                <li>Resolved API endpoint routing issues</li>
            </ul>
            
            <h3>New Features</h3>
            <ul>
                <li>Created add-email.php page for adding additional email accounts</li>
                <li>Added ability for users to create multiple email addresses per account</li>
                <li>Implemented proper form validation for additional email creation</li>
                <li>Added email prefix and domain selection for new addresses</li>
            </ul>
            
            <h3>Technical Improvements</h3>
            <ul>
                <li>Enhanced Mailcow API integration with proper error handling</li>
                <li>Improved validation for email creation process</li>
                <li>Added consistent styling to new pages</li>
                <li>Better integration between dashboard and email management</li>
                <li>Maintained security with proper authentication checks</li>
            </ul>
            
            <h3>User Experience</h3>
            <ul>
                <li>Users can now add multiple email addresses from their dashboard</li>
                <li>Clear error messages for failed operations</li>
                <li>Seamless redirect back to dashboard after successful email creation</li>
                <li>Consistent navigation and styling across all pages</li>
            </ul>
        </div>
    </div>

    <div class="version">
        <h2>Version 2.1.0 - July 31, 2025</h2>
        <div class="changes">
            <h3>New Features</h3>
            <ul>
                <li>Added donation page with crypto payment support</li>
                <li>Version changelog system</li>
                <li>Consistent header and footer across all pages</li>
                <li>Unified navigation system</li>
                <li>Created modular header/footer includes</li>
            </ul>
            
            <h3>Payment Methods</h3>
            <ul>
                <li>Bitcoin (BTC) donations</li>
                <li>Ethereum (ETH) support</li>
                <li>Litecoin (LTC) integration</li>
            </ul>
            
            <h3>Interface Improvements</h3>
            <ul>
                <li>Standardized navigation across all pages</li>
                <li>Cleaner page structure</li>
                <li>Better organized content</li>
                <li>Consistent user experience</li>
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
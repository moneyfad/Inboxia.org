<?php
require_once 'config.php';
$page_title = "Donate - Inboxia Mail";
include 'header.php';
?>

<h1>Support Inboxia Mail</h1>

<p><a href="index.php">← Back to Home</a></p>

<div class="donate-info">
    <h2>Help Keep Inboxia Mail Running</h2>
    <p><strong>Last Updated:</strong> July 31, 2025</p>
    <p>Your donations help us maintain our servers, improve security, and add new features. Any contribution helps keep Inboxia Mail running smoothly and free for everyone.</p>
    <p><strong>Why Crypto Only?</strong> Cryptocurrency payments are fast, secure, and help us maintain privacy while keeping operational costs low.</p>
</div>

<div class="donation-section">
    <h2>Make a Donation</h2>
    <p>Send any amount to support Inboxia Mail. Every donation helps us keep the service running.</p>
    
    <div class="crypto-addresses">
        <h3>Bitcoin (BTC)</h3>
        <div class="wallet-address">
            bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh
            <button class="copy-btn" onclick="copyAddress('bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh')">Copy</button>
        </div>
        
        <h3>Ethereum (ETH)</h3>
        <div class="wallet-address">
            0x742d35Cc6634C0532925a3b8D322C9F2A9c5D4AF
            <button class="copy-btn" onclick="copyAddress('0x742d35Cc6634C0532925a3b8D322C9F2A9c5D4AF')">Copy</button>
        </div>
        
        <h3>Litecoin (LTC)</h3>
        <div class="wallet-address">
            LTC1qw508d6qejxtdg4y5r3zarvary0c5xw7kxpjzsx
            <button class="copy-btn" onclick="copyAddress('LTC1qw508d6qejxtdg4y5r3zarvary0c5xw7kxpjzsx')">Copy</button>
        </div>
    </div>
</div>

<div class="donation-info">
    <h2>How Your Donations Help</h2>
    <ul>
        <li><strong>Server Costs:</strong> Hosting and bandwidth expenses</li>
        <li><strong>Security:</strong> Regular updates and monitoring</li>
        <li><strong>Development:</strong> New features and improvements</li>
        <li><strong>Maintenance:</strong> Bug fixes and system updates</li>
        <li><strong>Privacy:</strong> No ads or data mining needed</li>
    </ul>
</div>

<div class="donation-info">
    <h2>After Your Donation</h2>
    <ol>
        <li><strong>Send Payment:</strong> Transfer any amount to one of the wallet addresses above</li>
        <li><strong>Optional:</strong> Include your username in the transaction memo to get recognition</li>
        <li><strong>Confirmation:</strong> Donations are typically confirmed within 10-60 minutes</li>
        <li><strong>Thank You:</strong> We appreciate every contribution, no matter the size</li>
    </ol>
    <p>Questions? Contact us at <a href="mailto:donations@inboxia.org">donations@inboxia.org</a></p>
</div>

<script>
    function copyAddress(address) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(address).then(function() {
                alert('Address copied to clipboard!');
            }, function(err) {
                fallbackCopy(address);
            });
        } else {
            fallbackCopy(address);
        }
    }
    
    function fallbackCopy(address) {
        const textArea = document.createElement('textarea');
        textArea.value = address;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Address copied to clipboard!');
    }
</script>

<?php include 'footer.php'; ?>
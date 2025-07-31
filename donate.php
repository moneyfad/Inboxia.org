<?php
require_once 'config.php';
$page_title = "Donate - Inboxia Mail";
include 'header.php';
?>

<h1>Support Inboxia Mail</h1>

<div class="donate-info">
    <h2>Help Keep Inboxia Mail Running</h2>
    <p>Your donations help us maintain our servers, improve security, and add new features. Any contribution helps keep Inboxia Mail running smoothly and free for everyone.</p>
    <p><strong>Why Crypto Only?</strong> Cryptocurrency payments are fast, secure, and help us maintain privacy while keeping operational costs low.</p>
</div>

<div class="donation-section">
    <h2>Make a Donation</h2>
    <p>Send any amount to support Inboxia Mail. Every donation helps us keep the service running.</p>
    
    <div class="crypto-addresses">
        <h3>Bitcoin (BTC)</h3>
        <div class="wallet-address">
            19Vu1cwrSUF6gQFvVBgJsWdW5RngY5qAbX
            <button class="copy-btn" onclick="copyAddress('19Vu1cwrSUF6gQFvVBgJsWdW5RngY5qAbX')">Copy</button>
        </div>
        
        <h3>Ethereum (ETH)</h3>
        <div class="wallet-address">
            0xd4f2a3ae16d5cb588f3f9e060aba0c910fbe5d8a
            <button class="copy-btn" onclick="copyAddress('0xd4f2a3ae16d5cb588f3f9e060aba0c910fbe5d8a')">Copy</button>
        </div>
        
        <h3>Litecoin (LTC)</h3>
        <div class="wallet-address">
            LdmtWdwNexKs1x3n51UWR6J3ZivKaUJGRV
            <button class="copy-btn" onclick="copyAddress('LdmtWdwNexKs1x3n51UWR6J3ZivKaUJGRV')">Copy</button>
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
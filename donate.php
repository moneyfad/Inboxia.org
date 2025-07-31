<?php
require_once 'config.php';

$last_updated = date("F j, Y", filemtime(__FILE__));

// Storage plans configuration
$plans = [
    'basic' => [
        'name' => 'Basic Plan',
        'storage' => '1 GB',
        'price_btc' => '0.0005',
        'price_eth' => '0.01',
        'price_ltc' => '0.1',
        'duration' => '12 months',
        'features' => ['1 GB Email Storage', 'Basic Support', 'Standard Features']
    ],
    'pro' => [
        'name' => 'Pro Plan',
        'storage' => '5 GB',
        'price_btc' => '0.002',
        'price_eth' => '0.04',
        'price_ltc' => '0.4',
        'duration' => '12 months',
        'features' => ['5 GB Email Storage', 'Priority Support', 'Advanced Features', 'Custom Filters']
    ],
    'premium' => [
        'name' => 'Premium Plan',
        'storage' => '10 GB',
        'price_btc' => '0.004',
        'price_eth' => '0.08',
        'price_ltc' => '0.8',
        'duration' => '12 months',
        'features' => ['10 GB Email Storage', '24/7 Premium Support', 'All Features', 'Custom Domain Support', 'Advanced Security']
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate & Upgrade - Inboxia Mail</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .plans-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .plan-card {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: #f9f9f9;
            transition: transform 0.3s ease;
        }
        
        .plan-card:hover {
            transform: translateY(-5px);
            border-color: #007cba;
        }
        
        .plan-card.featured {
            border-color: #007cba;
            background: #f0f8ff;
            transform: scale(1.05);
        }
        
        .plan-storage {
            font-size: 2em;
            font-weight: bold;
            color: #007cba;
            margin: 10px 0;
        }
        
        .plan-features {
            list-style: none;
            padding: 0;
            margin: 15px 0;
        }
        
        .plan-features li {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        
        .plan-features li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
        }
        
        .crypto-prices {
            margin: 15px 0;
            padding: 15px;
            background: #fff;
            border-radius: 5px;
        }
        
        .crypto-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .crypto-option:last-child {
            border-bottom: none;
        }
        
        .crypto-logo {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }
        
        .donate-info {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .wallet-addresses {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px solid #ddd;
        }
        
        .wallet-address {
            font-family: monospace;
            background: #f8f8f8;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            word-break: break-all;
            font-size: 14px;
        }
        
        .copy-btn {
            background: #007cba;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            margin-left: 10px;
            font-size: 12px;
        }
        
        .copy-btn:hover {
            background: #005a87;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>💳 Support & Upgrade Inboxia Mail</h1>
        
        <p><a href="index.php">← Back to Home</a></p>
        
        <div class="donate-info">
            <h2>🚀 Help Keep Inboxia Mail Running!</h2>
            <p><strong>Last Updated:</strong> <?php echo $last_updated; ?></p>
            <p>Your support helps us maintain our servers, improve security, and add new features. Choose a storage upgrade plan or make a donation to keep Inboxia Mail running smoothly!</p>
            <p><strong>💎 Why Crypto Only?</strong> Crypto payments are fast, secure, and help us maintain privacy while keeping costs low.</p>
        </div>

        <h2>📦 Storage Upgrade Plans</h2>
        
        <div class="plans-container">
            <?php foreach ($plans as $key => $plan): ?>
            <div class="plan-card <?php echo $key === 'pro' ? 'featured' : ''; ?>">
                <h3><?php echo $plan['name']; ?></h3>
                <?php if ($key === 'pro'): ?>
                    <div style="background: #007cba; color: white; padding: 5px; border-radius: 15px; margin: 10px 0; font-size: 12px;">MOST POPULAR</div>
                <?php endif; ?>
                
                <div class="plan-storage"><?php echo $plan['storage']; ?></div>
                <p><strong>Duration:</strong> <?php echo $plan['duration']; ?></p>
                
                <ul class="plan-features">
                    <?php foreach ($plan['features'] as $feature): ?>
                        <li><?php echo $feature; ?></li>
                    <?php endforeach; ?>
                </ul>
                
                <div class="crypto-prices">
                    <h4>💰 Crypto Prices:</h4>
                    <div class="crypto-option">
                        <span>🟠 Bitcoin (BTC)</span>
                        <strong><?php echo $plan['price_btc']; ?> BTC</strong>
                    </div>
                    <div class="crypto-option">
                        <span>🔷 Ethereum (ETH)</span>
                        <strong><?php echo $plan['price_eth']; ?> ETH</strong>
                    </div>
                    <div class="crypto-option">
                        <span>🔘 Litecoin (LTC)</span>
                        <strong><?php echo $plan['price_ltc']; ?> LTC</strong>
                    </div>
                </div>
                
                <button class="button" onclick="showPayment('<?php echo $key; ?>')">Choose Plan</button>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="wallet-addresses" id="payment-section" style="display: none;">
            <h2>🏦 Crypto Wallet Addresses</h2>
            <p>Send your payment to the appropriate wallet address below. <strong>Include your username in the transaction memo/note!</strong></p>
            
            <div>
                <h3>🟠 Bitcoin (BTC)</h3>
                <div class="wallet-address">
                    bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh
                    <button class="copy-btn" onclick="copyAddress('bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh')">Copy</button>
                </div>
            </div>
            
            <div>
                <h3>🔷 Ethereum (ETH)</h3>
                <div class="wallet-address">
                    0x742d35Cc6634C0532925a3b8D322C9F2A9c5D4AF
                    <button class="copy-btn" onclick="copyAddress('0x742d35Cc6634C0532925a3b8D322C9F2A9c5D4AF')">Copy</button>
                </div>
            </div>
            
            <div>
                <h3>🔘 Litecoin (LTC)</h3>
                <div class="wallet-address">
                    LTC1qw508d6qejxtdg4y5r3zarvary0c5xw7kxpjzsx
                    <button class="copy-btn" onclick="copyAddress('LTC1qw508d6qejxtdg4y5r3zarvary0c5xw7kxpjzsx')">Copy</button>
                </div>
            </div>
        </div>

        <div class="donate-info">
            <h2>❤️ One-Time Donations</h2>
            <p>Want to support us without upgrading? Any amount helps keep our servers running!</p>
            <p><strong>Minimum donation:</strong> $5 USD equivalent in crypto</p>
            <button class="button" onclick="showPayment('donation')">Make a Donation</button>
        </div>

        <div class="donate-info">
            <h2>🔄 After Payment</h2>
            <ol>
                <li><strong>Send Payment:</strong> Transfer the exact amount to the wallet address</li>
                <li><strong>Include Username:</strong> Add your Inboxia username in the transaction memo</li>
                <li><strong>Wait for Confirmation:</strong> Usually takes 10-60 minutes</li>
                <li><strong>Automatic Upgrade:</strong> Your account will be upgraded automatically</li>
                <li><strong>Need Help?</strong> Contact us at <a href="mailto:billing@inboxia.org">billing@inboxia.org</a></li>
            </ol>
        </div>

        <div class="donate-info">
            <h2>⚡ Why Support Inboxia?</h2>
            <ul>
                <li>🔒 <strong>Privacy First:</strong> No data mining or ads</li>
                <li>🛡️ <strong>Security:</strong> Regular updates and monitoring</li>
                <li>🌍 <strong>Open Source:</strong> Community-driven development</li>
                <li>💚 <strong>Sustainable:</strong> Eco-friendly hosting infrastructure</li>
                <li>🚀 <strong>Innovation:</strong> Continuous feature improvements</li>
            </ul>
        </div>
    </div>

    <script>
        function showPayment(plan) {
            document.getElementById('payment-section').style.display = 'block';
            document.getElementById('payment-section').scrollIntoView({ behavior: 'smooth' });
            
            if (plan !== 'donation') {
                // You could add plan-specific messaging here
                console.log('Selected plan:', plan);
            }
        }
        
        function copyAddress(address) {
            navigator.clipboard.writeText(address).then(function() {
                alert('Address copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = address;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Address copied to clipboard!');
            });
        }
    </script>
</body>
</html>
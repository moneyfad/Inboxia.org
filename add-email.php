<?php
require_once 'config.php';
require_once 'mailcow-api.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_prefix = trim($_POST['email_prefix'] ?? '');
    $domain = $_POST['domain'] ?? '';
    $password = $_POST['password'] ?? '';
    $turnstile_response = $_POST['cf-turnstile-response'] ?? '';
    
    // Validate inputs
    if (empty($email_prefix) || empty($domain) || empty($password)) {
        $error = 'All fields are required';
    } elseif (!in_array($domain, $available_domains)) {
        $error = 'Invalid domain selected';
    } elseif (!preg_match('/^[a-zA-Z0-9_.-]+$/', $email_prefix)) {
        $error = 'Email prefix can only contain letters, numbers, underscore, dot and hyphen';
    } else {
        // Verify Turnstile captcha
        $verify_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        $verify_data = [
            'secret' => TURNSTILE_SECRET_KEY,
            'response' => $turnstile_response
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $verify_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($verify_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $verify_response = curl_exec($ch);
        curl_close($ch);
        
        $verify_result = json_decode($verify_response, true);
        
        if (!$verify_result['success']) {
            $error = 'Captcha verification failed';
        } else {
            // Verify user password
            $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if (!password_verify($password, $user['password'])) {
                $error = 'Incorrect password';
            } else {
                // Check if email exists
                $email = $email_prefix . '@' . $domain;
                $stmt = $pdo->prepare('SELECT id FROM email_accounts WHERE email_address = ?');
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Email address already exists';
                } else {
                    // Create email account
                    try {
                        $pdo->beginTransaction();
                        
                        // Create email account record
                        $stmt = $pdo->prepare('INSERT INTO email_accounts (user_id, email_address, domain) VALUES (?, ?, ?)');
                        $stmt->execute([$user_id, $email, $domain]);
                        
                        // Create mailbox in Mailcow
                        $mailcowResult = createMailcowMailbox($email, $password, $username . '_' . time());
                        if (!$mailcowResult['success']) {
                            // Rollback if mailcow creation fails
                            $pdo->rollBack();
                            $error = 'Failed to create mailbox: ' . $mailcowResult['error'];
                        } else {
                            $pdo->commit();
                            $success = 'Email account created successfully!';
                        }
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $error = 'Failed to create email account. Please try again.';
                    }
                }
            }
        }
    }
}

// Get user's existing email accounts count
$stmt = $pdo->prepare('SELECT COUNT(*) as count FROM email_accounts WHERE user_id = ?');
$stmt->execute([$user_id]);
$email_count = $stmt->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Email Account - Inboxia Mail</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body>
    <div class="container">
        <div class="navbar">
            Welcome, <?php echo htmlspecialchars($username); ?> | 
            <a href="dashboard.php">Dashboard</a> | 
            <a href="settings.php">Settings</a> | 
            <a href="/mail/" target="_blank">Webmail</a> | 
            <a href="logout.php">Logout</a>
        </div>
        
        <h1>Add New Email Account</h1>
        
        <p><a href="dashboard.php">← Back to Dashboard</a></p>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <p><a href="dashboard.php">Return to Dashboard</a> | <a href="add-email.php">Add Another Email</a></p>
        <?php else: ?>
        
        <div class="info">
            <p>You currently have <?php echo $email_count; ?> email account(s).</p>
            <p>Each email account requires captcha verification for security.</p>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email_prefix">Email Address:</label>
                <div style="display: flex; align-items: center;">
                    <input type="text" id="email_prefix" name="email_prefix" required maxlength="50" value="<?php echo htmlspecialchars($_POST['email_prefix'] ?? ''); ?>" style="flex: 1;">
                    <span style="margin: 0 10px;">@</span>
                    <select name="domain" required style="flex: 1;">
                        <?php foreach ($available_domains as $domain): ?>
                            <option value="<?php echo htmlspecialchars($domain); ?>" <?php echo (($_POST['domain'] ?? 'inboxia.org') === $domain) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($domain); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Your Account Password:</label>
                <input type="password" id="password" name="password" required>
                <small>Enter your account password to authorize this email creation</small>
            </div>
            
            <div class="form-group">
                <div class="cf-turnstile" data-sitekey="<?php echo TURNSTILE_SITE_KEY; ?>"></div>
            </div>
            
            <button type="submit">Create Email Account</button>
        </form>
        
        <?php endif; ?>
        
        <div class="info" style="margin-top: 2em;">
            <h3>Important Notes:</h3>
            <ul>
                <li>All email accounts under your account use the same password</li>
                <li>You can manage all your email accounts from the dashboard</li>
                <li>Each email account has its own inbox and storage</li>
                <li>You can disable/enable email accounts without deleting them</li>
            </ul>
        </div>
    </div>
</body>
</html>
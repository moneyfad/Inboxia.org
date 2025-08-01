<?php
require_once 'config.php';
require_once 'mailcow-api.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email_prefix = trim($_POST['email_prefix'] ?? '');
    $domain = $_POST['domain'] ?? '';
    $recovery_email = trim($_POST['recovery_email'] ?? '');
    $turnstile_response = $_POST['cf-turnstile-response'] ?? '';
    
    // Validate inputs
    if (empty($username) || empty($password) || empty($email_prefix) || empty($domain) || empty($recovery_email)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8 || strlen($password) > 32) {
        $error = 'Password must be between 8 and 32 characters';
    } elseif (!filter_var($recovery_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid recovery email address';
    } elseif (!in_array($domain, $available_domains)) {
        $error = 'Invalid domain selected';
    } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        $error = 'Username can only contain letters, numbers, underscore and hyphen';
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
            // Check if username exists
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username already exists';
            } else {
                // Check if email exists
                $email = $email_prefix . '@' . $domain;
                $stmt = $pdo->prepare('SELECT id FROM email_accounts WHERE email_address = ?');
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Email address already exists';
                } else {
                    // Create user
                    try {
                        $pdo->beginTransaction();
                        
                        $stmt = $pdo->prepare('INSERT INTO users (username, password, recovery_email) VALUES (?, ?, ?)');
                        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $recovery_email]);
                        $user_id = $pdo->lastInsertId();
                        
                        // Create email account
                        $stmt = $pdo->prepare('INSERT INTO email_accounts (user_id, email_address, domain) VALUES (?, ?, ?)');
                        $stmt->execute([$user_id, $email, $domain]);
                        
                        // Create mailbox in Mailcow
                        $mailcowResult = createMailcowMailbox($email, $password, $username);
                        if (!$mailcowResult['success']) {
                            // Rollback if mailcow creation fails
                            $pdo->rollBack();
                            
                            // Handle error properly - convert to string if needed
                            $errorMsg = isset($mailcowResult['error']) ? $mailcowResult['error'] : 'Unknown error';
                            if (is_array($errorMsg) || is_object($errorMsg)) {
                                $errorMsg = json_encode($errorMsg);
                            }
                            $error = 'Failed to create mailbox: ' . $errorMsg;
                        } else {
                            $pdo->commit();
                            $success = 'Account created successfully! You can now login.';
                        }
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $error = 'Registration failed. Please try again.';
                    }
                }
            }
        }
    }
}

$page_title = "Register - Inboxia Mail";
$extra_head = '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
include 'header.php';
?>

<h1>Register New Account</h1>

<?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <p><a href="login.php">Login now</a></p>
<?php else: ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required maxlength="50" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
    </div>
    
    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required minlength="8" maxlength="32">
        <small>Must be 8-32 characters</small>
    </div>
    
    <div class="form-group">
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </div>
    
    <div class="form-group">
        <label for="email_prefix">Email Address:</label>
        <div class="email-input-group">
            <input type="text" id="email_prefix" name="email_prefix" required maxlength="50" value="<?php echo htmlspecialchars($_POST['email_prefix'] ?? ''); ?>" class="email-prefix">
            <span class="at-symbol">@</span>
            <select name="domain" id="domain" required class="domain-select">
                <?php foreach ($available_domains as $domain_option): ?>
                    <option value="<?php echo htmlspecialchars($domain_option); ?>" <?php echo (isset($_POST['domain']) && $_POST['domain'] === $domain_option) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($domain_option); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <small>More domains coming soon!</small>
    </div>
    
    <div class="form-group">
        <label for="recovery_email">Recovery Email:</label>
        <input type="email" id="recovery_email" name="recovery_email" required maxlength="100" value="<?php echo htmlspecialchars($_POST['recovery_email'] ?? ''); ?>">
        <small>Used for password recovery</small>
    </div>
    
    <div class="form-group">
        <div class="cf-turnstile" data-sitekey="<?php echo TURNSTILE_SITE_KEY; ?>"></div>
    </div>
    
    <div class="form-group">
        <input type="submit" value="Register">
    </div>
</form>

<?php endif; ?>

<?php include 'footer.php'; ?>
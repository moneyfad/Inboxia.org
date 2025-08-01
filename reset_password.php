<?php
require_once 'config.php';
require_once 'mailcow-api.php';

$error = '';
$success = '';
$step = 'request'; // request, verify, reset

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'request_reset':
                $username = trim($_POST['username'] ?? '');
                $recovery_email = trim($_POST['recovery_email'] ?? '');
                
                if (empty($username) || empty($recovery_email)) {
                    $error = 'Username and recovery email are required';
                } else {
                    // Check if user exists with this recovery email
                    $stmt = $pdo->prepare('SELECT id, username, recovery_email FROM users WHERE username = ? AND recovery_email = ?');
                    $stmt->execute([$username, $recovery_email]);
                    $user = $stmt->fetch();
                    
                    if ($user) {
                        // Generate reset token
                        $reset_token = bin2hex(random_bytes(32));
                        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        
                        // Store reset token in database
                        $stmt = $pdo->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?) 
                                             ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)');
                        $stmt->execute([$user['id'], $reset_token, $expires_at]);
                        
                        // In a real application, you would send an email here
                        // For now, we'll show the token (not secure, but for demo)
                        $_SESSION['reset_token'] = $reset_token;
                        $_SESSION['reset_user_id'] = $user['id'];
                        
                        $success = "Password reset requested. Reset token: $reset_token (save this token)";
                        $step = 'verify';
                    } else {
                        $error = 'Username and recovery email do not match our records';
                    }
                }
                break;
                
            case 'verify_token':
                $token = trim($_POST['token'] ?? '');
                
                if (empty($token)) {
                    $error = 'Reset token is required';
                } else {
                    // Verify token
                    $stmt = $pdo->prepare('SELECT pr.user_id, u.username FROM password_resets pr 
                                         JOIN users u ON pr.user_id = u.id 
                                         WHERE pr.token = ? AND pr.expires_at > NOW()');
                    $stmt->execute([$token]);
                    $reset_data = $stmt->fetch();
                    
                    if ($reset_data) {
                        $_SESSION['reset_user_id'] = $reset_data['user_id'];
                        $_SESSION['reset_username'] = $reset_data['username'];
                        $step = 'reset';
                    } else {
                        $error = 'Invalid or expired reset token';
                    }
                }
                break;
                
            case 'reset_password':
                $new_password = $_POST['new_password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';
                $user_id = $_SESSION['reset_user_id'] ?? 0;
                
                if (empty($new_password) || empty($confirm_password)) {
                    $error = 'Both password fields are required';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'Passwords do not match';
                } elseif (strlen($new_password) < 8 || strlen($new_password) > 32) {
                    $error = 'Password must be between 8 and 32 characters';
                } elseif (!$user_id) {
                    $error = 'Invalid reset session';
                } else {
                    try {
                        $pdo->beginTransaction();
                        
                        // Update user password
                        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
                        $stmt->execute([password_hash($new_password, PASSWORD_DEFAULT), $user_id]);
                        
                        // Get all email accounts for this user
                        $stmt = $pdo->prepare('SELECT email_address FROM email_accounts WHERE user_id = ?');
                        $stmt->execute([$user_id]);
                        $email_accounts = $stmt->fetchAll();
                        
                        // Update password for all email accounts in Mailcow
                        foreach ($email_accounts as $account) {
                            updateMailcowPassword($account['email_address'], $new_password);
                        }
                        
                        // Delete the reset token
                        $stmt = $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?');
                        $stmt->execute([$user_id]);
                        
                        $pdo->commit();
                        
                        // Clear session
                        unset($_SESSION['reset_user_id'], $_SESSION['reset_username'], $_SESSION['reset_token']);
                        
                        $success = 'Password reset successfully! You can now login with your new password.';
                        $step = 'complete';
                        
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $error = 'Failed to reset password. Please try again.';
                    }
                }
                break;
        }
    }
}

// Determine current step
if (isset($_SESSION['reset_user_id']) && !isset($_POST['action'])) {
    $step = 'reset';
}

$page_title = "Reset Password - Inboxia Mail";
include 'header.php';
?>

<h1>Reset Password</h1>

<?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if ($step === 'request'): ?>
    <div class="reset-step">
        <h2>Step 1: Request Password Reset</h2>
        <p>Enter your username and recovery email to reset your password.</p>
        
        <form method="POST">
            <input type="hidden" name="action" value="request_reset">
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="recovery_email">Recovery Email:</label>
                <input type="email" id="recovery_email" name="recovery_email" required value="<?php echo htmlspecialchars($_POST['recovery_email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <input type="submit" value="Request Password Reset" class="button">
            </div>
        </form>
    </div>

<?php elseif ($step === 'verify'): ?>
    <div class="reset-step">
        <h2>Step 2: Enter Reset Token</h2>
        <p>Enter the reset token that was provided above.</p>
        
        <form method="POST">
            <input type="hidden" name="action" value="verify_token">
            
            <div class="form-group">
                <label for="token">Reset Token:</label>
                <input type="text" id="token" name="token" required placeholder="Enter your reset token">
            </div>
            
            <div class="form-group">
                <input type="submit" value="Verify Token" class="button">
            </div>
        </form>
    </div>

<?php elseif ($step === 'reset'): ?>
    <div class="reset-step">
        <h2>Step 3: Set New Password</h2>
        <p>Enter your new password for user: <strong><?php echo htmlspecialchars($_SESSION['reset_username'] ?? 'Unknown'); ?></strong></p>
        
        <form method="POST">
            <input type="hidden" name="action" value="reset_password">
            
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required minlength="8" maxlength="32">
                <small>Must be 8-32 characters</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Reset Password" class="button">
            </div>
        </form>
    </div>

<?php elseif ($step === 'complete'): ?>
    <div class="reset-step">
        <h2>Password Reset Complete!</h2>
        <p>Your password has been successfully reset. Your email accounts have also been updated with the new password.</p>
        <p><a href="login.php" class="button">Login Now</a></p>
    </div>
<?php endif; ?>

<div class="reset-info">
    <h3>Need Help?</h3>
    <ul>
        <li>Make sure you're using the correct username and recovery email</li>
        <li>Reset tokens expire after 1 hour</li>
        <li>Your new password will also be applied to all your email accounts</li>
        <li>Contact support if you continue having issues</li>
    </ul>
</div>

<style>
.reset-step {
    background: #f0f8ff;
    border: 2px solid #007cba;
    padding: 20px;
    margin: 20px 0;
}

.reset-info {
    background: #f5f5f5;
    border: 1px solid #cccccc;
    padding: 15px;
    margin: 20px 0;
}

.reset-info ul {
    margin: 10px 0;
    padding-left: 20px;
}

.reset-info li {
    margin: 5px 0;
}
</style>

<?php include 'footer.php'; ?>
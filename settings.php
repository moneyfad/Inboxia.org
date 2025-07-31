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

// Get user's current recovery email
$stmt = $pdo->prepare('SELECT recovery_email FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$current_recovery_email = $user['recovery_email'];

// Handle recovery email change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_recovery') {
    $new_recovery_email = trim($_POST['recovery_email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($new_recovery_email) || empty($password)) {
        $error = 'Recovery email and password are required';
    } elseif (!filter_var($new_recovery_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        // Verify password
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!password_verify($password, $user['password'])) {
            $error = 'Incorrect password';
        } else {
            // Update recovery email
            $stmt = $pdo->prepare('UPDATE users SET recovery_email = ? WHERE id = ?');
            $stmt->execute([$new_recovery_email, $user_id]);
            $success = 'Recovery email updated successfully';
            $current_recovery_email = $new_recovery_email;
        }
    }
}

// Handle email account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_email') {
    $email_id = (int)($_POST['email_id'] ?? 0);
    $password = $_POST['password'] ?? '';
    $confirm_delete = $_POST['confirm_delete'] ?? '';
    
    if (empty($password)) {
        $error = 'Password is required to delete an email account';
    } elseif ($confirm_delete !== 'DELETE') {
        $error = 'Please type DELETE to confirm';
    } else {
        // Verify password
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!password_verify($password, $user['password'])) {
            $error = 'Incorrect password';
        } else {
            // Get email account details
            $stmt = $pdo->prepare('SELECT email_address FROM email_accounts WHERE id = ? AND user_id = ?');
            $stmt->execute([$email_id, $user_id]);
            $email_account = $stmt->fetch();
            
            if (!$email_account) {
                $error = 'Email account not found';
            } else {
                try {
                    $pdo->beginTransaction();
                    
                    // Delete from Mailcow
                    if (deleteMailcowMailbox($email_account['email_address'])) {
                        // Delete messages
                        $stmt = $pdo->prepare('DELETE FROM messages WHERE email_account_id = ?');
                        $stmt->execute([$email_id]);
                        
                        // Delete email account
                        $stmt = $pdo->prepare('DELETE FROM email_accounts WHERE id = ? AND user_id = ?');
                        $stmt->execute([$email_id, $user_id]);
                        
                        $pdo->commit();
                        $success = 'Email account deleted successfully';
                    } else {
                        $pdo->rollBack();
                        $error = 'Failed to delete mailbox from mail server';
                    }
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = 'Failed to delete email account';
                }
            }
        }
    }
}

// Get user's email accounts
$stmt = $pdo->prepare('SELECT * FROM email_accounts WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$emails = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Inboxia Mail</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            Welcome, <?php echo htmlspecialchars($username); ?> | 
            <a href="dashboard.php">Dashboard</a> | 
            <a href="settings.php">Settings</a> | 
            <a href="https://wm.inboxia.org/" target="_blank">Webmail</a> | 
            <a href="logout.php">Logout</a>
        </div>
        
        <h1>Account Settings</h1>
        
        <p><a href="dashboard.php">← Back to Dashboard</a></p>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <!-- Change Recovery Email Section -->
        <h2>Change Recovery Email</h2>
        <div class="settings-section">
            <p>Current recovery email: <strong><?php echo htmlspecialchars($current_recovery_email); ?></strong></p>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="change_recovery">
                
                <div class="form-group">
                    <label for="recovery_email">New Recovery Email:</label>
                    <input type="email" id="recovery_email" name="recovery_email" required maxlength="100">
                </div>
                
                <div class="form-group">
                    <label for="password_recovery">Your Password:</label>
                    <input type="password" id="password_recovery" name="password" required>
                </div>
                
                <button type="submit">Update Recovery Email</button>
            </form>
        </div>
        
        <!-- Delete Email Accounts Section -->
        <h2>Delete Email Accounts</h2>
        <div class="danger-section">
            <div class="warning">
                <strong>⚠️ WARNING:</strong> Deleting an email account will permanently remove:
                <ul>
                    <li>The email address</li>
                    <li>All messages in the inbox</li>
                    <li>All sent messages</li>
                    <li>All data associated with this email</li>
                </ul>
                <strong>This action CANNOT be undone!</strong>
            </div>
            
            <?php if (empty($emails)): ?>
                <p>You don't have any email accounts to delete.</p>
            <?php else: ?>
                <form method="POST" action="" onsubmit="return confirm('Are you absolutely sure you want to delete this email account? This action CANNOT be undone!');">
                    <input type="hidden" name="action" value="delete_email">
                    
                    <div class="form-group">
                        <label for="email_id">Select Email to Delete:</label>
                        <select id="email_id" name="email_id" required>
                            <option value="">-- Select an email account --</option>
                            <?php foreach ($emails as $email): ?>
                                <option value="<?php echo $email['id']; ?>">
                                    <?php echo htmlspecialchars($email['email_address']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_delete">Type DELETE to confirm:</label>
                        <input type="text" id="confirm_delete" name="confirm_delete" required placeholder="Type DELETE">
                    </div>
                    
                    <div class="form-group">
                        <label for="password_delete">Your Password:</label>
                        <input type="password" id="password_delete" name="password" required>
                    </div>
                    
                    <button type="submit" class="danger-button">Delete Email Account</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
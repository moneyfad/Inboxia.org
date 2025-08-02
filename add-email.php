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
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($email_prefix) || empty($domain) || empty($password)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8 || strlen($password) > 32) {
        $error = 'Password must be between 8 and 32 characters';
    } elseif (!in_array($domain, $available_domains)) {
        $error = 'Invalid domain selected';
    } elseif (!preg_match('/^[a-zA-Z0-9_.-]+$/', $email_prefix)) {
        $error = 'Email prefix can only contain letters, numbers, underscore, dot and hyphen';
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
                $mailcowResult = createMailcowMailbox($email, $password, $username);
                if (!$mailcowResult['success']) {
                    // Rollback if mailcow creation fails
                    $pdo->rollBack();
                    
                    $errorMsg = isset($mailcowResult['error']) ? $mailcowResult['error'] : 'Unknown error';
                    if (is_array($errorMsg) || is_object($errorMsg)) {
                        $errorMsg = json_encode($errorMsg);
                    }
                    $error = 'Failed to create mailbox: ' . $errorMsg;
                } else {
                    $pdo->commit();
                    $success = 'Email account created successfully!';
                    
                    // Redirect to dashboard after success
                    header('Location: dashboard.php?msg=' . urlencode('Email account created successfully!'));
                    exit;
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

$page_title = "Add Email Account - Inboxia Mail";
include 'header.php';
?>

<h1>Add New Email Account</h1>

<div class="back-link">
    <a href="dashboard.php">&larr; Back to Dashboard</a>
</div>

<?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form method="POST" class="auth-form">
    <h2>Create Additional Email Address</h2>
    
    <div class="form-group">
        <label for="email_prefix">Email Address:</label>
        <div class="email-input">
            <input type="text" id="email_prefix" name="email_prefix" value="<?php echo htmlspecialchars($_POST['email_prefix'] ?? ''); ?>" required>
            <span class="at-symbol">@</span>
            <select name="domain" required>
                <option value="">Select domain</option>
                <?php foreach($available_domains as $available_domain): ?>
                    <option value="<?php echo htmlspecialchars($available_domain); ?>" <?php echo (($_POST['domain'] ?? '') === $available_domain) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($available_domain); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <small>Choose a unique email prefix for this domain</small>
    </div>

    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required minlength="8" maxlength="32">
        <small>8-32 characters (this will be the password for this specific email account)</small>
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </div>

    <button type="submit" class="submit-button">Create Email Account</button>
</form>

<div class="info-box">
    <h3>Note:</h3>
    <p>This email account will be linked to your user account (<?php echo htmlspecialchars($username); ?>). You can manage all your email accounts from your dashboard.</p>
</div>

<?php include 'footer.php'; ?>
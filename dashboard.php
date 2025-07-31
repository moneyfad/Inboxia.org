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
$message = '';
$error = '';

// Handle creating new email address
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_email') {
    $email_prefix = trim($_POST['email_prefix'] ?? '');
    $domain = $_POST['domain'] ?? '';
    
    if (empty($email_prefix) || empty($domain)) {
        $error = 'Email prefix and domain are required';
    } elseif (!in_array($domain, $available_domains)) {
        $error = 'Invalid domain selected';
    } elseif (!preg_match('/^[a-zA-Z0-9_.-]+$/', $email_prefix)) {
        $error = 'Email prefix can only contain letters, numbers, underscore, dot and hyphen';
    } else {
        $email = $email_prefix . '@' . $domain;
        
        // Check if email exists
        $stmt = $pdo->prepare('SELECT id FROM email_accounts WHERE email_address = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email address already exists';
        } else {
            // Get user's password for mailcow (we'll need to store it or ask for it)
            // For now, we'll use a temporary approach - in production, handle this differently
            $error = 'Please use the registration form to create your first email address.';
            // In a real implementation, you'd either:
            // 1. Store the password encrypted in the database
            // 2. Ask the user to enter their password again
            // 3. Use a different authentication method
        }
    }
}

// Handle toggling email status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $email_id = (int)$_GET['toggle'];
    
    // Get email details
    $stmt = $pdo->prepare('SELECT email_address, is_active FROM email_accounts WHERE id = ? AND user_id = ?');
    $stmt->execute([$email_id, $user_id]);
    $emailData = $stmt->fetch();
    
    if ($emailData) {
        $newStatus = !$emailData['is_active'];
        
        // Update in mailcow
        if (updateMailcowMailbox($emailData['email_address'], $newStatus)) {
            // Update in database
            $stmt = $pdo->prepare('UPDATE email_accounts SET is_active = ? WHERE id = ? AND user_id = ?');
            $stmt->execute([$newStatus, $email_id, $user_id]);
        }
    }
    
    header('Location: dashboard.php');
    exit;
}

// Get user's email accounts
$stmt = $pdo->prepare('SELECT * FROM email_accounts WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$emails = $stmt->fetchAll();

// Get message counts for each email
$email_stats = [];
foreach ($emails as $email) {
    $stmt = $pdo->prepare('SELECT COUNT(*) as total, SUM(is_read = 0) as unread FROM messages WHERE email_account_id = ?');
    $stmt->execute([$email['id']]);
    $email_stats[$email['id']] = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Inboxia Mail</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            Welcome, <?php echo htmlspecialchars($username); ?> | 
            <a href="dashboard.php">Dashboard</a> | 
            <a href="/mail/" target="_blank">Webmail</a> | 
            <a href="logout.php">Logout</a>
        </div>
        
        <h1>Email Dashboard</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if (count($emails) == 0): ?>
            <div class="error">
                <p>To create your first email address, please use the registration process.</p>
                <p>Additional email addresses can be created after setting up your primary account.</p>
            </div>
        <?php endif; ?>
        
        <h2>Your Email Addresses</h2>
        <?php if (empty($emails)): ?>
            <p>You don't have any email addresses yet.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Email Address</th>
                    <th>Status</th>
                    <th>Messages</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($emails as $email): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($email['email_address']); ?></td>
                        <td><?php echo $email['is_active'] ? 'Active' : 'Disabled'; ?></td>
                        <td>
                            Total: <?php echo $email_stats[$email['id']]['total']; ?> 
                            (Unread: <?php echo $email_stats[$email['id']]['unread'] ?: '0'; ?>)
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($email['created_at'])); ?></td>
                        <td>
                            <a href="?toggle=<?php echo $email['id']; ?>">
                                <?php echo $email['is_active'] ? 'Disable' : 'Enable'; ?>
                            </a> | 
                            <a href="/mail/" target="_blank">Webmail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        
        <h2>Email Configuration</h2>
        <div class="email-item">
            <p><strong>Webmail Access:</strong></p>
            <p><a href="/mail/" target="_blank">https://inboxia.org/mail/</a><br>
            Login with your full email address and password</p>
        </div>
        
        <div class="email-item">
            <p><strong>Incoming Mail Server (IMAP):</strong></p>
            <p>Server: <?php echo IMAP_SERVER; ?><br>
            Port: <?php echo IMAP_PORT; ?> (SSL/TLS)<br>
            Username: Your full email address<br>
            Password: Your account password</p>
        </div>
        
        <div class="email-item">
            <p><strong>Outgoing Mail Server (SMTP):</strong></p>
            <p>Server: <?php echo SMTP_SERVER; ?><br>
            Port: <?php echo SMTP_PORT; ?> (STARTTLS)<br>
            Authentication: Required<br>
            Username: Your full email address<br>
            Password: Your account password</p>
        </div>
        
        <div class="email-item">
            <p><strong>POP3 Server (Alternative):</strong></p>
            <p>Server: <?php echo IMAP_SERVER; ?><br>
            Port: 995 (SSL/TLS)<br>
            Username: Your full email address<br>
            Password: Your account password</p>
        </div>
    </div>
</body>
</html>
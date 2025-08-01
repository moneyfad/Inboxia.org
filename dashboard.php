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

// Check if user is suspended
$stmt = $pdo->prepare('SELECT is_suspended FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user && $user['is_suspended']) {
    session_destroy();
    header('Location: login.php?error=' . urlencode('Your account has been suspended'));
    exit;
}

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';

// Handle toggling email status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $email_id = (int)$_GET['toggle'];
    
    // Get email details
    $stmt = $pdo->prepare('SELECT email_address, is_active FROM email_accounts WHERE id = ? AND user_id = ?');
    $stmt->execute([$email_id, $user_id]);
    $emailData = $stmt->fetch();
    
    if ($emailData) {
        $newStatus = !$emailData['is_active'];
        $statusText = $newStatus ? 'enabled' : 'disabled';
        
        // Update in mailcow
        $mailcowResult = updateMailcowMailbox($emailData['email_address'], $newStatus);
        if ($mailcowResult['success']) {
            // Update in database
            $stmt = $pdo->prepare('UPDATE email_accounts SET is_active = ? WHERE id = ? AND user_id = ?');
            $stmt->execute([$newStatus, $email_id, $user_id]);
            $message = "Email account successfully {$statusText}.";
        } else {
            $error = "Failed to {$statusText} email account: " . ($mailcowResult['error'] ?? 'Unknown error');
        }
    } else {
        $error = 'Email account not found.';
    }
    
    header('Location: dashboard.php?msg=' . urlencode($message ?? '') . '&err=' . urlencode($error ?? ''));
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

$page_title = "Dashboard - Inboxia Mail";
include 'header.php';
?>

<h1>Email Dashboard</h1>

<?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($message): ?>
    <div class="success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div style="margin-bottom: 20px;">
    <a href="add-email.php" class="button">+ Add New Email Account</a>
</div>

<h2>Your Email Addresses</h2>
<?php if (empty($emails)): ?>
    <p>You don't have any email addresses yet. <a href="add-email.php">Create your first email account</a>.</p>
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
                    <a href="https://wm.inboxia.org/" target="_blank">Check Webmail</a>
                </td>
                <td><?php echo date('Y-m-d H:i', strtotime($email['created_at'])); ?></td>
                <td>
                    <a href="?toggle=<?php echo $email['id']; ?>">
                        <?php echo $email['is_active'] ? 'Disable' : 'Enable'; ?>
                    </a> | 
                    <a href="https://wm.inboxia.org/" target="_blank">Webmail</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<h2>Email Configuration</h2>
<div class="email-item">
    <p><strong>Webmail Access:</strong></p>
    <p><a href="https://wm.inboxia.org/" target="_blank">https://wm.inboxia.org/</a><br>
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

<?php include 'footer.php'; ?>
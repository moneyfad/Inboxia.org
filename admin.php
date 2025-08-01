<?php
require_once 'config.php';

// Check if user is admin (for now, simple check - you can enhance this)
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$page_title = "Admin Panel - Inboxia Mail";
include 'header.php';

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $stmt->fetch()['total_users'];

$stmt = $pdo->query("SELECT COUNT(*) as total_emails FROM email_accounts");
$total_emails = $stmt->fetch()['total_emails'];

$stmt = $pdo->query("SELECT COUNT(*) as total_messages FROM messages");
$total_messages = $stmt->fetch()['total_messages'];

// Get recent users
$stmt = $pdo->query("SELECT u.username, u.created_at, u.last_login, 
                     COUNT(e.id) as email_count 
                     FROM users u 
                     LEFT JOIN email_accounts e ON u.id = e.user_id 
                     GROUP BY u.id 
                     ORDER BY u.created_at DESC 
                     LIMIT 10");
$recent_users = $stmt->fetchAll();

// Handle actions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete_user':
                $user_id = (int)$_POST['user_id'];
                try {
                    $pdo->beginTransaction();
                    
                    // Delete messages first
                    $stmt = $pdo->prepare("DELETE m FROM messages m 
                                         JOIN email_accounts e ON m.email_account_id = e.id 
                                         WHERE e.user_id = ?");
                    $stmt->execute([$user_id]);
                    
                    // Delete email accounts
                    $stmt = $pdo->prepare("DELETE FROM email_accounts WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    
                    // Delete user
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    
                    $pdo->commit();
                    $message = "User deleted successfully";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Failed to delete user: " . $e->getMessage();
                }
                break;
                
            case 'clear_all_accounts':
                try {
                    $pdo->beginTransaction();
                    $pdo->exec("DELETE FROM messages");
                    $pdo->exec("DELETE FROM email_accounts");  
                    $pdo->exec("DELETE FROM users WHERE username != 'admin'");
                    $pdo->commit();
                    $message = "All user accounts cleared (admin preserved)";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Failed to clear accounts: " . $e->getMessage();
                }
                break;
        }
        
        // Refresh page to show updated data
        header('Location: admin.php');
        exit;
    }
}
?>

<h1>Admin Panel</h1>

<?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($message): ?>
    <div class="success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="admin-stats">
    <h2>System Statistics</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <h3><?php echo $total_users; ?></h3>
            <p>Total Users</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_emails; ?></h3>
            <p>Email Accounts</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_messages; ?></h3>
            <p>Total Messages</p>
        </div>
    </div>
</div>

<div class="admin-actions">
    <h2>Quick Actions</h2>
    <form method="POST" onsubmit="return confirm('Are you sure you want to clear ALL user accounts? This cannot be undone!')">
        <input type="hidden" name="action" value="clear_all_accounts">
        <button type="submit" class="danger-button">Clear All Accounts</button>
    </form>
</div>

<div class="user-list">
    <h2>Recent Users</h2>
    <?php if (empty($recent_users)): ?>
        <p>No users found</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Username</th>
                <th>Email Accounts</th>
                <th>Created</th>
                <th>Last Login</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($recent_users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo $user['email_count']; ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                    <td><?php echo $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : 'Never'; ?></td>
                    <td>
                        <?php if ($user['username'] !== 'admin'): ?>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this user?')">
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="user_id" value="<?php echo $user['id'] ?? 0; ?>">
                                <button type="submit" class="danger-button" style="font-size: 12px; padding: 2px 6px;">Delete</button>
                            </form>
                        <?php else: ?>
                            <span style="color: #007cba;">Admin</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<style>
.admin-stats {
    margin: 20px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    background: #f0f8ff;
    border: 2px solid #007cba;
    padding: 20px;
    text-align: center;
}

.stat-card h3 {
    font-size: 2em;
    color: #007cba;
    margin: 0;
}

.stat-card p {
    margin: 10px 0 0 0;
    font-weight: bold;
}

.admin-actions {
    margin: 30px 0;
    padding: 20px;
    background: #ffeeee;
    border: 2px solid #cc0000;
}

.user-list {
    margin: 20px 0;
}
</style>

<?php include 'footer.php'; ?>
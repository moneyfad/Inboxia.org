<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Inboxia Mail'; ?></title>
    <link rel="stylesheet" href="style.css">
    <?php if (isset($extra_head)) echo $extra_head; ?>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['username'])): ?>
        <!-- Logged in navbar -->
        <div class="navbar">
            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> | 
            <a href="dashboard.php">Dashboard</a> | 
            <?php if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin'): ?>
                <a href="admin.php">Admin Panel</a> | 
            <?php endif; ?>
            <a href="donate.php">Support</a> | 
            <a href="join.php">Join Community</a> | 
            <a href="changelog.php">Changelog</a> | 
            <a href="https://wm.inboxia.org/" target="_blank">Webmail</a> | 
            <a href="logout.php">Logout</a>
        </div>
        <?php else: ?>
        <!-- Not logged in navbar -->
        <div class="nav-links">
            <a href="index.php">Home</a> | 
            <a href="register.php">Register</a> | 
            <a href="login.php">Login</a> | 
            <a href="donate.php">Support</a> | 
            <a href="join.php">Join Community</a> | 
            <a href="changelog.php">Changelog</a> | 
            <a href="https://wm.inboxia.org/" target="_blank">Webmail</a>
        </div>
        <?php endif; ?>
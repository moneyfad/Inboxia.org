<?php
require_once 'config.php';
$page_title = "Join Our Community - Inboxia Mail";
include 'header.php';
?>

<h1>Join Our Community</h1>

<div class="community-intro">
    <p>Connect with other Inboxia Mail users, get support, and stay updated with the latest news and features!</p>
</div>

<div class="community-platforms">
    <div class="platform-card">
        <h2>📱 Signal Group</h2>
        <p>Join our secure Signal group for private discussions, support, and announcements.</p>
        <div class="platform-features">
            <ul>
                <li>End-to-end encrypted messaging</li>
                <li>Real-time support from the community</li>
                <li>Beta feature announcements</li>
                <li>Security tips and best practices</li>
            </ul>
        </div>
        <div class="join-button-container">
            <a href="https://signal.group/#CjQKILv6plQ-baIJAcdda1gGb_z_48qXFYNh0O1b5olQSKjhEhCu5Rm5j09EBnAh3vyu7EBP" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="button join-button signal-button">
                Join Signal Group
            </a>
        </div>
        <small class="platform-note">Requires Signal app installed on your device</small>
    </div>

    <div class="platform-card">
        <h2>💬 Telegram Channel</h2>
        <p>Follow our Telegram channel for updates, news, and community discussions.</p>
        <div class="platform-features">
            <ul>
                <li>Instant notifications</li>
                <li>Service status updates</li>
                <li>Feature announcements</li>
                <li>Community discussions</li>
            </ul>
        </div>
        <div class="join-button-container">
            <a href="https://t.me/inboxia" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="button join-button telegram-button">
                Join Telegram
            </a>
        </div>
        <small class="platform-note">Works on web, mobile, and desktop</small>
    </div>
</div>

<div class="community-guidelines">
    <h2>Community Guidelines</h2>
    <div class="guidelines-content">
        <ul>
            <li><strong>Be respectful:</strong> Treat all community members with courtesy and respect</li>
            <li><strong>Stay on topic:</strong> Keep discussions relevant to Inboxia Mail and email services</li>
            <li><strong>No spam:</strong> Avoid promotional content or repetitive messages</li>
            <li><strong>Help others:</strong> Share your knowledge and help fellow users</li>
            <li><strong>Report issues:</strong> Use these channels to report bugs or suggest improvements</li>
        </ul>
    </div>
</div>

<div class="alternative-contact">
    <h2>Alternative Contact</h2>
    <p>Prefer email? You can also reach us directly at: <strong>admin@inboxia.org</strong></p>
</div>

<style>
.community-intro {
    background-color: #F0F8FF;
    border: 1px solid #B0D4F1;
    padding: 20px;
    margin: 20px 0;
    border-radius: 4px;
    text-align: center;
}

.community-platforms {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin: 30px 0;
}

.platform-card {
    background-color: #FFFFFF;
    border: 2px solid #CCCCCC;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.platform-card h2 {
    margin-top: 0;
    color: #000080;
    border-bottom: 2px solid #EEEEEE;
    padding-bottom: 10px;
}

.platform-features ul {
    list-style-type: none;
    padding-left: 0;
}

.platform-features li {
    padding: 5px 0;
    border-bottom: 1px dotted #CCCCCC;
}

.platform-features li:before {
    content: "✓ ";
    color: #008000;
    font-weight: bold;
    margin-right: 8px;
}

.join-button-container {
    text-align: center;
    margin: 20px 0 15px 0;
}

.join-button {
    display: inline-block;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
    border: 2px solid;
}

.signal-button {
    background-color: #3A76F0;
    color: #FFFFFF;
    border-color: #3A76F0;
}

.signal-button:hover {
    background-color: #2952CC;
    border-color: #2952CC;
    transform: translateY(-2px);
}

.telegram-button {
    background-color: #0088CC;
    color: #FFFFFF;
    border-color: #0088CC;
}

.telegram-button:hover {
    background-color: #006699;
    border-color: #006699;
    transform: translateY(-2px);
}

.platform-note {
    color: #666666;
    font-style: italic;
    font-size: 12px;
    display: block;
    text-align: center;
}

.community-guidelines {
    background-color: #FFF8DC;
    border: 1px solid #DDD;
    padding: 20px;
    margin: 30px 0;
    border-radius: 4px;
}

.community-guidelines h2 {
    margin-top: 0;
    color: #B8860B;
}

.guidelines-content ul {
    list-style-type: none;
    padding-left: 0;
}

.guidelines-content li {
    padding: 8px 0;
    border-bottom: 1px dotted #DDD;
}

.guidelines-content li:last-child {
    border-bottom: none;
}

.alternative-contact {
    background-color: #F5F5F5;
    border: 1px solid #CCCCCC;
    padding: 20px;
    text-align: center;
    border-radius: 4px;
    margin: 30px 0;
}

/* Mobile responsiveness */
@media (max-width: 600px) {
    .community-platforms {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .platform-card {
        padding: 20px;
    }
    
    .join-button {
        padding: 10px 20px;
        font-size: 14px;
    }
}
</style>

<?php include 'footer.php'; ?>
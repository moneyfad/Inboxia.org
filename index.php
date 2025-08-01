<?php
require_once 'config.php';
$page_title = "Inboxia Mail - Free Email Service";
$extra_head = '
<!-- TrustBox script -->
<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
<!-- End TrustBox script -->
';
include 'header.php';
?>

<h1>Inboxia Mail Service</h1>

<p>Welcome to Inboxia - Free anonymous email service</p>
<p>Create your own email address @inboxia.org</p>



<div class="features">
    <h2>Features:</h2>
    <ul>
        <li>Anonymous email accounts</li>
        <li>Multiple domain choices</li>
        <li>Simple web interface</li>
        <li>Fast and reliable</li>
        <li>Enhanced security features</li>
        <li>No JavaScript required (except for captcha)</li>
    </ul>
</div>

<!-- Trustpilot Widget - Replace YOUR_BUSINESS_ID with your actual Trustpilot business ID -->
<div class="trustpilot-widget" data-locale="en-US" data-template-id="5419b6a8b0d04a076446a9ad" data-businessunit-id="YOUR_BUSINESS_ID" data-style-height="24px" data-style-width="100%" data-theme="light">
    <a href="https://www.trustpilot.com/review/inboxia.org" target="_blank" rel="noopener">Trustpilot</a>
</div>

<div class="feature-request">
    <h3>Request a Feature</h3>
    <form id="feature-request-form" method="POST" action="submit-feature.php">
        <div class="form-group">
            <textarea name="feature" placeholder="What feature would you like to see in Inboxia Mail?" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <input type="submit" value="Submit Feature Request" class="button">
        </div>
    </form>
</div>

<script>
// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// Feature request form handler
document.getElementById('feature-request-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const submitButton = this.querySelector('input[type="submit"]');
    const originalValue = submitButton.value;
    submitButton.value = 'Submitting...';
    submitButton.disabled = true;
    
    const formData = new FormData(this);
    
    fetch('submit-feature.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            this.reset();
            // Show success message
            const successDiv = document.createElement('div');
            successDiv.className = 'success';
            successDiv.textContent = 'Thank you for your feature request! We\'ll review it soon.';
            this.parentNode.insertBefore(successDiv, this);
            setTimeout(() => successDiv.remove(), 5000);
        } else {
            alert('Error: ' + (data.error || 'Please try again.'));
        }
    })
    .catch(error => {
        alert('Network error. Please try again.');
    })
    .finally(() => {
        submitButton.value = originalValue;
        submitButton.disabled = false;
    });
});
</script>

<?php include 'footer.php'; ?>
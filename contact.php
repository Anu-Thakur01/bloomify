<?php
$page_title = 'Contact Us';
require_once __DIR__ . '/includes/header.php';

$message = '';
$message_type = '';

// Handle Contact Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_message'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message_text = sanitize($_POST['message']);
    
    // For this demo, we'll just show a success message
    // In production, you'd send an email or save to database
    $message = "Thank you, $name! Your message has been received. We'll get back to you within 24 hours.";
    $message_type = 'success';
}
?>

<style>
    /* Hero Section */
    .contact-hero {
        background: linear-gradient(135deg, rgba(45, 106, 79, 0.95), rgba(64, 145, 108, 0.9));
        color: white;
        text-align: center;
        padding: 70px 20px;
        border-radius: 0 0 30px 30px;
        margin-bottom: 50px;
    }
    .contact-hero h1 {
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 15px;
        letter-spacing: -1px;
    }
    .contact-hero p {
        font-size: 1.15rem;
        opacity: 0.95;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* Contact Info Cards */
    .contact-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 25px;
        max-width: 1100px;
        margin: 0 auto 60px;
        padding: 0 20px;
    }
    .info-card {
        background: white;
        padding: 30px 25px;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    .info-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 35px rgba(45, 106, 79, 0.12);
        border-color: #40916c;
    }
    .info-icon {
        width: 60px;
        height: 60px;
        background: #f0f9f4;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 18px;
        font-size: 1.8rem;
    }
    .info-card h3 {
        color: #2d6a4f;
        font-size: 1.1rem;
        margin-bottom: 10px;
        font-weight: 700;
    }
    .info-card p {
        color: #6c757d;
        font-size: 0.95rem;
        line-height: 1.6;
        margin: 0;
    }
    .info-card a {
        color: #40916c;
        text-decoration: none;
        font-weight: 600;
    }
    .info-card a:hover {
        color: #2d6a4f;
        text-decoration: underline;
    }

    /* Contact Form Section */
    .contact-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        max-width: 1100px;
        margin: 0 auto 60px;
        padding: 0 20px;
        align-items: start;
    }
    .contact-form-box {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
    }
    .contact-form-box h2 {
        color: #2d6a4f;
        font-size: 1.8rem;
        margin-bottom: 10px;
        font-weight: 800;
    }
    .contact-form-box > p {
        color: #6c757d;
        margin-bottom: 30px;
        font-size: 0.95rem;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s;
        font-family: inherit;
        box-sizing: border-box;
    }
    .form-control:focus {
        outline: none;
        border-color: #2d6a4f;
        box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.1);
    }
    textarea.form-control {
        resize: vertical;
        min-height: 130px;
    }
    .btn-submit {
        background: #2d6a4f;
        color: white;
        padding: 14px 35px;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        width: 100%;
    }
    .btn-submit:hover {
        background: #1b4332;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(45, 106, 79, 0.25);
    }

    /* Map / Info Side */
    .contact-map-box {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
    }
    .contact-map-box h2 {
        color: #2d6a4f;
        font-size: 1.8rem;
        margin-bottom: 20px;
        font-weight: 800;
    }
    .map-placeholder {
        background: linear-gradient(135deg, #f0f9f4, #e8f5e9);
        border-radius: 16px;
        height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 5rem;
        margin-bottom: 25px;
        border: 2px dashed #40916c;
    }
    .hours-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .hours-list li {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        color: #4a5568;
        font-size: 0.95rem;
    }
    .hours-list li:last-child {
        border-bottom: none;
    }
    .hours-list li span:first-child {
        font-weight: 600;
        color: #2d3748;
    }
    .hours-list li span:last-child {
        color: #40916c;
        font-weight: 600;
    }

    /* Alert Message */
    .alert {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-weight: 600;
    }
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .contact-wrapper {
            grid-template-columns: 1fr;
        }
        .contact-hero h1 {
            font-size: 2rem;
        }
    }
</style>

<!-- Hero Section -->
<div class="contact-hero">
    <h1>Get in Touch</h1>
    <p>Have questions about our flowers, delivery, or custom arrangements? We'd love to hear from you!</p>
</div>

<!-- Contact Info Cards -->
<div class="contact-info-grid">
    <div class="info-card">
        <div class="info-icon"></div>
        <h3>Visit Us</h3>
        <p>Thamel, Kathmandu<br>Nepal 44600</p>
    </div>
    <div class="info-card">
        <div class="info-icon"></div>
        <h3>Call Us</h3>
        <p><a href="tel:+9779812345678">+977 9812345678</a><br>Mon-Sat, 9am-6pm</p>
    </div>
    <div class="info-card">
        <div class="info-icon">️</div>
        <h3>Email Us</h3>
        <p><a href="mailto:hello@bloomify.com">hello@bloomify.com</a><br>We reply within 24 hours</p>
    </div>
    <div class="info-card">
        <div class="info-icon"></div>
        <h3>Live Chat</h3>
        <p>Chat with our team<br>Available 9am-9pm NPT</p>
    </div>
</div>

<!-- Contact Form & Map Section -->
<div class="contact-wrapper">
    <!-- Contact Form -->
    <div class="contact-form-box">
        <h2>Send Us a Message</h2>
        <p>Fill out the form below and we'll get back to you as soon as possible.</p>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="contact.php">
            <div class="form-group">
                <label for="name">Your Name *</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" required>
            </div>
            
            <div class="form-group">
                <label for="subject">Subject *</label>
                <input type="text" id="subject" name="subject" class="form-control" placeholder="How can we help you?" required>
            </div>
            
            <div class="form-group">
                <label for="message">Your Message *</label>
                <textarea id="message" name="message" class="form-control" placeholder="Tell us more about your inquiry..." required></textarea>
            </div>
            
            <button type="submit" name="submit_message" class="btn-submit">Send Message →</button>
        </form>
    </div>
    
    <!-- Map & Hours -->
    <div class="contact-map-box">
        <h2>Find Us Here</h2>
        <div class="map-placeholder"></div>
        
        <h3 style="color: #2d6a4f; font-size: 1.2rem; margin-bottom: 15px; font-weight: 700;">Opening Hours</h3>
        <ul class="hours-list">
            <li>
                <span>Monday - Friday</span>
                <span>9:00 AM - 8:00 PM</span>
            </li>
            <li>
                <span>Saturday</span>
                <span>9:00 AM - 6:00 PM</span>
            </li>
            <li>
                <span>Sunday</span>
                <span>10:00 AM - 4:00 PM</span>
            </li>
            <li>
                <span>Public Holidays</span>
                <span style="color: #dc3545;">Closed</span>
            </li>
        </ul>
        
        <div style="margin-top: 25px; padding: 20px; background: #f0f9f4; border-radius: 12px; border-left: 4px solid #2d6a4f;">
            <strong style="color: #2d6a4f;">Same-Day Delivery</strong>
            <p style="margin: 8px 0 0; color: #4a5568; font-size: 0.9rem;">Order before 2 PM for same-day delivery within Kathmandu Valley!</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
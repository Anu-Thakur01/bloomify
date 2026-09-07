<?php
$page_title = 'About Us';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    .about-container { max-width: 1000px; margin: 40px auto; padding: 20px; }
    .about-hero { text-align: center; padding: 60px 40px; background: linear-gradient(135deg, #40916c 0%, #2d6a4f 100%); color: white; border-radius: 16px; margin-bottom: 50px; }
    .about-hero h1 { font-size: 2.8rem; margin-bottom: 15px; font-weight: 700; }
    .about-hero p { font-size: 1.2rem; max-width: 600px; margin: 0 auto; opacity: 0.95; }
    
    .about-section { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-bottom: 30px; }
    .about-section h2 { color: #2d6a4f; font-size: 2rem; margin-bottom: 20px; font-weight: 700; }
    .about-section p { color: #6c757d; line-height: 1.8; font-size: 1.05rem; margin-bottom: 15px; }
    
    .values-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-top: 30px; }
    .value-card { background: #f8f9fa; padding: 30px; border-radius: 10px; text-align: center; transition: transform 0.3s; }
    .value-card:hover { transform: translateY(-5px); }
    .value-icon { font-size: 3rem; margin-bottom: 15px; }
    .value-card h3 { color: #2d6a4f; margin-bottom: 10px; font-size: 1.3rem; }
    .value-card p { color: #6c757d; font-size: 0.95rem; }
    
    .stats-section { background: linear-gradient(135deg, #40916c 0%, #2d6a4f 100%); color: white; padding: 50px 40px; border-radius: 12px; margin: 40px 0; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; text-align: center; }
    .stat-item h3 { font-size: 3rem; margin-bottom: 10px; font-weight: 800; }
    .stat-item p { font-size: 1.1rem; opacity: 0.95; }
    
    .team-section { margin-top: 40px; }
    .team-section h2 { text-align: center; margin-bottom: 40px; }
    
    @media (max-width: 768px) {
        .about-hero h1 { font-size: 2rem; }
        .about-hero p { font-size: 1rem; }
        .about-section { padding: 25px; }
        .about-section h2 { font-size: 1.5rem; }
    }
</style>

<div class="about-container">
    <!-- Hero Section -->
    <div class="about-hero">
        <h1>About Bloomify 🌸</h1>
        <p>Bringing joy and beauty to your life through fresh, handcrafted flower arrangements since 2020.</p>
    </div>
    
    <!-- Our Story -->
    <div class="about-section">
        <h2>Our Story</h2>
        <p>
            Bloomify started with a simple mission: to make beautiful, fresh flowers accessible to everyone. 
            What began as a small flower shop in the heart of Kathmandu has grown into a beloved online 
            flower delivery service serving customers across Nepal.
        </p>
        <p>
            We believe that flowers have the power to express emotions that words cannot capture. Whether 
            it's love, gratitude, sympathy, or celebration, our carefully curated bouquets help you share 
            those special moments with the people who matter most.
        </p>
    </div>
    
    <!-- Our Values -->
    <div class="about-section">
        <h2>Why Choose Bloomify?</h2>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">🌺</div>
                <h3>Fresh Flowers</h3>
                <p>We source the freshest flowers daily from local and international growers</p>
            </div>
            <div class="value-card">
                <div class="value-icon"></div>
                <h3>Handcrafted</h3>
                <p>Each bouquet is carefully designed by our expert florists with love and attention</p>
            </div>
            <div class="value-card">
                <div class="value-icon"></div>
                <h3>Fast Delivery</h3>
                <p>Same-day delivery available across Kathmandu and major cities</p>
            </div>
            <div class="value-card">
                <div class="value-icon"></div>
                <h3>Customer First</h3>
                <p>Your satisfaction is our priority. We ensure quality in every order</p>
            </div>
        </div>
    </div>
    
    <!-- Stats Section -->
    <div class="stats-section">
        <div class="stats-grid">
            <div class="stat-item">
                <h3>5000+</h3>
                <p>Happy Customers</p>
            </div>
            <div class="stat-item">
                <h3>10000+</h3>
                <p>Orders Delivered</p>
            </div>
            <div class="stat-item">
                <h3>50+</h3>
                <p>Flower Varieties</p>
            </div>
            <div class="stat-item">
                <h3>4.9</h3>
                <p>Customer Rating</p>
            </div>
        </div>
    </div>
    
    <!-- What We Offer -->
    <div class="about-section">
        <h2>What We Offer</h2>
        <p>
            At Bloomify, we offer a wide range of floral arrangements for every occasion:
        </p>
        <ul style="color: #6c757d; line-height: 2; font-size: 1.05rem; margin-left: 20px;">
            <li>🌹 <strong>Rose Bouquets</strong> - Express love and romance</li>
            <li>🌺 <strong>Carnation Arrangements</strong> - Elegant and long-lasting beauty</li>
            <li>💐 <strong>Mixed Bouquets</strong> - Colorful combinations for any occasion</li>
            <li>🌸 <strong>Premium Collections</strong> - Luxury arrangements for special moments</li>
            <li>🎁 <strong>Custom Orders</strong> - Personalized designs just for you</li>
        </ul>
    </div>
    
    <!-- Contact CTA -->
    <div class="about-section" style="text-align: center; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
        <h2>Get In Touch</h2>
        <p style="margin-bottom: 25px;">
            Have questions or need a custom arrangement? We'd love to hear from you!
        </p>
        <div style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin-top: 20px;">
            <div>
                <div style="font-size: 2rem; margin-bottom: 10px;"></div>
                <div style="color: #6c757d;">New Road, Kathmandu, Nepal</div>
            </div>
            <div>
                <div style="font-size: 2rem; margin-bottom: 10px;"></div>
                <div style="color: #6c757d;">9812345678</div>
            </div>
            <div>
                <div style="font-size: 2rem; margin-bottom: 10px;"></div>
                <div style="color: #6c757d;">info@bloomify.com</div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
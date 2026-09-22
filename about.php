<?php
$page_title = 'About Us';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* Navigation Styles */
    .about-nav {
        position: sticky;
        top: 80px;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        height: fit-content;
        z-index: 100;
    }
    .about-nav h3 {
        color: #2d6a4f;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 20px;
        font-weight: 700;
    }
    .about-nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .about-nav li {
        margin-bottom: 8px;
    }
    .about-nav a {
        display: block;
        padding: 12px 16px;
        color: #6c757d;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
        font-size: 0.95rem;
    }
    .about-nav a:hover, .about-nav a.active {
        background: #f0f9f4;
        color: #2d6a4f;
        transform: translateX(5px);
    }
    .about-nav a.active {
        background: #2d6a4f;
        color: white;
    }

    /* Main Layout */
    .about-wrapper {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 40px;
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    /* Content Sections */
    .about-section {
        scroll-margin-top: 100px;
        padding: 40px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .about-section:last-child {
        border-bottom: none;
    }
    .section-title {
        font-size: 2.2rem;
        color: #2d6a4f;
        font-weight: 800;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .section-content {
        color: #4a5568;
        line-height: 1.8;
        font-size: 1.05rem;
    }
    .section-content p {
        margin-bottom: 15px;
    }

    /* Feature Cards */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }
    .feature-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
        transition: all 0.3s;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(45, 106, 79, 0.1);
        border-color: #40916c;
    }
    .feature-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        display: block;
    }
    .feature-card h4 {
        color: #2d3748;
        font-size: 1.1rem;
        margin-bottom: 10px;
        font-weight: 700;
    }
    .feature-card p {
        color: #6c757d;
        font-size: 0.95rem;
        line-height: 1.6;
        margin: 0;
    }

    /* Hero Section */
    .about-hero {
        background: linear-gradient(135deg, rgba(45, 106, 79, 0.95), rgba(64, 145, 108, 0.9));
        color: white;
        text-align: center;
        padding: 60px 20px;
        border-radius: 0 0 30px 30px;
        margin-bottom: 40px;
    }
    .about-hero h1 {
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 15px;
        letter-spacing: -1px;
    }
    .about-hero p {
        font-size: 1.15rem;
        opacity: 0.95;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* CTA Section */
    .cta-box {
        background: linear-gradient(135deg, #2d6a4f, #40916c);
        color: white;
        padding: 40px;
        border-radius: 16px;
        text-align: center;
        margin-top: 40px;
    }
    .cta-box h3 {
        font-size: 1.8rem;
        margin-bottom: 15px;
        font-weight: 700;
    }
    .cta-box p {
        opacity: 0.95;
        margin-bottom: 25px;
        font-size: 1.05rem;
    }
    .btn-cta {
        background: #ffd700;
        color: #1b4332;
        padding: 14px 35px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.05rem;
        transition: all 0.3s;
        display: inline-block;
    }
    .btn-cta:hover {
        background: #ffea70;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(255, 215, 0, 0.3);
    }

    /* Mobile Responsive */
    @media (max-width: 900px) {
        .about-wrapper {
            grid-template-columns: 1fr;
        }
        .about-nav {
            position: relative;
            top: 0;
            margin-bottom: 30px;
        }
        .about-nav ul {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .about-nav li {
            margin: 0;
        }
        .about-nav a {
            padding: 10px 16px;
            font-size: 0.9rem;
        }
        .about-hero h1 {
            font-size: 2rem;
        }
    }
</style>

<!-- Hero Section -->
<div class="about-hero">
    <h1>About Bloomify</h1>
    <p>Bringing nature's most beautiful creations to your doorstep with love and care.</p>
</div>

<div class="about-wrapper">
    <!-- Navigation Sidebar -->
    <nav class="about-nav">
        <h3>Quick Links</h3>
        <ul>
            <li><a href="#story" class="nav-link active">Our Story</a></li>
            <li><a href="#blog" class="nav-link">Blog</a></li>
            <li><a href="#careers" class="nav-link">Careers</a></li>
            <li><a href="#wholesale" class="nav-link">Wholesale</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Our Story Section -->
        <section id="story" class="about-section">
            <h2 class="section-title">🌸 Our Story</h2>
            <div class="section-content">
                <p><strong>Bloomify</strong> is an online flower ordering and shopping platform primarily focused on providing a wide variety of fresh flowers and floral arrangements.</p>
                
                <p>Founded with a passion for bringing people closer through the universal language of flowers, we have grown from a small local florist to a trusted online platform serving flower lovers across the nation.</p>
                
                <p>Our mission is simple yet profound: to deliver happiness, one bouquet at a time. We believe that every occasion deserves to be celebrated with beauty, and every emotion deserves to be expressed with the perfect floral arrangement.</p>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <span class="feature-icon">🌿</span>
                        <h4>Fresh & Quality</h4>
                        <p>Hand-picked flowers sourced directly from local growers</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon"></span>
                        <h4>Expert Florists</h4>
                        <p>Skilled artisans crafting beautiful arrangements</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon"></span>
                        <h4>Fast Delivery</h4>
                        <p>Same-day delivery to keep your flowers fresh</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Blog Section -->
        <section id="blog" class="about-section">
            <h2 class="section-title">Our Blog</h2>
            <div class="section-content">
                <p>Stay updated with the latest trends in floral design, flower care tips, and stories about the meaning behind different blooms.</p>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <span class="feature-icon"></span>
                        <h4>Flower Care Tips</h4>
                        <p>Learn how to keep your bouquets fresh longer with expert advice</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon"></span>
                        <h4>Design Inspiration</h4>
                        <p>Discover creative ways to arrange and display flowers</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon"></span>
                        <h4>Occasion Guides</h4>
                        <p>Find the perfect flowers for every celebration</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon"></span>
                        <h4>Seasonal Blooms</h4>
                        <p>Explore what's in season and at its peak beauty</p>
                    </div>
                </div>
                
                <div class="cta-box" style="margin-top: 30px;">
                    <h3>Coming Soon!</h3>
                    <p>Our blog is currently being crafted with love. Stay tuned for flower tips, trends, and inspiration!</p>
                </div>
            </div>
        </section>

        <!-- Careers Section -->
        <section id="careers" class="about-section">
            <h2 class="section-title">Careers</h2>
            <div class="section-content">
                <p>Join our growing team of flower enthusiasts and help us spread joy through the beauty of nature. At Bloomify, we're always looking for passionate individuals who share our love for flowers and customer service.</p>
                
                <h3 style="color: #2d6a4f; margin-top: 25px; margin-bottom: 15px;">Why Work With Us?</h3>
                <ul style="line-height: 2; color: #4a5568;">
                    <li>Work in a creative and positive environment</li>
                    <li>Growth opportunities and skill development</li>
                    <li>Competitive compensation and benefits</li>
                    <li>Employee discounts on beautiful arrangements</li>
                    <li>Collaborative and supportive team culture</li>
                    <li> Make a difference in people's special moments</li>
                </ul>
                
                <h3 style="color: #2d6a4f; margin-top: 25px; margin-bottom: 15px;">Open Positions</h3>
                <div class="features-grid">
                    <div class="feature-card">
                        <span class="feature-icon">‍</span>
                        <h4>Florist Designer</h4>
                        <p>Create stunning arrangements</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon"></span>
                        <h4>Delivery Partner</h4>
                        <p>Bring smiles to customers</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon"></span>
                        <h4>Customer Support</h4>
                        <p>Help customers find perfect flowers</p>
                    </div>
                </div>
                
                <div class="cta-box" style="margin-top: 30px;">
                    <h3>Interested in Joining Us?</h3>
                    <p>Send your resume to <strong>careers@bloomify.com</strong> and tell us why you'd be a great fit!</p>
                </div>
            </div>
        </section>

        <!-- Wholesale Section -->
        <section id="wholesale" class="about-section">
            <h2 class="section-title"> Wholesale</h2>
            <div class="section-content">
                <p>Are you a business owner, event planner, or retailer looking for premium flowers at wholesale prices? Bloomify offers competitive wholesale options for qualified partners.</p>
                
                <h3 style="color: #2d6a4f; margin-top: 25px; margin-bottom: 15px;">Who Can Apply?</h3>
                <ul style="line-height: 2; color: #4a5568;">
                    <li>Retail flower shops and boutiques</li>
                    <li>Event planners and wedding coordinators</li>
                    <li> Hotels and hospitality businesses</li>
                    <li>Corporate offices and venues</li>
                    <li> Online retailers and subscription services</li>
                </ul>
                
                <h3 style="color: #2d6a4f; margin-top: 25px; margin-bottom: 15px;">Wholesale Benefits</h3>
                <div class="features-grid">
                    <div class="feature-card">
                        <span class="feature-icon">💵</span>
                        <h4>Competitive Pricing</h4>
                        <p>Special wholesale rates for bulk orders</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon"></span>
                        <h4>Bulk Orders</h4>
                        <p>Large quantity fulfillment with flexibility</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon"></span>
                        <h4>Priority Service</h4>
                        <p>Dedicated account manager support</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon"></span>
                        <h4>Fresh Guarantee</h4>
                        <p>Premium quality flowers every time</p>
                    </div>
                </div>
                
                <div class="cta-box" style="margin-top: 30px;">
                    <h3>Interested in Wholesale?</h3>
                    <p>Contact our wholesale team at <strong>wholesale@bloomify.com</strong> or call us at <strong>+977-1-XXXXXXX</strong></p>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    // Smooth scrolling for navigation links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            // Update active state
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Smooth scroll to section
            targetSection.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    });

    // Update active link on scroll
    window.addEventListener('scroll', function() {
        let current = '';
        const sections = document.querySelectorAll('.about-section');
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= (sectionTop - 150)) {
                current = section.getAttribute('id');
            }
        });

        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').slice(1) === current) {
                link.classList.add('active');
            }
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
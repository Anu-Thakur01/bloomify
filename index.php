<?php
$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';

$cat_sql = "SELECT * FROM categories LIMIT 4";
$categories = $conn->query($cat_sql);

$prod_sql = "SELECT * FROM products WHERE status = 'active' ORDER BY id DESC";
$products = $conn->query($prod_sql);
?>

<style>
    /* Homepage Specific Spacious UI */
    .hero-section { padding: 60px 20px 40px; text-align: center; }
    .hero-title { font-size: 2.8rem; color: #2d6a4f; font-weight: 800; margin-bottom: 15px; }
    .hero-subtitle { font-size: 1.2rem; color: #6c757d; margin-bottom: 40px; }
    
    .search-container { max-width: 700px; margin: 0 auto; }
    .search-form { display: flex; box-shadow: 0 8px 25px rgba(0,0,0,0.1); border-radius: 50px; overflow: hidden; background: white; }
    .search-input { flex: 1; padding: 18px 30px; border: none; font-size: 1.1rem; outline: none; }
    .search-btn { background: #40916c; color: white; border: none; padding: 18px 40px; cursor: pointer; font-weight: 700; font-size: 1.1rem; transition: background 0.3s; }
    .search-btn:hover { background: #2d6a4f; }
    
    .banner-section { padding: 20px 20px 60px; }
    .banner-img { width: 100%; height: 450px; object-fit: cover; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.15); }

    .categories-section { padding: 60px 20px; background: #f8f9fa; }
    .section-title { text-align: center; color: #2d6a4f; font-size: 2rem; font-weight: 700; margin-bottom: 50px; }
    
    .category-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 30px; max-width: 1200px; margin: 0 auto; }
    .category-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.08); transition: transform 0.3s, box-shadow 0.3s; text-decoration: none; color: inherit; }
    .category-card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(0,0,0,0.15); }
    .category-img { width: 100%; height: 220px; object-fit: cover; transition: transform 0.5s; }
    .category-card:hover .category-img { transform: scale(1.08); }
    .category-info { padding: 25px; text-align: center; }
    .category-name { font-size: 1.3rem; font-weight: 700; color: #2d6a4f; margin-bottom: 10px; }

    .products-section { padding: 80px 20px; max-width: 1400px; margin: 0 auto; }
    .product-grid-spacious { display: grid; grid-template-columns: repeat(5, 1fr); gap: 25px; }
    
    @media (max-width: 1200px) { .product-grid-spacious { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 968px) { .product-grid-spacious { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { 
        .product-grid-spacious { grid-template-columns: repeat(2, 1fr); } 
        .hero-title { font-size: 2rem; }
        .banner-img { height: 250px; }
    }
    @media (max-width: 480px) { .product-grid-spacious { grid-template-columns: 1fr; } }

    .product-card-spacious { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: transform 0.3s, box-shadow 0.3s; display: flex; flex-direction: column; }
    .product-card-spacious:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); }
    .product-img-placeholder { height: 220px; background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%); display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #40916c; overflow: hidden; }
    .product-img-placeholder img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .product-card-spacious:hover .product-img-placeholder img { transform: scale(1.05); }
    .product-info-spacious { padding: 20px; flex: 1; display: flex; flex-direction: column; }
    .product-name-spacious { font-size: 1rem; font-weight: 700; color: #2d3748; margin-bottom: 8px; line-height: 1.4; }
    .product-price-spacious { font-size: 1.2rem; color: #40916c; font-weight: 800; margin-bottom: 12px; }
    .product-stock-spacious { font-size: 0.85rem; color: #6c757d; margin-bottom: 15px; }
    .btn-view { margin-top: auto; padding: 10px; font-size: 0.9rem; }
</style>

<!-- Hero Section -->
<div class="hero-section">
    <h1 class="hero-title">Welcome<?php echo isLoggedIn() ? ', ' . htmlspecialchars($_SESSION['user_name']) : ''; ?> 🌸</h1>
    <p class="hero-subtitle">Find beautiful flowers for every occasion.</p>
    <div class="search-container">
        <form action="products.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search for roses, lilies, bouquets..." class="search-input">
            <button type="submit" class="search-btn">🔍 Search</button>
        </form>
    </div>
</div>

<!-- Banner Section -->
<div class="banner-section">
    <div class="container">
        <img src="<?php echo SITE_URL; ?>/assets/images/banner-flowers.jpg" alt="Beautiful Flowers" class="banner-img">
    </div>
</div>

<!-- Categories Section -->
<div class="categories-section">
    <h2 class="section-title">Featured Categories</h2>
    <div class="category-grid">
        <?php if ($categories && $categories->num_rows > 0): ?>
            <?php 
            $cat_images = ['cat-roses.jpg', 'cat-carnations.jpg', 'cat-mixed.jpg', 'cat-premium.jpg'];
            $i = 0;
            while($cat = $categories->fetch_assoc()): 
            ?>
                <a href="products.php?category=<?php echo $cat['id']; ?>" class="category-card">
                    <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $cat_images[$i] ?? 'cat-mixed.jpg'; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="category-img">
                    <div class="category-info">
                        <h3 class="category-name"><?php echo htmlspecialchars($cat['name']); ?></h3>
                    </div>
                </a>
            <?php $i++; endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Products Section -->
<div class="products-section">
    <h2 class="section-title">Bloomify Flower Shop 🌸</h2>
    <div class="product-grid-spacious">
        <?php if ($products && $products->num_rows > 0): ?>
            <?php while($product = $products->fetch_assoc()): ?>
                <div class="product-card-spacious">
                    <div class="product-img-placeholder">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            🌸
                        <?php endif; ?>
                    </div>
                    <div class="product-info-spacious">
                        <h3 class="product-name-spacious"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price-spacious"><?php echo formatPrice($product['price']); ?></div>
                        <div class="product-stock-spacious">
                            <?php if($product['stock_quantity'] > 0): ?>
                                <span style="color: #2d6a4f; font-weight: 600;">✓ In Stock (<?php echo $product['stock_quantity']; ?>)</span>
                            <?php else: ?>
                                <span style="color: #d90429;"> Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-view">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1/-1; color: #666; padding: 40px;">No products available yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
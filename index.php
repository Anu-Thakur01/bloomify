<?php
$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';

// 1. Fetch ALL categories (includes image and description)
$cat_sql = "SELECT id, name, image, description FROM categories ORDER BY id ASC LIMIT 6";
$categories = $conn->query($cat_sql);

// 2. Fetch active products - LIMITED TO 10 for homepage
$prod_sql = "SELECT id, name, price, stock, stock_quantity, image, status, category_id FROM products WHERE status = 'active' ORDER BY id DESC LIMIT 10";
$products = $conn->query($prod_sql);

$has_products = ($products && $products->num_rows > 0);
$has_categories = ($categories && $categories->num_rows > 0);
?>

<style>
    /* Homepage Specific Spacious UI */
    .hero-section { padding: 80px 20px 60px; text-align: center; background: linear-gradient(to bottom, #f0f9f4, #ffffff); }
    .hero-title { font-size: 3rem; color: #2d6a4f; font-weight: 800; margin-bottom: 15px; letter-spacing: -1px; }
    .hero-subtitle { font-size: 1.2rem; color: #6c757d; margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto; }
    
    .search-container { max-width: 700px; margin: 0 auto; }
    .search-form { display: flex; box-shadow: 0 10px 30px rgba(45, 106, 79, 0.15); border-radius: 50px; overflow: hidden; background: white; border: 1px solid #e2e8f0; }
    .search-input { flex: 1; padding: 18px 30px; border: none; font-size: 1.1rem; outline: none; }
    .search-btn { background: #2d6a4f; color: white; border: none; padding: 18px 40px; cursor: pointer; font-weight: 700; font-size: 1.1rem; transition: background 0.3s; }
    .search-btn:hover { background: #1b4332; }
    
    .banner-section { padding: 0 20px 60px; }
    .banner-img { 
        width: 100%; max-width: 1200px; height: 450px; object-fit: cover; border-radius: 20px; 
        box-shadow: 0 20px 50px rgba(0,0,0,0.1); display: block; margin: 0 auto; 
        background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);
    }

    .categories-section { padding: 80px 20px; background: #f8f9fa; }
    .section-title { text-align: center; color: #2d6a4f; font-size: 2.2rem; font-weight: 800; margin-bottom: 50px; }
    
    .category-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 30px; max-width: 1200px; margin: 0 auto; }
    .category-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.06); transition: transform 0.3s, box-shadow 0.3s; text-decoration: none; color: inherit; border: 1px solid #f0f0f0; }
    .category-card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(45, 106, 79, 0.15); border-color: #40916c; }
    .category-img { width: 100%; height: 220px; object-fit: cover; transition: transform 0.5s; }
    .category-card:hover .category-img { transform: scale(1.08); }
    .category-info { padding: 25px; text-align: center; }
    .category-name { font-size: 1.3rem; font-weight: 700; color: #2d6a4f; margin-bottom: 10px; }
    .category-desc { color: #6c757d; font-size: 0.95rem; line-height: 1.6; margin: 0; }

    .products-section { padding: 80px 20px; max-width: 1400px; margin: 0 auto; }
    .product-grid-spacious { display: grid; grid-template-columns: repeat(5, 1fr); gap: 25px; }
    
    @media (max-width: 1200px) { .product-grid-spacious { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 968px) { .product-grid-spacious { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { 
        .product-grid-spacious { grid-template-columns: repeat(2, 1fr); } 
        .hero-title { font-size: 2.2rem; }
        .banner-img { height: 250px; }
    }
    @media (max-width: 480px) { 
        .product-grid-spacious { grid-template-columns: 1fr; } 
        .search-form { flex-direction: column; border-radius: 16px; }
        .search-input { padding: 15px; text-align: center; }
        .search-btn { border-radius: 0 0 16px 16px; }
    }

    .product-card-spacious { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.06); transition: transform 0.3s, box-shadow 0.3s; display: flex; flex-direction: column; border: 1px solid #f0f0f0; }
    .product-card-spacious:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(45, 106, 79, 0.12); border-color: #40916c; }
    .product-img-placeholder { height: 220px; background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%); display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #40916c; overflow: hidden; }
    .product-img-placeholder img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .product-card-spacious:hover .product-img-placeholder img { transform: scale(1.05); }
    .product-info-spacious { padding: 20px; flex: 1; display: flex; flex-direction: column; }
    .product-name-spacious { font-size: 1.05rem; font-weight: 700; color: #2d3748; margin-bottom: 8px; line-height: 1.4; }
    .product-price-spacious { font-size: 1.25rem; color: #2d6a4f; font-weight: 800; margin-bottom: 12px; }
    .product-stock-spacious { font-size: 0.85rem; color: #6c757d; margin-bottom: 15px; }
    
    .btn-view-all { 
        display: block; width: fit-content; margin: 50px auto 0; padding: 15px 40px; 
        background: white; color: #2d6a4f; border: 2px solid #2d6a4f; border-radius: 50px; 
        text-decoration: none; font-weight: 700; font-size: 1.1rem; transition: all 0.3s; 
    }
    .btn-view-all:hover { background: #2d6a4f; color: white; transform: translateY(-3px); box-shadow: 0 10px 25px rgba(45, 106, 79, 0.2); }
</style>

<!-- Hero Section -->
<div class="hero-section">
    <h1 class="hero-title">Welcome<?php echo isLoggedIn() ? ', ' . htmlspecialchars($_SESSION['user_name']) : ''; ?> </h1>
    <p class="hero-subtitle">Find beautiful, fresh flowers for every occasion. Delivered with love and care.</p>
    <div class="search-container">
        <form action="products.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search for roses, lilies, bouquets..." class="search-input" required>
            <button type="submit" class="search-btn">Search</button>
        </form>
    </div>
</div>

<!-- Banner Section 
<div class="banner-section">
    <img src="<?php echo SITE_URL; ?>/assets/images/banner-flowers.jpg" alt="Beautiful Flowers" class="banner-img" onerror="this.style.background='linear-gradient(135deg, #2d6a4f 0%, #40916c 100%)'; this.style.display='flex'; this.style.alignItems='center'; this.style.justifyContent='center'; this.innerHTML='<span style=\'color:white; font-size:2rem; font-weight:800;\'> Bloomify</span>';">
</div>
-->

<!-- Categories Section -->
<div class="categories-section">
    <h2 class="section-title">Featured Categories</h2>
    <div class="category-grid">
        <?php if ($has_categories): ?>
            <?php while($cat = $categories->fetch_assoc()): ?>
                <a href="products.php?category=<?php echo $cat['id']; ?>" class="category-card">
                    <?php 
                    $cat_image = !empty($cat['image']) ? $cat['image'] : '';
                    $colors = ['e8f5e9', 'fff3e0', 'fce4ec', 'e3f2fd', 'f3e5f5'];
                    $placeholder_color = $colors[array_rand($colors)];
                    $img_src = !empty($cat_image) ? SITE_URL . '/assets/images/' . htmlspecialchars($cat_image) : 'https://via.placeholder.com/400x300/' . $placeholder_color . '/2d6a4f?text=' . urlencode($cat['name']);
                    ?>
                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="category-img">
                    <div class="category-info">
                        <h3 class="category-name"><?php echo htmlspecialchars($cat['name']); ?></h3>
                        <?php if (!empty($cat['description'])): ?>
                            <p class="category-desc"><?php echo htmlspecialchars(substr($cat['description'], 0, 80)) . (strlen($cat['description']) > 80 ? '...' : ''); ?></p>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1/-1; color: #6c757d; font-size: 1.1rem;">No categories available yet. Check back soon!</p>
        <?php endif; ?>
    </div>
    
    <?php if ($has_categories): ?>
        <a href="categories.php" class="btn-view-all">View All Categories →</a>
    <?php endif; ?>
</div>

<!-- Products Section -->
<div class="products-section">
    <h2 class="section-title">Fresh Arrivals </h2>
    <div class="product-grid-spacious">
        <?php if ($has_products): ?>
            <?php while($product = $products->fetch_assoc()): ?>
                <div class="product-card-spacious">
                    <div class="product-img-placeholder">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.parentElement.innerHTML='🌸'">
                        <?php else: ?>
                            🌸
                        <?php endif; ?>
                    </div>
                    <div class="product-info-spacious">
                        <h3 class="product-name-spacious"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price-spacious"><?php echo formatPrice($product['price']); ?></div>
                        
                        <!-- ✅ STOCK DISPLAY -->
                        <div class="product-stock-spacious">
                            <?php 
                            $stock_qty = (int)($product['stock'] ?? $product['stock_quantity'] ?? 0);
                            if ($stock_qty > 0): ?>
                                <span style="color: #2d6a4f; font-weight: 600;">✓ In Stock (<?php echo $stock_qty; ?>)</span>
                            <?php else: ?>
                                <span style="color: #d90429; font-weight: 600;">✕ Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- ✅ NEW BUTTON LAYOUT -->
                        <div style="display: flex; flex-direction: column; gap: 8px; margin-top: auto;">
                            <!-- Top Row: Cart + Wishlist -->
                            <div style="display: flex; gap: 8px;">
                                <form method="POST" action="add-to-cart.php" style="flex: 1; margin: 0;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="add_to_cart" style="width: 100%; padding: 10px; background: #40916c; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.85rem;">
                                         Cart
                                    </button>
                                </form>
                                
                                <?php if (isLoggedIn()): ?>
                                    <?php
                                    $check_sql = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?";
                                    $check_stmt = $conn->prepare($check_sql);
                                    $check_stmt->bind_param("ii", $_SESSION['user_id'], $product['id']);
                                    $check_stmt->execute();
                                    $in_wishlist = $check_stmt->get_result()->num_rows > 0;
                                    ?>
                                    <form method="POST" action="wishlist-action.php" style="margin: 0;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="action" value="<?php echo $in_wishlist ? 'remove' : 'add'; ?>">
                                        <button type="submit" style="width: 45px; height: 38px; background: <?php echo $in_wishlist ? '#fee2e2' : 'white'; ?>; color: <?php echo $in_wishlist ? '#d90429' : '#2d6a4f'; ?>; border: 2px solid <?php echo $in_wishlist ? '#d90429' : '#2d6a4f'; ?>; border-radius: 6px; cursor: pointer; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                                            <?php echo $in_wishlist ? '❤️' : '♡'; ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php" style="width: 45px; height: 38px; background: white; color: #2d6a4f; border: 2px solid #2d6a4f; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; text-decoration: none;">
                                        ♡
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Bottom Row: View Details -->
                            <a href="product-details.php?id=<?php echo $product['id']; ?>" style="width: 100%; padding: 10px; background: white; color: #2d6a4f; border: 2px solid #40916c; border-radius: 6px; font-weight: 600; text-align: center; text-decoration: none; font-size: 0.9rem; display: block; box-sizing: border-box;">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1/-1; color: #6c757d; padding: 40px; font-size: 1.1rem;">No products available yet.</p>
        <?php endif; ?>
    </div>
    
    <?php if ($has_products): ?>
        <a href="products.php" class="btn-view-all">View All Products →</a>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php
$page_title = 'Products';
require_once __DIR__ . '/includes/header.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$sql = "SELECT p.id, p.name, p.price, p.discount_percentage, p.stock, p.stock_quantity, p.image, p.status, p.category_id, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.status = 'active'";

$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}
if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}
$sql .= " ORDER BY p.id DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

$categories_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$categories = $conn->query($categories_sql);
?>

<style>
    .products-container { max-width: 1200px; margin: 30px auto; padding: 20px; }
    .products-header { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); margin-bottom: 30px; }
    .products-title { font-size: 2rem; color: #2d6a4f; margin-bottom: 20px; font-weight: 700; text-align: center; }
    
    .filters { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
    .search-box { flex: 1; min-width: 250px; position: relative; }
    .search-box input { width: 100%; padding: 12px 45px 12px 15px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; transition: all 0.3s; }
    .search-box input:focus { outline: none; border-color: #40916c; box-shadow: 0 0 0 3px rgba(64, 145, 108, 0.1); }
    .search-box button { position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: #40916c; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; transition: background 0.3s; font-size: 1.1rem; }
    .search-box button:hover { background: #2d6a4f; }
    
    .category-filter { min-width: 200px; }
    .category-filter select { width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; background: white; cursor: pointer; transition: all 0.3s; }
    .category-filter select:focus { outline: none; border-color: #40916c; }
    
    .results-count { color: #6c757d; font-size: 1rem; margin-bottom: 20px; display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }
    .results-count strong { color: #2d6a4f; }
    .results-count a { color: #40916c; text-decoration: none; font-weight: 600; transition: color 0.3s; }
    .results-count a:hover { color: #2d6a4f; text-decoration: underline; }
    
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 25px; }
    
    /* ✅ COMPACT PRODUCT CARD STYLES */
    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        border: 1px solid #f0f0f0;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(45, 106, 79, 0.12);
        border-color: #40916c;
    }
    .product-img {
        height: 180px; /* Reduced from 220px */
        background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem; /* Reduced from 4rem */
        color: #40916c;
        overflow: hidden;
    }
    .product-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .product-card:hover .product-img img { transform: scale(1.05); }
    
    .product-info {
        padding: 15px; /* Reduced from 20px */
        display: flex;
        flex-direction: column;
    }
    .product-name {
        font-size: 0.95rem; /* Reduced from 1.05rem */
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 6px; /* Reduced from 8px */
        line-height: 1.4;
    }
    .product-category {
        font-size: 0.8rem; /* Reduced from 0.85rem */
        color: #6c757d;
        margin-bottom: 8px; /* Reduced from 10px */
    }
    .product-price {
        font-size: 1.1rem; /* Reduced from 1.3rem */
        color: #2d6a4f;
        font-weight: 800;
        margin-bottom: 8px; /* Reduced from 12px */
    }
    .product-stock {
        font-size: 0.8rem; /* Reduced from 0.85rem */
        color: #6c757d;
        margin-bottom: 12px; /* Reduced from 15px */
    }
    
    .no-results { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); }
    .no-results h3 { color: #2d6a4f; margin-bottom: 15px; font-size: 1.5rem; }
    .no-results p { color: #6c757d; margin-bottom: 25px; }
    
    @media (max-width: 768px) {
        .filters { flex-direction: column; }
        .search-box, .category-filter { min-width: 100%; }
        .product-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
    }
</style>

<div class="products-container">
    <div class="products-header">
        <h1 class="products-title">
            <?php if (!empty($search)): ?>
                Search Results for "<?php echo htmlspecialchars($search); ?>"
            <?php elseif ($category_id > 0): ?>
                <?php
                $cat_stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
                $cat_stmt->bind_param("i", $category_id);
                $cat_stmt->execute();
                $cat = $cat_stmt->get_result()->fetch_assoc();
                echo htmlspecialchars($cat['name'] ?? 'Products');
                ?>
            <?php else: ?>
                All Products
            <?php endif; ?>
        </h1>
        <div class="filters">
            <div class="search-box">
                <form method="GET" action="products.php">
                    <?php if ($category_id > 0): ?>
                        <input type="hidden" name="category" value="<?php echo $category_id; ?>">
                    <?php endif; ?>
                    <input type="text" name="search" placeholder="Search flowers..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"></button>
                </form>
            </div>
            <div class="category-filter">
                <form method="GET" action="products.php" id="categoryForm">
                    <?php if (!empty($search)): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <?php endif; ?>
                    <select name="category" onchange="document.getElementById('categoryForm').submit()">
                        <option value="0">All Categories</option>
                        <?php
                        $categories->data_seek(0);
                        while ($cat = $categories->fetch_assoc()):
                        ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
            </div>
        </div>
        <div class="results-count">
            <?php if ($category_id > 0): ?>
                <a href="categories.php">← Back to Categories</a>
            <?php else: ?>
                <a href="index.php">← Back to Home</a>
            <?php endif; ?>
            <span>Showing <strong><?php echo $products->num_rows; ?></strong> product<?php echo ($products->num_rows != 1) ? 's' : ''; ?></span>
            <?php if (!empty($search) || $category_id > 0): ?>
                <span>|</span>
                <a href="products.php">Clear Filters</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($products->num_rows > 0): ?>
        <div class="product-grid">
            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-img">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.parentElement.innerHTML='🌸'">
                        <?php else: ?>
                            🌸
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <?php if (!empty($product['category_name'])): ?>
                            <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                        <?php endif; ?>
                        <div class="product-price">
                            <?php
                            $original_price = $product['price'];
                            $discount = (int)($product['discount_percentage'] ?? 0);
                            $discounted_price = $original_price - ($original_price * $discount / 100);
                            ?>
                            <?php if ($discount > 0): ?>
                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    <span style="text-decoration: line-through; color: #9ca3af; font-size: 0.9rem;">Rs. <?php echo number_format($original_price, 2); ?></span>
                                    <span style="color: #dc2626; font-weight: 800; font-size: 1.1rem;">Rs. <?php echo number_format($discounted_price, 2); ?></span>
                                    <span style="background: #dc2626; color: white; padding: 3px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;"><?php echo $discount; ?>% OFF</span>
                                </div>
                            <?php else: ?>
                                <span style="color: #2d6a4f; font-weight: 800; font-size: 1.1rem;">Rs. <?php echo number_format($original_price, 2); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-stock">
                            <?php 
                            $stock_qty = (int)($product['stock'] ?? $product['stock_quantity'] ?? 0);
                            if ($stock_qty > 0): ?>
                                <span style="color: #2d6a4f; font-weight: 600;">✓ In Stock (<?php echo $stock_qty; ?>)</span>
                            <?php else: ?>
                                <span style="color: #d90429; font-weight: 600;">✕ Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px; margin-top: auto;">
                            <div style="display: flex; gap: 8px;">
                                <form method="POST" action="add-to-cart.php" style="flex: 1; margin: 0;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="add_to_cart" style="width: 100%; padding: 8px; background: #40916c; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.85rem;"> Cart</button>
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
                                        <button type="submit" style="width: 38px; height: 34px; background: <?php echo $in_wishlist ? '#fee2e2' : 'white'; ?>; color: <?php echo $in_wishlist ? '#d90429' : '#2d6a4f'; ?>; border: 2px solid <?php echo $in_wishlist ? '#d90429' : '#2d6a4f'; ?>; border-radius: 6px; cursor: pointer; font-size: 1rem; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                                            <?php echo $in_wishlist ? '❤️' : '♡'; ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php" style="width: 38px; height: 34px; background: white; color: #2d6a4f; border: 2px solid #2d6a4f; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 1rem; text-decoration: none;">♡</a>
                                <?php endif; ?>
                            </div>
                            <a href="product-details.php?id=<?php echo $product['id']; ?>" style="width: 100%; padding: 8px; background: white; color: #2d6a4f; border: 2px solid #40916c; border-radius: 6px; font-weight: 600; text-align: center; text-decoration: none; font-size: 0.85rem; display: block; box-sizing: border-box;">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            <div style="font-size: 4rem; margin-bottom: 20px;">🔍</div>
            <h3>No Products Found</h3>
            <p>We couldn't find any products matching your search criteria.</p>
            <a href="products.php" style="display: inline-block; padding: 12px 30px; background: #40916c; color: white; border-radius: 6px; text-decoration: none; font-weight: 600;">View All Products</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
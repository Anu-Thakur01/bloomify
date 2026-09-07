<?php
$page_title = 'Products';
require_once __DIR__ . '/includes/header.php';

// Initialize filters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Build SQL query
$sql = "SELECT p.*, c.name as category_name 
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

// Fetch categories for filter dropdown
$categories_sql = "SELECT * FROM categories ORDER BY name";
$categories = $conn->query($categories_sql);
?>

<style>
    .products-container { max-width: 1200px; margin: 30px auto; padding: 20px; }
    .products-header { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-bottom: 30px; }
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
    .product-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: transform 0.3s, box-shadow 0.3s; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.12); }
    .product-img { height: 220px; background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%); display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #40916c; overflow: hidden; }
    .product-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .product-card:hover .product-img img { transform: scale(1.05); }
    .product-info { padding: 20px; }
    .product-name { font-size: 1.05rem; font-weight: 700; color: #2d3748; margin-bottom: 8px; line-height: 1.4; }
    .product-category { font-size: 0.85rem; color: #6c757d; margin-bottom: 10px; }
    .product-price { font-size: 1.3rem; color: #40916c; font-weight: 800; margin-bottom: 12px; }
    .product-stock { font-size: 0.85rem; color: #6c757d; margin-bottom: 15px; }
    .btn-view { width: 100%; padding: 10px; background: #40916c; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; transition: all 0.3s; }
    .btn-view:hover { background: #2d6a4f; transform: translateY(-2px); }
    
    .no-results { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
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
                    <button type="submit">🔍</button>
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
                        while($cat = $categories->fetch_assoc()): 
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
            <a href="index.php">← Back to Home</a>
            <span>Showing <strong><?php echo $products->num_rows; ?></strong> product<?php echo ($products->num_rows != 1) ? 's' : ''; ?></span>
            <?php if (!empty($search) || $category_id > 0): ?>
                <span>|</span>
                <a href="products.php">Clear Filters</a>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($products->num_rows > 0): ?>
        <div class="product-grid">
            <?php while($product = $products->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-img">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            🌸
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <?php if (!empty($product['category_name'])): ?>
                            <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                        <?php endif; ?>
                        <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                        <div class="product-stock">
                            <?php if($product['stock_quantity'] > 0): ?>
                                <span style="color: #2d6a4f; font-weight: 600;">✓ In Stock (<?php echo $product['stock_quantity']; ?>)</span>
                            <?php else: ?>
                                <span style="color: #d90429;"> Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn-view">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            <div style="font-size: 4rem; margin-bottom: 20px;">🔍</div>
            <h3>No Products Found</h3>
            <p>We couldn't find any products matching your search criteria.</p>
            <a href="products.php" class="btn btn-primary" style="width: auto; padding: 12px 30px;">View All Products</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
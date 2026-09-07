<?php
$page_title = 'Product Details';
require_once __DIR__ . '/includes/header.php';

// --- LOGIN CHECK ADDED HERE ---
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view product details.', 'danger');
}
// ------------------------------

// Get product ID from URL
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$product_id = (int)$_GET['id'];

// Fetch product details
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ? AND p.status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    redirect('index.php', 'Product not found!', 'danger');
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = (int)$_POST['quantity'];
    if ($quantity > 0 && $quantity <= $product['stock_quantity']) {
        addToCart($product['id'], $quantity);
        redirect('product-details.php?id=' . $product_id, 'Added to cart successfully!', 'success');
    } else {
        redirect('product-details.php?id=' . $product_id, 'Invalid quantity or out of stock!', 'danger');
    }
}
?>

<style>
    .product-detail-container { max-width: 1000px; margin: 40px auto; padding: 40px 20px; }
    
    .product-detail-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        padding: 40px;
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 50px;
        align-items: start;
    }
    
    .product-image-large {
        width: 100%;
        height: 450px;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8rem;
        color: #40916c;
        overflow: hidden;
    }
    
    .product-image-large img { width: 100%; height: 100%; object-fit: cover; }
    
    .product-title { font-size: 2.2rem; color: #2d6a4f; margin-bottom: 15px; font-weight: 700; }
    
    .product-rating { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .stars { color: #ffd700; font-size: 1.2rem; }
    .rating-count { color: #6c757d; font-size: 0.95rem; }
    
    .product-price-large { font-size: 2rem; color: #40916c; font-weight: 800; margin-bottom: 15px; }
    
    .stock-badge {
        display: inline-block;
        padding: 6px 15px;
        background: #d4edda;
        color: #155724;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 25px;
    }
    
    .product-description { color: #6c757d; line-height: 1.8; margin-bottom: 30px; font-size: 1rem; }
    
    .quantity-selector { margin-bottom: 25px; }
    .quantity-selector label { display: block; margin-bottom: 8px; font-weight: 600; color: #2d3748; }
    .quantity-input { width: 100px; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1rem; }
    
    .action-buttons { display: flex; gap: 15px; margin-bottom: 25px; }
    
    .btn-add-cart {
        flex: 1; padding: 14px; background: #40916c; color: white; border: none;
        border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s;
    }
    .btn-add-cart:hover { background: #2d6a4f; transform: translateY(-2px); }
    
    .btn-buy-now {
        flex: 1; padding: 14px; background: #ffd700; color: #2d6a4f; border: none;
        border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: all 0.3s;
    }
    .btn-buy-now:hover { background: #ffb703; transform: translateY(-2px); }
    
    .btn-back {
        display: inline-block; padding: 10px 25px; background: #6c757d; color: white;
        text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s;
    }
    .btn-back:hover { background: #5a6268; }
    
    @media (max-width: 768px) {
        .product-detail-card { grid-template-columns: 1fr; gap: 30px; }
        .product-image-large { height: 300px; }
    }
</style>

<div class="product-detail-container">
    <div class="product-detail-card">
        <!-- Product Image -->
        <div class="product-image-large">
            <?php if (!empty($product['image'])): ?>
                <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php else: ?>
                🌸
            <?php endif; ?>
        </div>
        
        <!-- Product Details -->
        <div>
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="product-rating">
                <span class="stars">★★★★★</span>
                <span class="rating-count">(4.9)</span>
            </div>
            
            <div class="product-price-large"><?php echo formatPrice($product['price']); ?></div>
            
            <?php if ($product['stock_quantity'] > 0): ?>
                <span class="stock-badge">✓ In Stock (<?php echo $product['stock_quantity']; ?>)</span>
            <?php else: ?>
                <span class="stock-badge" style="background: #f8d7da; color: #721c24;">Out of Stock</span>
            <?php endif; ?>
            
            <div class="product-description">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>
            
            <?php if ($product['stock_quantity'] > 0): ?>
               <form method="POST" action="add-to-cart.php">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <div class="quantity-selector">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" 
                    max="<?php echo $product['stock_quantity']; ?>" class="quantity-input">
                    </div>
    
                <div class="action-buttons">
                    <button type="submit" name="add_to_cart" class="btn-add-cart">🛒 Add To Cart</button>
                    <button type="button" class="btn-buy-now" onclick="alert('Checkout coming soon!')">Buy Now</button>
                </div>
                </form>
            <?php endif; ?>
            
            <a href="index.php" class="btn-back">← Back to Shop</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
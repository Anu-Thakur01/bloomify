<?php
$page_title = 'Product Details';
require_once __DIR__ . '/includes/header.php';

// 1. Login Check
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view product details.', 'danger');
}

// 2. Get product ID from URL
if (!isset($_GET['id'])) {
    redirect('index.php', 'Product not found!', 'danger');
}

$product_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// 3. Fetch product details
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

// 4. Fetch approved reviews for this product
$reviews_sql = "SELECT r.*, u.name as user_name 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = ? AND r.status = 'approved' 
                ORDER BY r.created_at DESC";
$reviews_stmt = $conn->prepare($reviews_sql);
$reviews_stmt->bind_param("i", $product_id);
$reviews_stmt->execute();
$reviews = $reviews_stmt->get_result();

// 5. Calculate average rating
$avg_sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
            FROM reviews 
            WHERE product_id = ? AND status = 'approved'";
$avg_stmt = $conn->prepare($avg_sql);
$avg_stmt->bind_param("i", $product_id);
$avg_stmt->execute();
$avg_result = $avg_stmt->get_result()->fetch_assoc();
$avg_rating = round($avg_result['avg_rating'], 1);
$total_reviews = $avg_result['total_reviews'];

// 6. Check if user has purchased this product (to show review form)
$purchase_check_sql = "SELECT o.id FROM orders o 
                       JOIN order_items oi ON o.id = oi.order_id 
                       WHERE o.user_id = ? AND oi.product_id = ? AND o.payment_status = 'success'";
$purchase_stmt = $conn->prepare($purchase_check_sql);
$purchase_stmt->bind_param("ii", $user_id, $product_id);
$purchase_stmt->execute();
$can_review = $purchase_stmt->get_result()->num_rows > 0;

// 7. Check if user already reviewed this product
$already_reviewed = false;
if ($can_review) {
    $check_review_sql = "SELECT id FROM reviews WHERE user_id = ? AND product_id = ?";
    $check_stmt = $conn->prepare($check_review_sql);
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $already_reviewed = $check_stmt->get_result()->num_rows > 0;
}

// 8. Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = (int)$_POST['quantity'];
    if ($quantity > 0 && $quantity <= ($product['stock'] ?? $product['stock_quantity'] ?? 0)) {
        addToCart($product['id'], $quantity);
        redirect('product-details.php?id=' . $product_id, 'Added to cart successfully! 🛒', 'success');
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
    
    .action-buttons { display: flex; flex-direction: column; gap: 12px; margin-bottom: 25px; }
    .action-buttons-row { display: flex; gap: 12px; }
    
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
    
    /* ✅ REVIEWS SECTION STYLES */
    .reviews-section {
        margin-top: 50px;
        padding-top: 40px;
        border-top: 2px solid #f0f0f0;
    }
    .reviews-title { font-size: 1.8rem; color: #2d6a4f; margin-bottom: 30px; font-weight: 700; }
    
    .review-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .reviewer-name { font-weight: 700; color: #2d3748; font-size: 1rem; }
    .review-date { color: #6c757d; font-size: 0.85rem; }
    .review-rating { color: #ffd700; margin-bottom: 10px; font-size: 1.1rem; }
    .review-comment { color: #4a5568; line-height: 1.6; font-size: 0.95rem; }
    
    .admin-reply {
        margin-top: 15px; 
        padding: 15px; 
        background: #f0f9f4; 
        border-left: 4px solid #40916c; 
        border-radius: 0 8px 8px 0;
    }
    .admin-reply strong { color: #2d6a4f; font-size: 0.9rem; }
    .admin-reply p { margin: 8px 0 0 0; color: #4a5568; font-size: 0.95rem; }
    
    .review-form {
        background: #f0f9f4;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
    }
    .form-group-review { margin-bottom: 15px; }
    .form-group-review label { display: block; margin-bottom: 8px; font-weight: 600; color: #2d3748; }
    .star-rating { display: flex; gap: 5px; font-size: 1.5rem; cursor: pointer; }
    .star-rating input { display: none; }
    .star-rating label { color: #d1d5db; cursor: pointer; transition: color 0.2s; }
    .star-rating label:hover,
    .star-rating input:checked ~ label { color: #ffd700; }
    .textarea-review { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; min-height: 100px; resize: vertical; }
    .textarea-review:focus { outline: none; border-color: #40916c; }
    .btn-submit-review { background: #40916c; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
    .btn-submit-review:hover { background: #2d6a4f; }
    
    .no-reviews { text-align: center; padding: 40px; color: #6c757d; font-style: italic; }
    
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
                <span class="stars">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <?php if($i <= $avg_rating): ?>★<?php else: ?>☆<?php endif; ?>
                    <?php endfor; ?>
                </span>
                <span class="rating-count"><?php echo $avg_rating; ?> (<?php echo $total_reviews; ?> reviews)</span>
            </div>
            
            <div class="product-price-large"><?php echo formatPrice($product['price']); ?></div>
            
            <?php 
            $stock_qty = (int)($product['stock'] ?? $product['stock_quantity'] ?? 0);
            if ($stock_qty > 0): ?>
                <span class="stock-badge">✓ In Stock (<?php echo $stock_qty; ?>)</span>
            <?php else: ?>
                <span class="stock-badge" style="background: #f8d7da; color: #721c24;">✕ Out of Stock</span>
            <?php endif; ?>
            
            <div class="product-description">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>
            
            <?php if ($stock_qty > 0): ?>
                <form method="POST" action="product-details.php?id=<?php echo $product_id; ?>">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <div class="quantity-selector">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" 
                        max="<?php echo $stock_qty; ?>" class="quantity-input">
                    </div>
    
                    <div class="action-buttons">
                        <div class="action-buttons-row">
                            <button type="submit" name="add_to_cart" class="btn-add-cart"> Add To Cart</button>
                            <button type="button" class="btn-buy-now" onclick="window.location.href='checkout.php'"> Buy Now</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
            
            <a href="products.php" class="btn-back">← Back to Shop</a>
        </div>
    </div>
    
    <!-- ✅ REVIEWS SECTION -->
    <div class="reviews-section">
        <h2 class="reviews-title">Customer Reviews (<?php echo $total_reviews; ?>)</h2>
        
        <!-- Write Review Form (only if purchased and not already reviewed) -->
        <?php if ($can_review && !$already_reviewed): ?>
            <div class="review-form">
                <h3 style="margin-top: 0; color: #2d6a4f; margin-bottom: 15px;"> Write a Review</h3>
                <form method="POST" action="submit-review.php">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    
                    <div class="form-group-review">
                        <label>Your Rating *</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" required>
                            <label for="star5">★</label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4">★</label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3">★</label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2">★</label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1">★</label>
                        </div>
                    </div>
                    
                    <div class="form-group-review">
                        <label>Your Review *</label>
                        <textarea name="comment" class="textarea-review" placeholder="Share your experience with this product..." required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit-review">Submit Review</button>
                </form>
            </div>
        <?php elseif ($already_reviewed): ?>
            <div style="background: #e0f2e9; padding: 15px; border-radius: 8px; margin-bottom: 30px; color: #2d6a4f; text-align: center;">
                ✓ You have already reviewed this product. Thank you!
            </div>
        <?php endif; ?>
        
        <!-- Display Reviews -->
        <?php if ($reviews->num_rows > 0): ?>
            <?php while($review = $reviews->fetch_assoc()): ?>
                <div class="review-card">
                    <div class="review-header">
                        <span class="reviewer-name"><?php echo htmlspecialchars($review['user_name']); ?></span>
                        <span class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                    </div>
                    <div class="review-rating">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <?php if($i <= $review['rating']): ?>★<?php else: ?>☆<?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <div class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></div>
                    
                    <!-- ✅ SHOW ADMIN REPLY IF EXISTS -->
                    <?php if (!empty($review['admin_reply'])): ?>
                        <div class="admin-reply">
                            <strong>🌸 Bloomify Team Reply:</strong>
                            <p><?php echo nl2br(htmlspecialchars($review['admin_reply'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-reviews">
                <p style="font-size: 3rem; margin-bottom: 10px;">💬</p>
                <p>No reviews yet. Be the first to review this product!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
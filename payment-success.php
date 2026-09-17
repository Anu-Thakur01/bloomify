<?php
$page_title = 'Payment Successful';
require_once __DIR__ . '/includes/header.php';

// 1. Check if order number is provided
if (!isset($_GET['order'])) {
    redirect('index.php', 'Invalid access.', 'danger');
}

$order_number = sanitize($_GET['order']);
$user_id = $_SESSION['user_id'];

// 2. Fetch order details
$sql = "SELECT * FROM orders WHERE order_number = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $order_number, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    redirect('index.php', 'Order not found.', 'danger');
}

// 3. Fetch order items
$items_sql = "SELECT oi.*, p.name, p.image 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $order['id']);
$items_stmt->execute();
$items = $items_stmt->get_result();
?>

<style>
    .success-container { max-width: 700px; margin: 40px auto; padding: 20px; }
    .success-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        padding: 50px 40px;
        text-align: center;
    }
    .success-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #40916c 0%, #2d6a4f 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        font-size: 3rem;
        color: white;
        animation: scaleIn 0.5s ease;
    }
    @keyframes scaleIn {
        0% { transform: scale(0); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    .success-title { font-size: 2.2rem; color: #2d6a4f; margin-bottom: 15px; font-weight: 800; }
    .success-message { color: #6c757d; font-size: 1.1rem; margin-bottom: 35px; }
    
    .order-details {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        margin: 30px 0;
        text-align: left;
    }
    .order-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    .order-row:last-child { border-bottom: none; }
    .order-label { color: #6c757d; font-weight: 600; }
    .order-value { color: #2d3748; font-weight: 700; }
    .status-pending { color: #f59e0b; }
    .status-success { color: #10b981; }
    
    .purchased-products {
        margin: 30px 0;
        text-align: left;
    }
    .products-title { font-size: 1.2rem; color: #2d6a4f; margin-bottom: 15px; font-weight: 700; }
    .product-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: white;
        border-radius: 8px;
        margin-bottom: 10px;
        border: 1px solid #e2e8f0;
    }
    .product-item img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .product-info { flex: 1; }
    .product-name { font-weight: 600; color: #2d3748; margin-bottom: 5px; }
    .product-qty { color: #6c757d; font-size: 0.9rem; }
    
    .action-buttons { display: flex; gap: 15px; justify-content: center; margin-top: 35px; flex-wrap: wrap; }
    .btn-my-orders {
        padding: 14px 30px;
        background: white;
        color: #2d6a4f;
        border: 2px solid #2d6a4f;
        border-radius: 8px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s;
    }
    .btn-my-orders:hover { background: #f0f9f4; transform: translateY(-2px); }
    
    .btn-continue {
        padding: 14px 30px;
        background: linear-gradient(135deg, #40916c 0%, #2d6a4f 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s;
    }
    .btn-continue:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(64, 145, 108, 0.3); }
    
    .btn-write-review {
        display: inline-block;
        padding: 8px 16px;
        background: #40916c;
        color: white;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        margin-left: 10px;
        transition: all 0.3s;
    }
    .btn-write-review:hover { background: #2d6a4f; transform: translateY(-1px); }
</style>

<div class="success-container">
    <div class="success-card">
        <div class="success-icon">✓</div>
        <h1 class="success-title">Payment Successful!</h1>
        <p class="success-message">Your order has been placed and payment is confirmed.</p>
        
        <div class="order-details">
            <div class="order-row">
                <span class="order-label">Order ID</span>
                <span class="order-value"><?php echo htmlspecialchars($order['order_number']); ?></span>
            </div>
            <div class="order-row">
                <span class="order-label">Total Amount</span>
                <span class="order-value"><?php echo formatPrice($order['total_amount']); ?></span>
            </div>
            <div class="order-row">
                <span class="order-label">Payment Method</span>
                <span class="order-value"><?php echo htmlspecialchars($order['payment_method']); ?></span>
            </div>
            <div class="order-row">
                <span class="order-label">Current Status</span>
                <span class="order-value <?php echo $order['delivery_status'] === 'pending' ? 'status-pending' : 'status-success'; ?>">
                    ✓ <?php echo ucfirst($order['delivery_status']); ?>
                </span>
            </div>
        </div>
        
        <!-- Purchased Products Section -->
        <?php if ($items->num_rows > 0): ?>
            <div class="purchased-products">
                <h3 class="products-title">Products in this Order:</h3>
                <?php while($item = $items->fetch_assoc()): ?>
                    <div class="product-item">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php else: ?>
                            <div style="width: 60px; height: 60px; background: #f0f9f4; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">🌸</div>
                        <?php endif; ?>
                        <div class="product-info">
                            <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="product-qty">Qty: <?php echo $item['quantity']; ?></div>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/product-details.php?id=<?php echo $item['product_id']; ?>" class="btn-write-review">
                            Write Review
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
        
        <div class="action-buttons">
            <a href="<?php echo SITE_URL; ?>/index.php" class="btn-my-orders">Go to Home</a>
            <a href="<?php echo SITE_URL; ?>/my-orders.php" class="btn-continue">My Orders</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
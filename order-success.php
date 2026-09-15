<?php
$page_title = 'Order Success';
require_once __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view your orders.', 'danger');
}

if (!isset($_GET['order'])) {
    redirect('index.php', 'Invalid order.', 'danger');
}

$order_number = $_GET['order'];

$sql = "SELECT o.*, u.email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.order_number = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $order_number, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    redirect('index.php', 'Order not found.', 'danger');
}

$items_sql = "SELECT oi.*, p.name as product_name 
              FROM order_items oi 
              LEFT JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_sql);
$stmt->bind_param("i", $order['id']);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<style>
    .success-container { max-width: 650px; margin: 20px auto; padding: 15px; }
    .success-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    .success-icon { width: 60px; height: 60px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; }
    .success-icon svg { width: 35px; height: 35px; fill: white; }
    .success-title { font-size: 1.5rem; color: #28a745; margin-bottom: 8px; font-weight: 700; text-align: center; }
    .success-message { color: #6c757d; font-size: 0.9rem; margin-bottom: 20px; text-align: center; }
    
    .order-details { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0; }
    .order-detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #dee2e6; font-size: 0.9rem; }
    .order-detail-row:last-child { border-bottom: none; }
    .order-detail-label { font-weight: 600; color: #2d3748; }
    .order-detail-value { color: #40916c; font-weight: 700; }
    
    .order-items { margin: 15px 0; }
    .order-items h3 { margin-bottom: 10px; color: #2d6a4f; font-size: 1rem; }
    .order-item { background: white; padding: 10px; border-radius: 5px; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; }
    .order-item-name { font-weight: 600; color: #2d3748; font-size: 0.9rem; }
    .order-item-qty { font-size: 0.8rem; color: #6c757d; }
    
    .payment-badge { display: inline-block; padding: 5px 12px; border-radius: 12px; font-weight: 600; font-size: 0.8rem; }
    .payment-esewa { background: #60B054; color: white; }
    .payment-khalti { background: #5C2D91; color: white; }
    .payment-cod { background: #FFB800; color: #2d3748; }
    
    .success-actions { display: flex; gap: 10px; justify-content: center; margin-top: 20px; }
    .btn-my-orders { background: #40916c; color: white; padding: 8px 20px; border-radius: 5px; text-decoration: none; font-weight: 600; display: inline-block; transition: all 0.3s; font-size: 0.9rem; }
    .btn-my-orders:hover { background: #2d6a4f; transform: translateY(-2px); }
    .btn-continue-shop { background: white; color: #40916c; border: 2px solid #40916c; padding: 6px 18px; border-radius: 5px; text-decoration: none; font-weight: 600; display: inline-block; transition: all 0.3s; font-size: 0.9rem; }
    .btn-continue-shop:hover { background: #f0f9f4; }
</style>

<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        </div>
        
        <h1 class="success-title">Payment Successful!</h1>
        <p class="success-message">Your order has been placed successfully.</p>
        
        <div class="order-details">
            <div class="order-detail-row">
                <span class="order-detail-label">Order Number</span>
                <span class="order-detail-value">#<?php echo htmlspecialchars($order['order_number']); ?></span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Date</span>
                <span class="order-detail-value"><?php echo date('m/d H:i', strtotime($order['created_at'])); ?></span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Payment</span>
                <?php
                $payment_class = '';
                if ($order['payment_method'] == 'esewa') $payment_class = 'payment-esewa';
                elseif ($order['payment_method'] == 'khalti') $payment_class = 'payment-khalti';
                else $payment_class = 'payment-cod';
                ?>
                <span class="payment-badge <?php echo $payment_class; ?>">
                    <?php echo htmlspecialchars($order['payment_method']); ?>
                </span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Status</span>
                <span style="color: #28a745; font-weight: 700;">✓ <?php echo ucfirst($order['payment_status']); ?></span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Total</span>
                <span class="order-detail-value"><?php echo formatPrice($order['total_amount']); ?></span>
            </div>
        </div>
        
        <div class="order-items">
            <h3>Items (<?php echo count($items); ?>)</h3>
            <?php foreach ($items as $item): ?>
                <div class="order-item">
                    <div>
                        <div class="order-item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                        <div class="order-item-qty">Qty: <?php echo $item['quantity']; ?></div>
                    </div>
                    <div style="font-weight: 700; color: #40916c; font-size: 0.9rem;"><?php echo formatPrice($item['price'] * $item['quantity']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="success-actions">
            <a href="my-orders.php" class="btn-my-orders">My Orders</a>
            <a href="index.php" class="btn-continue-shop">Continue Shopping</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
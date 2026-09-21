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
    .success-container { max-width: 650px; margin: 40px auto; padding: 20px; }
    .success-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    .success-icon { width: 70px; height: 70px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .success-icon svg { width: 40px; height: 40px; fill: white; }
    .success-title { font-size: 1.8rem; color: #28a745; margin-bottom: 10px; font-weight: 700; text-align: center; }
    .success-message { color: #6c757d; font-size: 1rem; margin-bottom: 25px; text-align: center; }
    
    .order-details { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
    .order-detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #dee2e6; font-size: 0.95rem; }
    .order-detail-row:last-child { border-bottom: none; }
    .order-detail-label { font-weight: 600; color: #2d3748; }
    .order-detail-value { color: #40916c; font-weight: 700; }
    
    .order-items { margin: 20px 0; }
    .order-items h3 { margin-bottom: 15px; color: #2d6a4f; font-size: 1.1rem; }
    .order-item { background: white; padding: 12px; border-radius: 6px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; }
    .order-item-name { font-weight: 600; color: #2d3748; font-size: 0.95rem; }
    .order-item-qty { font-size: 0.85rem; color: #6c757d; }
    
    .payment-badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; }
    .payment-esewa { background: #d1fae5; color: #065f46; }
    .payment-khalti { background: #e9d5ff; color: #6b21a8; }
    .payment-cod { background: #fef3c7; color: #92400e; }
    
    .success-actions { display: flex; gap: 15px; justify-content: center; margin-top: 25px; }
    .btn-my-orders { background: #40916c; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block; transition: all 0.3s; font-size: 0.95rem; }
    .btn-my-orders:hover { background: #2d6a4f; transform: translateY(-2px); }
    .btn-continue-shop { background: white; color: #40916c; border: 2px solid #40916c; padding: 10px 23px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block; transition: all 0.3s; font-size: 0.95rem; }
    .btn-continue-shop:hover { background: #f0f9f4; }
</style>

<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        </div>
        
        <h1 class="success-title">Order Placed Successfully!</h1>
        <p class="success-message">Thank you for shopping with Bloomify. Your order details are below.</p>
        
        <div class="order-details">
            <div class="order-detail-row">
                <span class="order-detail-label">Order Number</span>
                <span class="order-detail-value">#<?php echo htmlspecialchars($order['order_number']); ?></span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Date</span>
                <span class="order-detail-value"><?php echo date('M d, Y - h:i A', strtotime($order['created_at'])); ?></span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Payment Method</span>
                <?php
                // ✅ FIXED: Properly format payment method text and class
                $raw_payment = strtolower(trim($order['payment_method']));
                $payment_class = 'payment-cod';
                $payment_text = 'Cash on Delivery';
                
                if (in_array($raw_payment, ['esewa'])) {
                    $payment_class = 'payment-esewa';
                    $payment_text = 'eSewa';
                } elseif (in_array($raw_payment, ['khalti'])) {
                    $payment_class = 'payment-khalti';
                    $payment_text = 'Khalti';
                } elseif (in_array($raw_payment, ['cod', 'cash on delivery', ''])) {
                    $payment_class = 'payment-cod';
                    $payment_text = 'Cash on Delivery';
                } else {
                    $payment_text = ucfirst($raw_payment);
                }
                ?>
                <span class="payment-badge <?php echo $payment_class; ?>">
                    <?php echo htmlspecialchars($payment_text); ?>
                </span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Status</span>
                <span style="color: #28a745; font-weight: 700;">✓ <?php echo ucfirst($order['payment_status']); ?></span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Total Amount</span>
                <span class="order-detail-value"><?php echo formatPrice($order['total_amount']); ?></span>
            </div>
        </div>
        
        <div class="order-items">
            <h3>Order Items (<?php echo count($items); ?>)</h3>
            <?php foreach ($items as $item): ?>
                <div class="order-item">
                    <div>
                        <div class="order-item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                        <div class="order-item-qty">Qty: <?php echo $item['quantity']; ?></div>
                    </div>
                    <div style="font-weight: 700; color: #40916c; font-size: 0.95rem;"><?php echo formatPrice($item['price'] * $item['quantity']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="success-actions">
            <a href="my-orders.php" class="btn-my-orders">View My Orders</a>
            <a href="index.php" class="btn-continue-shop">Continue Shopping</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
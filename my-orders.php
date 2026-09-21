<?php
$page_title = 'My Orders';
require_once __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view your orders.', 'danger');
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM orders WHERE user_id = ? AND archived = 0 ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<style>
    .orders-container { max-width: 900px; margin: 40px auto; padding: 20px; }
    .orders-title { font-size: 2rem; color: #2d6a4f; margin-bottom: 30px; font-weight: 700; text-align: center; display: flex; align-items: center; justify-content: center; gap: 15px; }
    
    .order-card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); transition: transform 0.3s, box-shadow 0.3s; border-left: 5px solid transparent; }
    .order-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
    
    .order-card.status-pending { border-left-color: #ffc107; }
    .order-card.status-processing { border-left-color: #17a2b8; }
    .order-card.status-completed { border-left-color: #28a745; }
    .order-card.status-delivered { border-left-color: #28a745; }
    .order-card.status-cancelled { border-left-color: #dc3545; opacity: 0.8; }
    
    .order-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0; }
    .order-number { font-size: 1.3rem; font-weight: 700; color: #2d6a4f; margin-bottom: 5px; }
    .order-date { font-size: 0.9rem; color: #6c757d; }
    
    .order-details { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px; }
    .order-detail-box { background: #f8f9fa; padding: 15px; border-radius: 8px; }
    .order-detail-label { font-size: 0.85rem; color: #6c757d; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .order-detail-value { font-weight: 700; color: #2d3748; font-size: 1.1rem; }
    
    .status-badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; text-transform: capitalize; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-processing { background: #d1ecf1; color: #0c5460; }
    .badge-completed { background: #d4edda; color: #155724; }
    .badge-delivered { background: #d4edda; color: #155724; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }
    
    .payment-badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; }
    .pay-esewa { background: #d1fae5; color: #065f46; }
    .pay-khalti { background: #e9d5ff; color: #6b21a8; }
    .pay-cod { background: #fef3c7; color: #92400e; }
    
    .order-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 10px; }
    
    .btn-view { background: #40916c; color: white; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-view:hover { background: #2d6a4f; transform: translateY(-2px); }
    
    .btn-cancel { background: white; color: #dc3545; border: 2px solid #dc3545; padding: 8px 22px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-cancel:hover { background: #dc3545; color: white; transform: translateY(-2px); }
    
    .btn-archive { background: #6c757d; color: white; border: none; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-archive:hover { background: #5a6268; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3); }
    
    .empty-orders { text-align: center; padding: 80px 20px; background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
    .empty-orders h3 { color: #2d6a4f; margin-bottom: 15px; font-size: 1.6rem; }
    .empty-orders p { color: #6c757d; margin-bottom: 30px; font-size: 1.1rem; }
    
    @media (max-width: 768px) {
        .order-details { grid-template-columns: 1fr; }
        .order-actions { flex-direction: column; }
        .btn-view, .btn-cancel, .btn-archive { width: 100%; justify-content: center; }
    }
</style>

<div class="orders-container">
    <h1 class="orders-title">My Orders</h1>
    
    <?php if ($orders->num_rows > 0): ?>
        <?php while($order = $orders->fetch_assoc()): 
            $count_sql = "SELECT COUNT(*) as cnt FROM order_items WHERE order_id = ?";
            $count_stmt = $conn->prepare($count_sql);
            $count_stmt->bind_param("i", $order['id']);
            $count_stmt->execute();
            $item_count = $count_stmt->get_result()->fetch_assoc()['cnt'];
            
            $delivery_status = $order['delivery_status'];
            $status_class = 'status-' . $delivery_status;
            $badge_class = 'badge-' . $delivery_status;
            
            // ✅ FIXED: Properly format payment method text and class
            $raw_payment = strtolower(trim($order['payment_method']));
            $pay_class = 'pay-cod';
            $pay_text = 'Cash on Delivery';
            
            if (in_array($raw_payment, ['esewa'])) {
                $pay_class = 'pay-esewa';
                $pay_text = 'eSewa';
            } elseif (in_array($raw_payment, ['khalti'])) {
                $pay_class = 'pay-khalti';
                $pay_text = 'Khalti';
            } elseif (in_array($raw_payment, ['cod', 'cash on delivery', ''])) {
                $pay_class = 'pay-cod';
                $pay_text = 'Cash on Delivery';
            } else {
                $pay_text = ucfirst($raw_payment);
            }
            
            $can_cancel = $delivery_status === 'pending' && $order['payment_status'] === 'pending';
            $can_archive = in_array($delivery_status, ['delivered', 'cancelled']);
        ?>
            <div class="order-card <?php echo $status_class; ?>">
                <div class="order-header">
                    <div>
                        <div class="order-number">Order #<?php echo htmlspecialchars($order['order_number']); ?></div>
                        <div class="order-date"><?php echo date('M d, Y - h:i A', strtotime($order['created_at'])); ?></div>
                    </div>
                    <span class="status-badge <?php echo $badge_class; ?>">
                        <?php echo ucfirst($delivery_status); ?>
                    </span>
                </div>
                
                <div class="order-details">
                    <div class="order-detail-box">
                        <div class="order-detail-label">Total Amount</div>
                        <div class="order-detail-value"><?php echo formatPrice($order['total_amount']); ?></div>
                    </div>
                    <div class="order-detail-box">
                        <div class="order-detail-label">Payment Method</div>
                        <span class="payment-badge <?php echo $pay_class; ?>">
                            <?php echo htmlspecialchars($pay_text); ?>
                        </span>
                    </div>
                    <div class="order-detail-box">
                        <div class="order-detail-label">Items</div>
                        <div class="order-detail-value"><?php echo $item_count; ?> Item<?php echo $item_count != 1 ? 's' : ''; ?></div>
                    </div>
                </div>
                
                <div class="order-actions">
                    <?php if ($can_cancel): ?>
                        <a href="cancel-order.php?id=<?php echo $order['id']; ?>" class="btn-cancel" onclick="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                            ✕ Cancel Order
                        </a>
                    <?php endif; ?>

                    <?php if ($can_archive): ?>
                        <a href="archive-order.php?id=<?php echo $order['id']; ?>" class="btn-archive" onclick="return confirm('Remove this order from your list? It will still be visible to admin.');">
                            Remove
                        </a>
                    <?php endif; ?>

                    <a href="order-success.php?order=<?php echo htmlspecialchars($order['order_number']); ?>" class="btn-view">
                        👁️ View Details
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-orders">
            <div style="font-size: 5rem; margin-bottom: 20px;">📭</div>
            <h3>No Orders Yet</h3>
            <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
            <a href="index.php" class="btn-view" style="width: auto; padding: 14px 35px; font-size: 1.1rem;">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
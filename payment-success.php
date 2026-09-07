<?php
$page_title = 'Payment Success';
require_once __DIR__ . '/includes/header.php';

if (!isLoggedIn() || !isset($_GET['order'])) {
    redirect('index.php', 'Invalid access.', 'danger');
}

$order_number = $_GET['order'];
$method = $_GET['method'] ?? 'Unknown';
$txn_id = $_GET['txn'] ?? 'N/A';

// Fetch order details to display receipt
$sql = "SELECT * FROM orders WHERE order_number = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $order_number, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    redirect('index.php', 'Order not found.', 'danger');
}
?>

<style>
    .success-wrapper { max-width: 600px; margin: 60px auto; text-align: center; padding: 0 20px; }
    .success-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
    .check-icon { 
        width: 80px; height: 80px; background: #2d6a4f; border-radius: 50%; 
        display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; 
        animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .check-icon svg { width: 45px; height: 45px; fill: white; }
    @keyframes popIn {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    .success-title { color: #2d6a4f; font-size: 2rem; margin-bottom: 10px; font-weight: 800; }
    .receipt-box { background: #f8f9fa; padding: 25px; border-radius: 12px; margin: 30px 0; text-align: left; }
    .receipt-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e2e8f0; }
    .receipt-row:last-child { border-bottom: none; }
    .receipt-label { color: #6c757d; font-weight: 600; font-size: 0.9rem; }
    .receipt-value { color: #2d3748; font-weight: 700; font-size: 0.95rem; }
    .action-btns { display: flex; gap: 15px; justify-content: center; margin-top: 30px; }
    .btn-shop { background: #2d6a4f; color: white; padding: 14px 30px; border-radius: 8px; text-decoration: none; font-weight: 700; transition: all 0.3s; }
    .btn-shop:hover { background: #1b4332; transform: translateY(-2px); }
    .btn-orders { background: white; color: #2d6a4f; border: 2px solid #2d6a4f; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 700; transition: all 0.3s; }
    .btn-orders:hover { background: #f0f9f4; transform: translateY(-2px); }
</style>

<div class="success-wrapper">
    <div class="success-card">
        <div class="check-icon">
            <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        </div>
        <h1 class="success-title">Payment Successful!</h1>
        <p style="color: #6c757d; font-size: 1.05rem;">Your order has been placed and payment is confirmed.</p>
        
        <div class="receipt-box">
            <div class="receipt-row">
                <span class="receipt-label">Order ID</span>
                <span class="receipt-value">#<?php echo htmlspecialchars($order['order_number']); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Transaction ID</span>
                <span class="receipt-value"><?php echo htmlspecialchars($txn_id); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Total Amount</span>
                <span class="receipt-value"><?php echo formatPrice($order['total_amount']); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Payment Method</span>
                <span class="receipt-value"><?php echo htmlspecialchars($method); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Current Status</span>
                <!-- Dynamically shows "Completed" because the database was updated -->
                <span class="receipt-value" style="color: #2d6a4f; text-transform: capitalize;">✓ <?php echo str_replace('_', ' ', $order['status']); ?></span>
            </div>
        </div>
        
        <div class="action-btns">
            <a href="my-orders.php" class="btn-orders">My Orders</a>
            <a href="index.php" class="btn-shop">Continue Shopping</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
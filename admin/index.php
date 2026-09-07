<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/includes/admin-header.php';

// Fetch Dashboard Stats
$stats_sql = "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN payment_status = 'completed' THEN total_amount ELSE 0 END) as total_revenue,
                SUM(CASE WHEN status IN ('pending', 'awaiting_payment') THEN 1 ELSE 0 END) as pending_orders,
                (SELECT COUNT(*) FROM users WHERE role = 'user') as total_users
            FROM orders";
$stats = $conn->query($stats_sql)->fetch_assoc();

// Fetch Recent Orders (Last 10)
$recent_orders_sql = "SELECT o.*, u.name as user_name 
                      FROM orders o 
                      JOIN users u ON o.user_id = u.id 
                      ORDER BY o.created_at DESC LIMIT 10";
$recent_orders = $conn->query($recent_orders_sql);
?>

<!-- Dashboard Stats Cards -->
<div class="stats-grid">
    <div class="stat-card revenue">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value"><?php echo formatPrice($stats['total_revenue'] ?? 0); ?></div>
        <div class="stat-icon">💰</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Total Orders</div>
        <div class="stat-value"><?php echo $stats['total_orders'] ?? 0; ?></div>
        <div class="stat-icon">📦</div>
    </div>
    
    <div class="stat-card pending">
        <div class="stat-label">Pending Orders</div>
        <div class="stat-value" style="color: #ff4757;"><?php echo $stats['pending_orders'] ?? 0; ?></div>
        <div class="stat-icon">⏳</div>
    </div>
    
    <div class="stat-card users">
        <div class="stat-label">Total Users</div>
        <div class="stat-value"><?php echo $stats['total_users'] ?? 0; ?></div>
        <div class="stat-icon">👥</div>
    </div>
</div>

<!-- Pending COD Alert Banner -->
<?php if (($stats['pending_orders'] ?? 0) > 0): ?>
    <div class="cod-alert">
        <div class="cod-alert-text">
            <span class="cod-alert-icon"></span>
            <div>
                <h3><?php echo $stats['pending_orders']; ?> Order(s) Awaiting Confirmation</h3>
                <p>Cash on Delivery and unpaid orders need your attention.</p>
            </div>
        </div>
        <a href="orders.php" class="btn-confirm-cod">Review Orders →</a>
    </div>
<?php endif; ?>

<!-- Recent Orders Table -->
<h2 class="section-title">Recent Orders</h2>
<table class="admin-table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($recent_orders->num_rows > 0): ?>
            <?php while($order = $recent_orders->fetch_assoc()): 
                // Safely format status for CSS class (e.g., "awaiting_payment" -> "badge-awaiting-payment")
                $safe_status = strtolower(str_replace('_', '-', $order['status']));
                $badge_class = 'badge-' . $safe_status;
            ?>
                <tr>
                    <td style="font-weight: 700; color: #2d6a4f;">#<?php echo htmlspecialchars($order['order_number']); ?></td>
                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                    <td style="font-weight: 700;"><?php echo formatPrice($order['total_amount']); ?></td>
                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                    <td>
                        <span class="status-badge-sm <?php echo $badge_class; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                        </span>
                    </td>
                    <td style="color: #718096; font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td>
                        <a href="orders.php?view=<?php echo $order['id']; ?>" class="btn-view-order">👁️ View</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 50px; color: #718096;">
                    No orders yet. Start selling! 🌸
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
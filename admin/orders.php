<?php
$page_title = 'Manage Orders';
require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/includes/admin-header.php';

// Handle Status Update / COD Confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = sanitize($_POST['new_status']);
    
    // Update payment status accordingly to keep data consistent
    $pay_status_update = "";
    if ($new_status === 'processing') {
        $pay_status_update = ", payment_status = 'pending'"; 
    } elseif ($new_status === 'delivered') {
        $pay_status_update = ", payment_status = 'success'";
    }

    $sql = "UPDATE orders SET delivery_status = ? $pay_status_update WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        redirect('orders.php', 'Order status updated successfully!', 'success');
    } else {
        redirect('orders.php', 'Failed to update order.', 'danger');
    }
}

// Fetch All Orders
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sql = "SELECT o.*, u.name as user_name, u.email as user_email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id";

if ($filter !== 'all') {
    $sql .= " WHERE o.delivery_status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $orders = $stmt->get_result();
} else {
    $sql .= " ORDER BY o.created_at DESC";
    $orders = $conn->query($sql);
}
?>

<style>
    /* Back Button Style */
    .btn-back { 
        display: inline-flex; align-items: center; gap: 8px;
        color: #6c757d; text-decoration: none; font-weight: 600; font-size: 0.95rem; 
        margin-bottom: 15px; transition: all 0.3s; padding: 8px 0;
    }
    .btn-back:hover { color: #2d6a4f; transform: translateX(-4px); }

    .filter-tabs { display: flex; gap: 10px; margin-bottom: 25px; flex-wrap: wrap; }
    .filter-tab { 
        padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; 
        background: white; color: #4a5568; border: 1px solid #e2e8f0; transition: all 0.3s;
    }
    .filter-tab:hover { background: #f8fafc; border-color: #cbd5e1; }
    .filter-tab.active { background: #2d6a4f; color: white; border-color: #2d6a4f; }
    
    .action-form { display: inline-flex; gap: 8px; align-items: center; }
    .status-select { 
        padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.85rem; 
        font-weight: 600; color: #2d3748; background: white; cursor: pointer;
    }
    .btn-update { 
        background: #2d6a4f; color: white; border: none; padding: 8px 16px; border-radius: 6px; 
        font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s;
    }
    .btn-update:hover { background: #1b4332; }
    
    .btn-confirm-cod-sm { 
        background: #ffd700; color: #1b4332; border: none; padding: 8px 16px; border-radius: 6px; 
        font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block;
    }
    .btn-confirm-cod-sm:hover { background: #e6c200; transform: translateY(-1px); }

    .btn-cancel-sm {
        background: #fee2e2; color: #991b1b; border: none; padding: 8px 16px; border-radius: 6px; 
        font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s;
    }
    .btn-cancel-sm:hover { background: #fecaca; }
    
    .completed-badge {
        display: inline-flex; align-items: center; gap: 6px;
        color: #2d6a4f; font-weight: 700; font-size: 0.9rem;
    }
</style>

<!-- NEW: Back Button -->
<a href="index.php" class="btn-back">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    Back to Dashboard
</a>

<div class="section-title">Manage Orders</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="orders.php?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">All Orders</a>
    <a href="orders.php?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending</a>
    <a href="orders.php?filter=processing" class="filter-tab <?php echo $filter === 'processing' ? 'active' : ''; ?>">Processing</a>
    <a href="orders.php?filter=completed" class="filter-tab <?php echo $filter === 'completed' ? 'active' : ''; ?>">Completed</a>
    <a href="orders.php?filter=cancelled" class="filter-tab <?php echo $filter === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($orders->num_rows > 0): ?>
            <?php while($order = $orders->fetch_assoc()): 
                $safe_status = strtolower(str_replace('_', '-', $order['delivery_status']));
                $badge_class = 'badge-' . $safe_status;
                
                $is_pending_cod = ($order['payment_method'] === 'cod' && $order['delivery_status'] === 'pending');
                $is_completed = ($order['delivery_status'] === 'delivered');
            ?>
                <tr>
                    <td style="font-weight: 700; color: #2d6a4f;">#<?php echo htmlspecialchars($order['order_number']); ?></td>
                    <td>
                        <div style="font-weight: 600;"><?php echo htmlspecialchars($order['user_name']); ?></div>
                        <div style="font-size: 0.8rem; color: #718096;"><?php echo htmlspecialchars($order['user_email']); ?></div>
                    </td>
                    <td style="font-weight: 700;"><?php echo formatPrice($order['total_amount']); ?></td>
                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                    <td><span class="status-badge-sm <?php echo $badge_class; ?>"><?php echo ucfirst(str_replace('_', ' ', $order['delivery_status'])); ?></span></td>
                    <td style="color: #718096; font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td>
                        <?php if ($is_pending_cod): ?>
                            <form method="POST" class="action-form">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <input type="hidden" name="new_status" value="processing">
                                <button type="submit" name="update_status" class="btn-confirm-cod-sm" onclick="return confirm('Confirm this Cash on Delivery order? This will mark it as Processing.');">
                                    ✓ Confirm COD
                                </button>
                            </form>
                        <?php elseif ($is_completed): ?>
                            <div class="action-form">
                                    <span class="completed-badge">✓ Delivered</span>
                                <form method="POST" class="action-form" style="margin-left: 10px;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="new_status" value="cancelled">
                                    <button type="submit" name="update_status" class="btn-cancel-sm" onclick="return confirm('Are you sure you want to CANCEL this delivered order?');">
                                        Cancel
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <form method="POST" class="action-form">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="new_status" class="status-select">
                                    <option value="pending" <?php echo $order['delivery_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['delivery_status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="delivered" <?php echo $order['delivery_status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['delivery_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn-update">Update</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 50px; color: #718096;">
                    No orders found for this filter.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
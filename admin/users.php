<?php
$page_title = 'Manage Users';
require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/includes/admin-header.php';

$message = '';
$message_type = '';

// --- HANDLE DELETE USER ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Safety Check: Do not delete users who have placed orders
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $order_count = $check_stmt->get_result()->fetch_assoc()['count'];
    
    if ($order_count > 0) {
        $message = "Cannot delete user. They have $order_count order(s) in the system. Deleting them would break order history.";
        $message_type = "danger";
    } else {
        $del_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $del_stmt->bind_param("i", $id);
        if ($del_stmt->execute()) {
            $message = "User deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Failed to delete user.";
            $message_type = "danger";
        }
    }
    
    // Redirect to clear GET params
    header("Location: users.php?msg=" . urlencode($message) . "&type=" . $message_type);
    exit();
}

// Fetch message from redirect
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $message_type = $_GET['type'];
}

// Fetch Users with Order Counts
$sql = "SELECT u.id, u.name, u.email, u.phone, u.role, u.created_at, 
               COUNT(o.id) as order_count 
        FROM users u 
        LEFT JOIN orders o ON u.id = o.user_id 
        GROUP BY u.id 
        ORDER BY u.id DESC";
$users = $conn->query($sql);
?>

<style>
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #6c757d; text-decoration: none; font-weight: 600; font-size: 0.95rem; margin-bottom: 15px; transition: all 0.3s; }
    .btn-back:hover { color: #2d6a4f; transform: translateX(-4px); }
    
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    
    .role-badge { padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    .role-admin { background: #fef3c7; color: #92400e; }
    .role-user { background: #e0e7ff; color: #3730a3; }
    
    .badge-count { background: #e2e8f0; color: #4a5568; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    
    .btn-action { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600; margin-right: 5px; display: inline-block; }
    .btn-delete { background: #fee2e2; color: #991b1b; }
    .btn-delete:hover { background: #fecaca; }
</style>

<a href="index.php" class="btn-back">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    Back to Dashboard
</a>

<div class="page-header">
    <h2 class="section-title" style="margin:0;">👥 Manage Users</h2>
</div>

<?php if ($message): ?>
    <div class="alert" style="padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; background: <?php echo $message_type === 'success' ? '#d1fae5' : '#fee2e2'; ?>; color: <?php echo $message_type === 'success' ? '#065f46' : '#991b1b'; ?>; border: 1px solid <?php echo $message_type === 'success' ? '#a7f3d0' : '#fecaca'; ?>;">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<!-- USERS TABLE -->
<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Orders</th>
            <th>Joined</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($users && $users->num_rows > 0): ?>
            <?php while($user = $users->fetch_assoc()): ?>
                <tr>
                    <td style="color: #718096;">#<?php echo $user['id']; ?></td>
                    <td style="font-weight: 600;"><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                    <td>
                        <span class="role-badge <?php echo $user['role'] === 'admin' ? 'role-admin' : 'role-user'; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge-count"><?php echo $user['order_count']; ?> Order(s)</span>
                    </td>
                    <td style="color: #718096; font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <?php if ($user['role'] !== 'admin'): ?>
                            <a href="?delete=<?php echo $user['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this user? This cannot be undone.');">Delete</a>
                        <?php else: ?>
                            <span style="color: #991b1b; font-size: 0.8rem; font-weight: 600;">Protected</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align: center; padding: 50px; color: #718096;">
                    No users found.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
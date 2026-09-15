<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn() || !isset($_GET['id'])) {
    redirect('my-orders.php', 'Invalid request.', 'danger');
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Only allow archiving completed/cancelled orders (not active ones)
$sql = "UPDATE orders SET archived = 1 WHERE id = ? AND user_id = ? AND delivery_status IN ('delivered', 'cancelled')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    redirect('my-orders.php', 'Order removed from your list.', 'success');
} else {
    redirect('my-orders.php', 'Cannot remove active orders. Please wait until they are completed or cancelled.', 'danger');
}
?>
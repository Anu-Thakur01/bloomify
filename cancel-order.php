<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn() || !isset($_GET['id'])) {
    redirect('my-orders.php', 'Invalid request.', 'danger');
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Only allow cancelling if order is 'pending' or 'awaiting_payment'
$sql = "UPDATE orders SET delivery_status = 'cancelled', payment_status = 'failed' 
    WHERE id = ? AND user_id = ? AND delivery_status = 'pending' AND payment_status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    redirect('my-orders.php', 'Order cancelled successfully.', 'success');
} else {
    redirect('my-orders.php', 'Cannot cancel this order. It may already be processed.', 'danger');
}
?>
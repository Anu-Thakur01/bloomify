<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = (int)($data['product_id'] ?? 0);
$action = $data['action'] ?? ''; // 'increase' or 'decrease'

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get current quantity
$sql = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit;
}

$current_qty = $result['quantity'];
$new_qty = $current_qty;

if ($action === 'increase') {
    $new_qty = $current_qty + 1;
} elseif ($action === 'decrease') {
    if ($current_qty <= 1) {
        // If quantity becomes 0, remove item
        $del_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        $del_stmt = $conn->prepare($del_sql);
        $del_stmt->bind_param("ii", $user_id, $product_id);
        $del_stmt->execute();
        echo json_encode(['success' => true, 'removed' => true]);
        exit;
    }
    $new_qty = $current_qty - 1;
}

// Update database
$update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
$upd_stmt = $conn->prepare($update_sql);
$upd_stmt->bind_param("iii", $new_qty, $user_id, $product_id);

if ($upd_stmt->execute()) {
    // Recalculate totals
    $total_sql = "SELECT SUM(c.quantity * p.price) as total 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = ?";
    $tot_stmt = $conn->prepare($total_sql);
    $tot_stmt->bind_param("i", $user_id);
    $tot_stmt->execute();
    $new_total = $tot_stmt->get_result()->fetch_assoc()['total'] ?? 0;
    
    echo json_encode([
        'success' => true, 
        'new_qty' => $new_qty, 
        'new_total' => formatPrice($new_total),
        'item_subtotal' => formatPrice($new_qty * ($data['price'] ?? 0))
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}
?>
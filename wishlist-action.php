<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to manage your wishlist.', 'danger');
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$action = $_POST['action'] ?? '';

if ($product_id > 0) {
    if ($action === 'add') {
        // Insert into wishlist (IGNORE prevents errors if already exists)
        $sql = "INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        redirect($_SERVER['HTTP_REFERER'] ?? 'products.php', 'Added to wishlist! ❤️', 'success');
        
    } elseif ($action === 'remove') {
        // Remove from wishlist
        $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        redirect($_SERVER['HTTP_REFERER'] ?? 'wishlist.php', 'Removed from wishlist.', 'success');
    }
}

redirect('products.php', 'Invalid request.', 'danger');
?>
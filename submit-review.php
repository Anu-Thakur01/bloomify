<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to write a review.', 'danger');
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$comment = isset($_POST['comment']) ? sanitize($_POST['comment']) : '';
$user_id = $_SESSION['user_id'];

if ($product_id > 0 && $rating >= 1 && $rating <= 5 && !empty($comment)) {
    // Check if user has purchased this product
    $check_sql = "SELECT o.id FROM orders o 
                  JOIN order_items oi ON o.id = oi.order_id 
                  WHERE o.user_id = ? AND oi.product_id = ? AND o.payment_status = 'success'";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $has_purchased = $check_stmt->get_result()->num_rows > 0;
    
    if ($has_purchased) {
        // Check if user already reviewed this product
        $existing_sql = "SELECT id FROM reviews WHERE user_id = ? AND product_id = ?";
        $existing_stmt = $conn->prepare($existing_sql);
        $existing_stmt->bind_param("ii", $user_id, $product_id);
        $existing_stmt->execute();
        
        if ($existing_stmt->get_result()->num_rows > 0) {
            redirect($_SERVER['HTTP_REFERER'] ?? 'products.php', 'You have already reviewed this product.', 'danger');
        }
        
        // ✅ Insert review as APPROVED immediately (no moderation)
        $sql = "INSERT INTO reviews (product_id, user_id, rating, comment, status) VALUES (?, ?, ?, ?, 'approved')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
        
        if ($stmt->execute()) {
            redirect($_SERVER['HTTP_REFERER'] ?? 'products.php', 'Thank you! Your review has been published.', 'success');
        } else {
            redirect($_SERVER['HTTP_REFERER'] ?? 'products.php', 'Failed to submit review. Please try again.', 'danger');
        }
    } else {
        redirect($_SERVER['HTTP_REFERER'] ?? 'products.php', 'You can only review products you have purchased.', 'danger');
    }
} else {
    redirect($_SERVER['HTTP_REFERER'] ?? 'products.php', 'Please provide a valid rating and comment.', 'danger');
}
?>
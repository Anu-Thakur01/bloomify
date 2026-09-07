<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    removeFromCart($product_id);
    redirect('cart.php', 'Item removed from cart.', 'success');
} else {
    header('Location: cart.php');
    exit();
}
?>
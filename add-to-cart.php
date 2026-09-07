<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity > 0) {
        addToCart($product_id, $quantity);
        redirect('cart.php', 'Item added to cart!', 'success');
    } else {
        redirect('product-details.php?id=' . $product_id, 'Invalid quantity!', 'danger');
    }
} else {
    header('Location: index.php');
    exit();
}
?>
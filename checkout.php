<?php
$page_title = 'Checkout';
require_once __DIR__ . '/includes/header.php';

// 1. Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to checkout.', 'danger');
}

// 2. Check if cart is empty
$cart_items = getCartItems();
$cart_total = getCartTotal();

if (empty($cart_items)) {
    redirect('cart.php', 'Your cart is empty!', 'danger');
}

// 3. Handle Order Placement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $payment_method = sanitize($_POST['payment_method']);

    if (empty($name) || empty($phone) || empty($address)) {
        redirect('checkout.php', 'Please fill in all shipping details.', 'danger');
    }

    $order_number = generateOrderNumber();
    $user_id = $_SESSION['user_id'];

    // Determine initial status based on payment method
    $initial_status = ($payment_method === 'Cash on Delivery') ? 'pending' : 'awaiting_payment';
    $initial_pay_status = ($payment_method === 'Cash on Delivery') ? 'pending' : 'pending';

    // Insert Order
    $sql = "INSERT INTO orders (user_id, order_number, total_amount, payment_method, payment_status, status, shipping_name, shipping_phone, shipping_address) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdssssss", $user_id, $order_number, $cart_total, $payment_method, $initial_pay_status, $initial_status, $name, $phone, $address);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;

        // Insert Order Items
        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_item = $conn->prepare($item_sql);
        
        foreach ($cart_items as $item) {
            $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
            $stmt_item->execute();
        }

        // Clear Cart
        clearCart();

        // Store order info in session for payment gateways
        $_SESSION['pending_order'] = [
            'id' => $order_id,
            'order_number' => $order_number,
            'total_amount' => $cart_total,
            'shipping_name' => $name,
            'shipping_phone' => $phone,
            'shipping_address' => $address
        ];

        // Redirect based on payment method
        if ($payment_method === 'eSewa') {
            redirect('esewa-payment.php', '', '');
        } elseif ($payment_method === 'Khalti') {
            redirect('khalti-payment.php', '', '');
        } else {
            // COD goes directly to success
            redirect('payment-success.php?order=' . $order_number . '&method=Cash on Delivery', 'Order placed successfully!', 'success');
        }
    } else {
        redirect('checkout.php', 'Failed to place order. Please try again.', 'danger');
    }
}
?>

<style>
    .checkout-container { max-width: 1000px; margin: 40px auto; padding: 20px; display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; }
    .checkout-box { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
    .checkout-title { font-size: 1.5rem; color: #2d6a4f; margin-bottom: 25px; font-weight: 700; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; }
    
    .form-group-checkout { margin-bottom: 20px; }
    .form-group-checkout label { display: block; margin-bottom: 8px; font-weight: 600; color: #2d3748; font-size: 0.95rem; }
    .form-control-checkout { width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; transition: all 0.3s; }
    .form-control-checkout:focus { outline: none; border-color: #40916c; box-shadow: 0 0 0 3px rgba(64, 145, 108, 0.1); }
    
    .payment-methods { display: flex; flex-direction: column; gap: 12px; margin-top: 10px; }
    .payment-option { display: flex; align-items: center; padding: 15px; border: 2px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: all 0.3s; }
    .payment-option:hover { border-color: #40916c; background: #f8f9fa; }
    .payment-option input { margin-right: 15px; transform: scale(1.2); accent-color: #40916c; }
    .payment-option label { margin: 0; cursor: pointer; font-weight: 600; color: #2d3748; flex: 1; display: flex; align-items: center; gap: 15px; }
    .payment-option img { height: 35px; object-fit: contain; }
    
    .summary-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
    .summary-item:last-child { border-bottom: none; }
    .summary-total { display: flex; justify-content: space-between; padding-top: 20px; margin-top: 10px; border-top: 2px solid #2d6a4f; font-size: 1.3rem; font-weight: 800; color: #2d6a4f; }
    
    .btn-place-order { width: 100%; padding: 16px; background: linear-gradient(135deg, #40916c 0%, #2d6a4f 100%); color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s; margin-top: 20px; }
    .btn-place-order:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(64, 145, 108, 0.3); }
    
    @media (max-width: 768px) { .checkout-container { grid-template-columns: 1fr; } }
</style>

<div class="checkout-container">
    <!-- Left Side: Shipping & Payment -->
    <div class="checkout-box">
        <h2 class="checkout-title">Shipping Details</h2>
        <form method="POST" action="checkout.php">
            <div class="form-group-checkout">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control-checkout" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group-checkout">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control-checkout" placeholder="98XXXXXXXX" required>
            </div>
            <div class="form-group-checkout">
                <label>Delivery Address</label>
                <textarea name="address" class="form-control-checkout" rows="3" required></textarea>
            </div>

            <h2 class="checkout-title" style="margin-top: 30px;">Payment Method</h2>
            <div class="payment-methods">
                <div class="payment-option">
                    <input type="radio" id="esewa" name="payment_method" value="eSewa" required>
                    <label for="esewa">
                        <img src="<?php echo SITE_URL; ?>/assets/images/esewa-logo.png" alt="eSewa">
                        <span>eSewa</span>
                    </label>
                </div>
                <div class="payment-option">
                    <input type="radio" id="khalti" name="payment_method" value="Khalti">
                    <label for="khalti">
                        <img src="<?php echo SITE_URL; ?>/assets/images/khalti-logo.png" alt="Khalti">
                        <span>Khalti</span>
                    </label>
                </div>
                <div class="payment-option">
                    <input type="radio" id="cod" name="payment_method" value="Cash on Delivery">
                    <label for="cod">
                        <span style="font-size: 1.5rem;">💵</span>
                        <span>Cash on Delivery</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-place-order">Place Order</button>
        </form>
    </div>

    <!-- Right Side: Order Summary -->
    <div class="checkout-box" style="height: fit-content;">
        <h2 class="checkout-title">Order Summary</h2>
        <?php foreach ($cart_items as $item): ?>
            <div class="summary-item">
                <div>
                    <div style="font-weight: 600; color: #2d3748;"><?php echo htmlspecialchars($item['name']); ?></div>
                    <div style="font-size: 0.85rem; color: #6c757d;">Qty: <?php echo $item['quantity']; ?></div>
                </div>
                <div style="font-weight: 700; color: #40916c;"><?php echo formatPrice($item['subtotal']); ?></div>
            </div>
        <?php endforeach; ?>
        
        <div class="summary-total">
            <span>Total Amount</span>
            <span><?php echo formatPrice($cart_total); ?></span>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
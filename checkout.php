<?php
$page_title = 'Checkout';
require_once __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php', 'Please login to checkout.', 'danger');
}

$cart_items = getCartItems();
$cart_total = getCartTotal();

if (empty($cart_items)) {
    redirect('cart.php', 'Your cart is empty!', 'danger');
}

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

    $sql = "INSERT INTO orders (user_id, order_number, total_amount, payment_method, payment_status, delivery_status, customer_name, customer_phone, customer_address) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $initial_pay_status = 'pending';
    $initial_delivery_status = 'pending';
    $stmt->bind_param("isdssssss", $user_id, $order_number, $cart_total, $payment_method, $initial_pay_status, $initial_delivery_status, $name, $phone, $address);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;

        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_item = $conn->prepare($item_sql);
        
        foreach ($cart_items as $item) {
            // ✅ Saves the discounted final_price to the order history
            $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['final_price']);
            $stmt_item->execute();
        }

        $_SESSION['pending_order'] = [
            'id' => $order_id,
            'order_number' => $order_number,
            'total_amount' => $cart_total
        ];

        if ($payment_method === 'eSewa') {
            $esewa_url = "https://rc-epay.esewa.com.np/api/epay/main/v2/form";
            $amount = $cart_total;
            $tax_amount = 0;
            $total_amount = $cart_total;
            $transaction_uuid = $order_number;
            $product_code = 'EPAYTEST';
            $success_url = SITE_URL . '/verify-esewa-payment.php';
            $failure_url = SITE_URL . '/payment-cancelled.php';
            $signed_field_names = 'total_amount,transaction_uuid,product_code';
            $message = "total_amount={$total_amount},transaction_uuid={$transaction_uuid},product_code={$product_code}";
            $signature = base64_encode(hash_hmac('sha256', $message, '8gBm/:&EnhH.1/q', true));

            echo '<!DOCTYPE html><html><head><title>Redirecting to eSewa...</title>
            <style>body{font-family:sans-serif;text-align:center;padding-top:100px;background:#f8f9fa;}.loader{border:4px solid #f3f3f3;border-top:4px solid #40916c;border-radius:50%;width:40px;height:40px;animation:spin 1s linear infinite;margin:0 auto 20px;}@keyframes spin{0%{transform:rotate(0deg);}100%{transform:rotate(360deg);}}</style>
            </head><body><div class="loader"></div><h2>Redirecting to eSewa Secure Payment...</h2><p>Please wait, do not close this window.</p>
            <form id="esewa_form" action="' . htmlspecialchars($esewa_url) . '" method="POST">
                <input type="hidden" name="amount" value="' . htmlspecialchars($amount) . '">
                <input type="hidden" name="tax_amount" value="' . htmlspecialchars($tax_amount) . '">
                <input type="hidden" name="total_amount" value="' . htmlspecialchars($total_amount) . '">
                <input type="hidden" name="transaction_uuid" value="' . htmlspecialchars($transaction_uuid) . '">
                <input type="hidden" name="product_code" value="' . htmlspecialchars($product_code) . '">
                <input type="hidden" name="product_service_charge" value="0">
                <input type="hidden" name="product_delivery_charge" value="0">
                <input type="hidden" name="success_url" value="' . htmlspecialchars($success_url) . '">
                <input type="hidden" name="failure_url" value="' . htmlspecialchars($failure_url) . '">
                <input type="hidden" name="signed_field_names" value="' . htmlspecialchars($signed_field_names) . '">
                <input type="hidden" name="signature" value="' . htmlspecialchars($signature) . '">
            </form><script>document.getElementById("esewa_form").submit();</script></body></html>';
            exit();
        } elseif ($payment_method === 'Khalti') {
            $_SESSION['khalti_order'] = ['order_id' => $order_id, 'order_number' => $order_number, 'amount' => $cart_total * 100];
            redirect('khalti-checkout.php', '', '');
        } else {
            clearCart();
            redirect('payment-success.php?order=' . $order_number . '&method=Cash on Delivery', 'Order placed successfully! Please pay on delivery.', 'success');
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
    .btn-back-cart { display: inline-flex; align-items: center; gap: 6px; color: #6c757d; text-decoration: none; font-weight: 600; font-size: 0.95rem; margin-bottom: 15px; transition: all 0.3s; }
    .btn-back-cart:hover { color: #2d6a4f; transform: translateX(-4px); }
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
    <div class="checkout-box">
        <a href="cart.php" class="btn-back-cart">← Back to Cart</a>
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
                    <label for="esewa"><img src="<?php echo SITE_URL; ?>/assets/images/esewa-logo.png" alt="eSewa"><span>eSewa</span></label>
                </div>
                <div class="payment-option">
                    <input type="radio" id="khalti" name="payment_method" value="Khalti">
                    <label for="khalti"><img src="<?php echo SITE_URL; ?>/assets/images/khalti-logo.png" alt="Khalti"><span>Khalti</span></label>
                </div>
                <div class="payment-option">
                    <input type="radio" id="cod" name="payment_method" value="Cash on Delivery">
                    <label for="cod"><span style="font-size: 1.5rem;"></span><span>Cash on Delivery</span></label>
                </div>
            </div>
            <button type="submit" class="btn-place-order">Place Order</button>
        </form>
    </div>

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
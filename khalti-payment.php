<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn() || !isset($_SESSION['pending_order'])) {
    redirect('index.php', 'No pending order found.', 'danger');
}

$order = $_SESSION['pending_order'];
$total_amount = $order['total_amount'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    sleep(1); 
    
    $sql = "UPDATE orders SET payment_status = 'completed', status = 'processing', transaction_id = ? WHERE id = ?";
    $txn_id = 'KHL' . date('YmdHis') . rand(100, 999);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $txn_id, $order['id']);
    
    if ($stmt->execute()) {
        unset($_SESSION['pending_order']);
        redirect('payment-success.php?order=' . $order['order_number'] . '&method=Khalti&txn=' . $txn_id, 'Payment Successful!', 'success');
    } else {
        redirect('checkout.php', 'Payment failed. Please try again.', 'danger');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khalti Payment - Bloomify</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .khalti-container { background: white; width: 400px; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .khalti-logo { text-align: center; margin-bottom: 30px; }
        .khalti-logo h2 { color: #5C2D91; margin: 0; font-size: 2rem; }
        
        .form-group-khalti { margin-bottom: 20px; }
        .form-group-khalti label { display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666; }
        .form-control-khalti { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; }
        
        .btn-khalti { width: 100%; padding: 14px; background: #5C2D91; color: white; border: none; border-radius: 6px; font-weight: 700; font-size: 1.1rem; cursor: pointer; margin-top: 10px; }
        .btn-khalti:hover { background: #4a2475; }
        
        .cancel-link { display: block; text-align: center; margin-top: 20px; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="khalti-container">
        <div class="khalti-logo">
            <h2>Khalti</h2>
        </div>
        
        <form method="POST">
            <div class="form-group-khalti">
                <label>Khalti ID / Mobile</label>
                <input type="text" class="form-control-khalti" value="<?php echo htmlspecialchars($order['shipping_phone']); ?>" readonly>
            </div>
            
            <div class="form-group-khalti">
                <label>MPIN</label>
                <input type="password" class="form-control-khalti" placeholder="Enter 4-digit MPIN" maxlength="4" required pattern="[0-9]{4}">
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin: 20px 0; text-align: center;">
                <span style="color: #666;">Payable Amount</span>
                <div style="font-weight: 700; font-size: 1.5rem; color: #5C2D91; margin-top: 5px;">Rs. <?php echo number_format($total_amount, 2); ?></div>
            </div>
            
            <button type="submit" class="btn-khalti">PAY WITH KHALTI</button>
        </form>
        
        <a href="checkout.php" class="cancel-link">Cancel Payment</a>
    </div>
</body>
</html>
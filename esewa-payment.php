<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn() || !isset($_SESSION['pending_order'])) {
    redirect('index.php', 'No pending order found.', 'danger');
}

$order = $_SESSION['pending_order'];
$total_amount = $order['total_amount'];

// Handle Simulated Payment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simulate processing delay
    sleep(1); 
    
    // Update Order Status in Database
    $sql = "UPDATE orders SET payment_status = 'completed', status = 'processing', transaction_id = ? WHERE id = ?";
    $txn_id = 'ESW' . date('YmdHis') . rand(100, 999);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $txn_id, $order['id']);
    
    if ($stmt->execute()) {
        unset($_SESSION['pending_order']);
        redirect('payment-success.php?order=' . $order['order_number'] . '&method=eSewa&txn=' . $txn_id, 'Payment Successful!', 'success');
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
    <title>eSewa Payment - Bloomify</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #2d3436; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .esewa-container { background: #353b48; width: 400px; padding: 40px; border-radius: 12px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); color: white; }
        .esewa-logo { text-align: center; margin-bottom: 30px; }
        .esewa-logo h2 { color: #60B054; margin: 0; font-size: 2rem; }
        .esewa-logo p { color: #aaa; font-size: 0.8rem; margin-top: 5px; }
        
        .form-group-esewa { margin-bottom: 20px; }
        .form-group-esewa label { display: block; margin-bottom: 8px; font-size: 0.9rem; color: #ccc; }
        .form-control-esewa { width: 100%; padding: 12px; background: #f0f2f5; border: none; border-radius: 6px; font-size: 1rem; color: #333; }
        
        .btn-esewa { width: 100%; padding: 14px; background: #60B054; color: white; border: none; border-radius: 6px; font-weight: 700; font-size: 1.1rem; cursor: pointer; margin-top: 10px; transition: background 0.3s; }
        .btn-esewa:hover { background: #4a8c40; }
        
        .cancel-link { display: block; text-align: center; margin-top: 20px; color: #aaa; text-decoration: none; font-size: 0.9rem; }
        .cancel-link:hover { color: white; }
        
        .demo-note { text-align: center; margin-top: 30px; padding: 10px; background: rgba(255,165,0,0.1); border-radius: 6px; font-size: 0.8rem; color: #ffa500; }
    </style>
</head>
<body>
    <div class="esewa-container">
        <div class="esewa-logo">
            <h2>eSewa</h2>
            <p>Dummy Payment Gateway</p>
        </div>
        
        <h3 style="margin-bottom: 25px; font-weight: 500;">Sign in to your account</h3>
        
        <form method="POST">
            <div class="form-group-esewa">
                <label>eSewa ID</label>
                <input type="email" class="form-control-esewa" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? 'demo@esewa.com'); ?>" required>
            </div>
            
            <div class="form-group-esewa">
                <label>Name</label>
                <input type="text" class="form-control-esewa" value="<?php echo htmlspecialchars($order['shipping_name']); ?>" readonly>
            </div>
            
            <div class="form-group-esewa">
                <label>Mobile Number</label>
                <input type="text" class="form-control-esewa" value="<?php echo htmlspecialchars($order['shipping_phone']); ?>" readonly>
            </div>
            
            <div class="form-group-esewa">
                <label>Address</label>
                <input type="text" class="form-control-esewa" value="<?php echo htmlspecialchars($order['shipping_address']); ?>" readonly>
            </div>
            
            <div class="form-group-esewa">
                <label>MPIN</label>
                <input type="password" class="form-control-esewa" placeholder="Enter 4-digit MPIN" maxlength="4" required pattern="[0-9]{4}">
            </div>
            
            <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 6px; margin: 20px 0; display: flex; justify-content: space-between; align-items: center;">
                <span>Total Amount</span>
                <span style="font-weight: 700; font-size: 1.2rem; color: #60B054;">NRP. <?php echo number_format($total_amount, 2); ?></span>
            </div>
            
            <button type="submit" class="btn-esewa">LOGIN & PAY</button>
        </form>
        
        <a href="checkout.php" class="cancel-link">CANCEL PAYMENT</a>
        
        <div class="demo-note">
            This is a dummy eSewa login for Bloomify demonstration.
        </div>
    </div>
</body>
</html>
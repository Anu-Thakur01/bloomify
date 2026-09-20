<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// eSewa sends base64-encoded JSON in 'data' parameter
if (isset($_REQUEST['data'])) {
    $encrypted_data = $_REQUEST['data'];
    
    // Decode base64
    $decoded = base64_decode($encrypted_data);
    
    // Parse JSON
    $params = json_decode($decoded, true);
    
    // Extract values
    $order_number = $params['transaction_uuid'] ?? '';
    $transaction_code = $params['transaction_code'] ?? '';
    $status = $params['status'] ?? '';
    $total_amount = $params['total_amount'] ?? 0;
    $signature = $params['signature'] ?? '';
    
    if (!empty($order_number)) {
        // Verify signature (security check)
        $signed_field_names = $params['signed_field_names'] ?? '';
        $fields = explode(',', $signed_field_names);
        
        $message_parts = [];
        foreach ($fields as $field) {
            $field = trim($field);
            if (isset($params[$field])) {
                $message_parts[] = $field . '=' . $params[$field];
            }
        }
        $message = implode(',', $message_parts);
        
        // Use test secret key for development
        $secret_key = '8gBm/:&EnhH.1/q'; // eSewa test secret key
        $expected_signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));
        
        $is_signature_valid = ($signature === $expected_signature);
        
        // Check if payment was successful
        $is_success = (strtoupper($status) === 'COMPLETE');
        
        if ($is_success && $is_signature_valid) {
            // ✅ 1. Update order status in database
            // Set delivery_status to 'confirmed' for successful eSewa payment
            $stmt = $conn->prepare("UPDATE orders SET payment_status = 'success', delivery_status = 'confirmed', transaction_id = ? WHERE order_number = ?");
            $stmt->bind_param("ss", $transaction_code, $order_number);
            $stmt->execute();
            
            // ✅ 2. Clear cart ONLY after successful payment verification
            clearCart();
            
            // ✅ 3. Redirect to success page
            redirect('payment-success.php?order=' . $order_number . '&method=eSewa&txn=' . $transaction_code, 'Payment successful! Your order has been confirmed.', 'success');
            
        } else {
            // Payment failed or cancelled
            $stmt = $conn->prepare("UPDATE orders SET payment_status = 'failed' WHERE order_number = ?");
            $stmt->bind_param("s", $order_number);
            $stmt->execute();
            
            redirect('checkout.php', 'Payment was cancelled or failed. Please try again.', 'danger');
        }
    } else {
        redirect('checkout.php', 'Invalid payment response. Order information not found.', 'danger');
    }
} else {
    redirect('checkout.php', 'No payment data received from eSewa.', 'danger');
}
?>
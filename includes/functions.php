<?php
require_once __DIR__ . '/config.php';

// Sanitize Input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Check if User is Logged In
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if User is Admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Format Price
function formatPrice($price) {
    return 'Rs. ' . number_format($price, 2);
}

// Generate Order Number
function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

// Get Cart Count (Database Version)
function getCartCount() {
    global $conn;
    if (!isLoggedIn()) return 0;
    
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'] ?? 0;
}

// Add to Cart (Database Version)
function addToCart($product_id, $quantity = 1) {
    global $conn;
    
    if (!isLoggedIn()) {
        redirect('login.php', 'Please login to add items to your cart.', 'danger');
        return;
    }

    $user_id = $_SESSION['user_id'];
    
    // Check if item already exists in DB cart
    $check_sql = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity if exists
        $row = $result->fetch_assoc();
        $new_qty = $row['quantity'] + $quantity;
        $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("iii", $new_qty, $user_id, $product_id);
    } else {
        // Insert new item
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    }
    
    $stmt->execute();
}

// Get Cart Total (Database Version - ✅ UPDATED FOR DISCOUNTS)
function getCartTotal() {
    global $conn;
    if (!isLoggedIn()) return 0;
    
    $user_id = $_SESSION['user_id'];
    // Calculate discounted price in SQL: price - (price * discount_percentage / 100)
    $sql = "SELECT SUM(c.quantity * (p.price - (p.price * p.discount_percentage / 100))) as total 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ? AND p.status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'] ?? 0;
}

// Redirect with Message & Save Return URL
function redirect($url, $message = '', $type = 'success') {
    // Save the current page URL before redirecting (for "return after login")
    if (!isset($_SESSION['user_id']) && !in_array(basename(parse_url($url, PHP_URL_PATH)), ['login.php', 'register.php'])) {
        $full_url = $_SERVER['REQUEST_URI'];
        if (strpos($full_url, SITE_URL) === false) {
            $full_url = SITE_URL . $full_url;
        }
        $_SESSION['return_url'] = $full_url;
    }

    if ($message) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
    header("Location: $url");
    exit();
}

// Helper to get and clear the saved return URL
function getReturnUrl() {
    if (isset($_SESSION['return_url']) && !empty($_SESSION['return_url'])) {
        $url = $_SESSION['return_url'];
        unset($_SESSION['return_url']);
        // Security check: only allow internal URLs
        if (strpos($url, SITE_URL) === 0 || strpos($url, '/') === 0) {
            return $url;
        }
    }
    return SITE_URL . '/index.php'; // Default fallback
}

// Display Message
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'info';
        echo "<div class='alert alert-{$type}'>{$message}</div>";
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Get Cart Items with Product Details (Database Version - ✅ UPDATED FOR DISCOUNTS)
function getCartItems() {
    global $conn;
    if (!isLoggedIn()) return [];
    
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT c.quantity, p.*, c.id as cart_id 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ? AND p.status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        // ✅ Calculate discounted price
        $discount = (int)($row['discount_percentage'] ?? 0);
        $final_price = $row['price'] - ($row['price'] * $discount / 100);
        
        $row['final_price'] = $final_price;
        $row['discount_percentage'] = $discount;
        $row['subtotal'] = $final_price * $row['quantity']; // ✅ Use discounted price for subtotal
        
        $items[] = $row;
    }
    return $items;
}

// Remove from Cart (Database Version)
function removeFromCart($product_id) {
    global $conn;
    if (!isLoggedIn()) return;
    
    $user_id = $_SESSION['user_id'];
    $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

// Clear Cart (Database Version - After Order Placement)
function clearCart() {
    global $conn;
    if (!isLoggedIn()) return;
    
    $user_id = $_SESSION['user_id'];
    $sql = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
?>
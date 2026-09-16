<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="header-content">
        <a href="<?php echo SITE_URL; ?>/index.php" class="logo">
            <img src="<?php echo SITE_URL; ?>/assets/images/logo-premium.png" alt="Bloomify" class="header-logo-img">
        </a>
        
        <nav class="nav-links">
            <a href="<?php echo SITE_URL; ?>/index.php">Home</a>
            <a href="<?php echo SITE_URL; ?>/contact.php">Contact</a>
    
            <?php if (isLoggedIn()): ?>
                
                <?php if (isAdmin()): ?>
                    <a href="<?php echo SITE_URL; ?>/admin/index.php">Admin Dashboard</a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/my-orders.php">My Orders</a>
                    <a href="<?php echo SITE_URL; ?>/profile.php">My Profile</a>
                    <a href="<?php echo SITE_URL; ?>/cart.php">Cart (<?php echo getCartCount(); ?>)</a>
                    
                    <?php 
                    // Get wishlist count for the header
                    $wishlist_count = 0;
                    $wc_sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?";
                    $wc_stmt = $conn->prepare($wc_sql);
                    $wc_stmt->bind_param("i", $_SESSION['user_id']);
                    $wc_stmt->execute();
                    $wishlist_count = $wc_stmt->get_result()->fetch_assoc()['count'];
                    ?>
                    
                    <a href="<?php echo SITE_URL; ?>/wishlist.php"> Wishlist<?php if ($wishlist_count > 0) echo ' (' . $wishlist_count . ')'; ?></a>
                    
                <?php endif; ?>
                
                <a href="<?php echo SITE_URL; ?>/logout.php">Logout</a>
                
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>/login.php">Login</a>
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-secondary" style="padding: 8px 20px;">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container">
    <?php displayMessage(); ?>
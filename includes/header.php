<?php
require_once 'config.php';
require_once 'functions.php';
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
            <a href="<?php echo SITE_URL; ?>/about.php">About Us</a>
    
            <?php if (isLoggedIn()): ?>
             <!-- NEW: My Orders Link -->
            <a href="<?php echo SITE_URL; ?>/my-orders.php">My Orders</a>
        
            <?php if (isAdmin()): ?>
            <a href="<?php echo SITE_URL; ?>/admin/index.php">Admin Dashboard</a>
            <?php else: ?>
            <a href="<?php echo SITE_URL; ?>/profile.php">My Profile</a>
            <?php endif; ?>
        
            <a href="<?php echo SITE_URL; ?>/cart.php">Cart (<?php echo getCartCount(); ?>)</a>
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
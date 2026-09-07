<?php
// Must be included AFTER admin-auth.php
$page_title = isset($page_title) ? $page_title . ' - Admin' : 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/admin/assets/css/admin.css">
</head>
<body class="admin-body">

<?php
// Get new order count for notification badge
$new_orders_sql = "SELECT COUNT(*) as cnt FROM orders WHERE status IN ('pending', 'awaiting_payment')";
$new_orders_result = $conn->query($new_orders_sql);
$new_orders_count = $new_orders_result->fetch_assoc()['cnt'];
?>

<header class="admin-header">
    <div class="admin-header-content">
        <div class="admin-logo">
            <span style="font-size: 1.5rem;">🌸</span>
            <span>Bloomify Admin</span>
        </div>
        
        <nav class="admin-nav">
            <a href="<?php echo SITE_URL; ?>/admin/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                 Dashboard
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                Orders
                <?php if ($new_orders_count > 0): ?>
                    <span class="notification-badge"><?php echo $new_orders_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                 Products
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                Categories
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                Users
            </a>
            <a href="<?php echo SITE_URL; ?>/index.php" target="_blank">
                View Site
            </a>
            <a href="<?php echo SITE_URL; ?>/logout.php" class="admin-logout">
                 Logout
            </a>
        </nav>
    </div>
</header>

<main class="admin-container">
    <?php displayMessage(); ?>
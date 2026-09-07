<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Security Gate: Only admins can access
if (!isLoggedIn() || !isAdmin()) {
    // FIXED: Use absolute SITE_URL to prevent /dashboard/ redirect
    header('Location: ' . SITE_URL . '/login.php?error=admin_required');
    exit();
}
?>
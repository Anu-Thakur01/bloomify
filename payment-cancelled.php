<?php
$page_title = 'Payment Cancelled';
require_once __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view this page.', 'danger');
}
?>

<style>
    .success-container { max-width: 700px; margin: 40px auto; padding: 20px; }
    .success-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        padding: 50px 40px;
        text-align: center;
    }
    .success-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        font-size: 3rem;
        color: white;
        animation: scaleIn 0.5s ease;
    }
    @keyframes scaleIn {
        0% { transform: scale(0); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    .success-title { font-size: 2.2rem; color: #2d6a4f; margin-bottom: 15px; font-weight: 800; }
    .success-message { color: #6c757d; font-size: 1.1rem; margin-bottom: 35px; }
    
    .action-buttons { display: flex; gap: 15px; justify-content: center; margin-top: 35px; flex-wrap: wrap; }
    
    .btn-my-orders {
        padding: 14px 30px;
        background: white;
        color: #2d6a4f;
        border: 2px solid #2d6a4f;
        border-radius: 8px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s;
    }
    .btn-my-orders:hover { background: #f0f9f4; transform: translateY(-2px); }
    
    .btn-continue {
        padding: 14px 30px;
        background: linear-gradient(135deg, #40916c 0%, #2d6a4f 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s;
    }
    .btn-continue:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(64, 145, 108, 0.3); }
</style>

<div class="success-container">
    <div class="success-card">
        <div class="success-icon">✕</div>
        <h1 class="success-title">Payment Cancelled</h1>
        <p class="success-message">Your payment was not completed. Don't worry, your cart items are still saved and ready for you!</p>
        
        <div class="action-buttons">
            <a href="<?php echo SITE_URL; ?>/cart.php" class="btn-my-orders">My Cart</a>
            <a href="<?php echo SITE_URL; ?>/checkout.php" class="btn-continue">Retry Payment</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
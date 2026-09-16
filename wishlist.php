<?php
$page_title = 'My Wishlist';
require_once __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view your wishlist.', 'danger');
}

$user_id = $_SESSION['user_id'];

// Fetch wishlist items with product details
$sql = "SELECT w.id as wishlist_id, p.id, p.name, p.price, p.stock, p.image, p.status 
        FROM wishlist w 
        JOIN products p ON w.product_id = p.id 
        WHERE w.user_id = ? 
        ORDER BY w.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist_items = $stmt->get_result();
?>

<style>
    .wishlist-container { max-width: 1000px; margin: 40px auto; padding: 20px; }
    .wishlist-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .wishlist-header h1 { font-size: 2rem; color: #2d6a4f; font-weight: 800; margin: 0; }
    .btn-back { color: #6c757d; text-decoration: none; font-weight: 600; transition: color 0.3s; }
    .btn-back:hover { color: #2d6a4f; }
    
    .wishlist-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }
    .wishlist-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border: 1px solid #f0f0f0; position: relative; }
    .wishlist-img { height: 220px; background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .wishlist-img img { width: 100%; height: 100%; object-fit: cover; }
    .wishlist-info { padding: 20px; }
    .wishlist-name { font-size: 1.1rem; font-weight: 700; color: #2d3748; margin-bottom: 8px; }
    .wishlist-price { font-size: 1.2rem; color: #2d6a4f; font-weight: 800; margin-bottom: 15px; }
    
    .btn-remove { 
        position: absolute; top: 10px; right: 10px; background: white; border: none; 
        width: 36px; height: 36px; border-radius: 50%; box-shadow: 0 2px 8px rgba(0,0,0,0.15); 
        cursor: pointer; color: #d90429; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; 
        transition: all 0.3s;
    }
    .btn-remove:hover { background: #d90429; color: white; transform: scale(1.1); }
    
    .empty-wishlist { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
    .empty-wishlist h3 { color: #2d6a4f; margin-bottom: 15px; }
</style>

<div class="wishlist-container">
    <div class="wishlist-header">
        <h1>❤️ My Wishlist</h1>
        <a href="products.php" class="btn-back">← Continue Shopping</a>
    </div>

    <?php if ($wishlist_items->num_rows > 0): ?>
        <div class="wishlist-grid">
            <?php while($item = $wishlist_items->fetch_assoc()): ?>
                <div class="wishlist-card">
                    <form method="POST" action="wishlist-action.php">
                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                        <input type="hidden" name="action" value="remove">
                        <button type="submit" class="btn-remove" title="Remove from wishlist">✕</button>
                    </form>
                    
                    <a href="product-details.php?id=<?php echo $item['id']; ?>">
                        <div class="wishlist-img">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <?php else: ?>
                                <span style="font-size: 4rem;">🌸</span>
                            <?php endif; ?>
                        </div>
                    </a>
                    
                    <div class="wishlist-info">
                        <h3 class="wishlist-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <div class="wishlist-price"><?php echo formatPrice($item['price']); ?></div>
                        <a href="product-details.php?id=<?php echo $item['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center; display: block; padding: 10px 0;">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-wishlist">
            <div style="font-size: 4rem; margin-bottom: 20px;">💔</div>
            <h3>Your wishlist is empty</h3>
            <p style="color: #6c757d; margin-bottom: 25px;">Save your favorite flowers here to buy them later!</p>
            <a href="products.php" class="btn btn-primary" style="padding: 12px 30px;">Browse Flowers</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
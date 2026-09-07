<?php
$page_title = 'Shopping Cart';
require_once __DIR__ . '/includes/header.php';

$cart_items = getCartItems();
$cart_total = getCartTotal();
?>

<style>
    .cart-container { max-width: 1000px; margin: 40px auto; padding: 20px; }
    .cart-table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
    .cart-table th { background: #2d6a4f; color: white; padding: 15px; text-align: left; font-weight: 600; }
    .cart-table td { padding: 20px 15px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .cart-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
    .cart-product-name { font-weight: 600; color: #2d3748; }
    
    /* NEW: Quantity Controls */
    .qty-controls { display: flex; align-items: center; gap: 10px; border: 2px solid #e2e8f0; border-radius: 8px; padding: 4px; width: fit-content; }
    .qty-btn { width: 32px; height: 32px; border: none; background: #f8f9fa; border-radius: 6px; cursor: pointer; font-size: 1.2rem; font-weight: 700; color: #2d6a4f; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
    .qty-btn:hover { background: #2d6a4f; color: white; }
    .qty-value { font-weight: 700; font-size: 1.1rem; min-width: 30px; text-align: center; }
    
    .btn-remove { color: #d90429; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: color 0.3s; }
    .btn-remove:hover { color: #b00320; text-decoration: underline; }
    
    .cart-summary { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-top: 30px; display: flex; justify-content: space-between; align-items: center; }
    .cart-total-label { font-size: 1.2rem; font-weight: 600; color: #2d3748; }
    .cart-total-amount { font-size: 1.8rem; font-weight: 800; color: #40916c; transition: all 0.3s; }
    
    .cart-actions { display: flex; gap: 15px; margin-top: 30px; }
    .btn-checkout { background: #40916c; color: white; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 1.1rem; transition: all 0.3s; }
    .btn-checkout:hover { background: #2d6a4f; transform: translateY(-2px); }
    .btn-continue { background: white; color: #40916c; border: 2px solid #40916c; padding: 13px 38px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 1.1rem; transition: all 0.3s; }
    .btn-continue:hover { background: #f0f9f4; }
    
    .empty-cart { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
    .empty-cart h3 { color: #2d6a4f; margin-bottom: 15px; font-size: 1.5rem; }
    
    /* Loading animation */
    .updating { opacity: 0.5; pointer-events: none; }
</style>

<div class="cart-container">
    <h2 style="color: #2d6a4f; margin-bottom: 30px; font-size: 2rem;">Your Shopping Cart </h2>

    <?php if (!empty($cart_items)): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="cart-body">
                <?php foreach ($cart_items as $item): 
                    $pid = isset($item['product_id']) ? $item['product_id'] : $item['id'];
                ?>
                    <tr data-product-id="<?php echo $pid; ?>" data-price="<?php echo $item['price']; ?>">
                        <td style="display: flex; align-items: center; gap: 15px;">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-img">
                            <?php else: ?>
                                <div class="cart-img" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">🌸</div>
                            <?php endif; ?>
                            <span class="cart-product-name"><?php echo htmlspecialchars($item['name']); ?></span>
                        </td>
                        <td><?php echo formatPrice($item['price']); ?></td>
                        <td>
                            <!-- NEW: Quantity Controls -->
                            <div class="qty-controls">
                                <button type="button" class="qty-btn decrease" onclick="updateQty(this, 'decrease')">−</button>
                                <span class="qty-value"><?php echo $item['quantity']; ?></span>
                                <button type="button" class="qty-btn increase" onclick="updateQty(this, 'increase')">+</button>
                            </div>
                        </td>
                        <td class="item-subtotal" style="font-weight: 700; color: #40916c;"><?php echo formatPrice($item['subtotal']); ?></td>
                        <td>
                            <a href="remove-from-cart.php?id=<?php echo $pid; ?>" class="btn-remove">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <div>
                <div class="cart-total-label">Total Amount</div>
                <div style="color: #6c757d; font-size: 0.9rem; margin-top: 5px;">Shipping calculated at checkout</div>
            </div>
            <div class="cart-total-amount" id="cart-total"><?php echo formatPrice($cart_total); ?></div>
        </div>

        <div class="cart-actions">
            <a href="index.php" class="btn-continue">← Continue Shopping</a>
            <a href="checkout.php" class="btn-checkout">Proceed to Checkout →</a>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <div style="font-size: 4rem; margin-bottom: 20px;"></div>
            <h3>Your cart is empty</h3>
            <p style="color: #6c757d; margin-bottom: 30px;">Looks like you haven't added any flowers yet.</p>
            <a href="index.php" class="btn btn-primary" style="width: auto; padding: 12px 30px;">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQty(btn, action) {
    const row = btn.closest('tr');
    const productId = row.dataset.productId;
    const price = parseFloat(row.dataset.price);
    const qtySpan = row.querySelector('.qty-value');
    const subtotalCell = row.querySelector('.item-subtotal');
    const totalEl = document.getElementById('cart-total');
    
    // Prevent double clicks
    if (row.classList.contains('updating')) return;
    row.classList.add('updating');
    
    fetch('update-cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ product_id: productId, action: action, price: price })
    })
    .then(res => res.json())
    .then(data => {
        row.classList.remove('updating');
        
        if (data.success) {
            if (data.removed) {
                // Item was removed (quantity went to 0)
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    // Check if cart is now empty
                    if (document.querySelectorAll('#cart-body tr').length === 0) {
                        location.reload();
                    }
                }, 300);
            } else {
                // Update quantity and prices
                qtySpan.textContent = data.new_qty;
                subtotalCell.textContent = data.item_subtotal;
                totalEl.textContent = data.new_total;
                
                // Visual feedback
                totalEl.style.transform = 'scale(1.1)';
                setTimeout(() => totalEl.style.transform = 'scale(1)', 200);
            }
            
            // Update cart count in header
            fetch('<?php echo SITE_URL; ?>/includes/header.php') // Optional: reload header for cart count
                .catch(() => {}); 
        } else {
            alert(data.message || 'Failed to update quantity');
        }
    })
    .catch(err => {
        row.classList.remove('updating');
        console.error('Error:', err);
        alert('Network error. Please try again.');
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
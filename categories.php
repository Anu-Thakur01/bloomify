<?php
$page_title = 'All Categories';
require_once __DIR__ . '/includes/header.php';

// Fetch ALL categories
$cat_sql = "SELECT id, name, image, description FROM categories ORDER BY name ASC";
$categories = $conn->query($cat_sql);
?>

<style>
    .btn-back-home { 
        display: inline-flex; align-items: center; gap: 8px; color: #40916c; 
        text-decoration: none; font-weight: 600; font-size: 0.95rem; 
        transition: all 0.3s; padding: 8px 16px; border-radius: 8px; 
        background: #f0f9f4;
    }
    .btn-back-home:hover { color: #2d6a4f; background: #e0f2e9; transform: translateX(-4px); }

    .page-header { padding: 60px 20px 40px; max-width: 1200px; margin: 0 auto; }
    
    /* CSS Grid to perfectly center the title while keeping the button on the left */
    .page-header-content { 
        display: grid; 
        grid-template-columns: 1fr auto 1fr; 
        align-items: center; 
        gap: 20px; 
    }
    .btn-back-home { justify-self: start; }
    .page-header h1 { justify-self: center; font-size: 2.5rem; color: #2d6a4f; font-weight: 800; margin: 0; }
    .page-header p { color: #6c757d; font-size: 1.1rem; margin: 15px 0 0 0; text-align: center; width: 100%; }
    
    .categories-container { padding: 0 20px 80px; max-width: 1200px; margin: 0 auto; }
    .category-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
    
    .category-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.06); transition: transform 0.3s, box-shadow 0.3s; text-decoration: none; color: inherit; border: 1px solid #f0f0f0; }
    .category-card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(45, 106, 79, 0.15); border-color: #40916c; }
    .category-img { width: 100%; height: 250px; object-fit: cover; transition: transform 0.5s; }
    .category-card:hover .category-img { transform: scale(1.08); }
    .category-info { padding: 25px; text-align: center; }
    .category-name { font-size: 1.4rem; font-weight: 700; color: #2d6a4f; margin-bottom: 10px; }
    .category-desc { color: #6c757d; font-size: 0.95rem; line-height: 1.6; margin: 0; }
    
    .empty-state { text-align: center; padding: 60px; color: #6c757d; font-size: 1.2rem; grid-column: 1/-1; }

    /* Mobile responsive adjustment */
    @media (max-width: 768px) {
        .page-header-content { 
            grid-template-columns: 1fr; 
            text-align: center; 
            gap: 15px; 
        }
        .btn-back-home { justify-self: center; }
        .page-header h1 { font-size: 2rem; }
    }
</style>

<div class="page-header">
    <div class="page-header-content">
        <a href="index.php" class="btn-back-home">← Back to Home</a>
        <h1>Shop by Category</h1>
        <div></div> <!-- Empty div to balance the 3-column grid -->
    </div>
    <p>Explore our wide variety of fresh flowers and arrangements.</p>
</div>

<div class="categories-container">
    <div class="category-grid">
        <?php if ($categories && $categories->num_rows > 0): ?>
            <?php while($cat = $categories->fetch_assoc()): ?>
                <a href="products.php?category=<?php echo $cat['id']; ?>" class="category-card">
                    <?php 
                    $cat_image = !empty($cat['image']) ? $cat['image'] : '';
                    $colors = ['e8f5e9', 'fff3e0', 'fce4ec', 'e3f2fd', 'f3e5f5'];
                    $placeholder_color = $colors[$cat['id'] % count($colors)];
                    $img_src = !empty($cat_image) ? SITE_URL . '/assets/images/' . htmlspecialchars($cat_image) : 'https://via.placeholder.com/400x300/' . $placeholder_color . '/2d6a4f?text=' . urlencode($cat['name']);
                    ?>
                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="category-img">
                    <div class="category-info">
                        <h3 class="category-name"><?php echo htmlspecialchars($cat['name']); ?></h3>
                        <?php if (!empty($cat['description'])): ?>
                            <p class="category-desc"><?php echo htmlspecialchars($cat['description']); ?></p>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">No categories available yet. Please check back soon!</div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
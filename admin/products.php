<?php
$page_title = 'Manage Products';
require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/includes/admin-header.php';

$upload_dir = __DIR__ . '/../assets/images/';
$message = '';
$message_type = '';

// --- HANDLE FORM SUBMISSIONS ---

// 1. ADD PRODUCT
if (isset($_POST['add_product'])) {
    $name = sanitize($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $stock = (int)($_POST['stock'] ?? 0);
    $description = sanitize($_POST['description']);
    $status = sanitize($_POST['status']);
    $image_name = 'default-flower.jpg';

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed)) {
            $image_name = 'flower_' . time() . '_' . rand(100, 999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
        }
    }

    $sql = "INSERT INTO products (name, category_id, price, stock_quantity, description, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidisss", $name, $category_id, $price, $stock, $description, $image_name, $status);
    
    if ($stmt->execute()) {
        $message = "Product added successfully!";
        $message_type = "success";
    } else {
        $message = "Failed to add product: " . $conn->error;
        $message_type = "danger";
    }
}

// 2. EDIT PRODUCT
if (isset($_POST['edit_product'])) {
    $id = (int)$_POST['product_id'];
    $name = sanitize($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $stock = (int)($_POST['stock'] ?? 0);
    $description = sanitize($_POST['description']);
    $status = sanitize($_POST['status']);
    $image_name = sanitize($_POST['current_image']);

    // Handle New Image Upload (if provided)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed)) {
            if ($image_name !== 'default-flower.jpg' && file_exists($upload_dir . $image_name)) {
                unlink($upload_dir . $image_name);
            }
            $image_name = 'flower_' . time() . '_' . rand(100, 999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
        }
    }

    $sql = "UPDATE products SET name = ?, category_id = ?, price = ?, stock_quantity = ?, description = ?, image = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidisssi", $name, $category_id, $price, $stock, $description, $image_name, $status, $id);
    
    if ($stmt->execute()) {
        $message = "Product updated successfully!";
        $message_type = "success";
    } else {
        $message = "Failed to update product: " . $conn->error;
        $message_type = "danger";
    }
}

// 3. DELETE PRODUCT
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if ($product) {
        if ($product['image'] !== 'default-flower.jpg' && file_exists($upload_dir . $product['image'])) {
            unlink($upload_dir . $product['image']);
        }
        
        $del_stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $del_stmt->bind_param("i", $id);
        if ($del_stmt->execute()) {
            $message = "Product deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Failed to delete product.";
            $message_type = "danger";
        }
    }
    header("Location: products.php?msg=" . urlencode($message) . "&type=" . $message_type);
    exit();
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $message_type = $_GET['type'];
}

// Fetch Products & Categories
$products = $conn->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");

// Fetch specific product for Edit Form
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_product = $stmt->get_result()->fetch_assoc();
}

// Determine if form should be visible
$show_form = isset($_GET['add']) || $edit_product || ($message_type === 'danger' && isset($_POST['add_product']));
?>

<style>
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #6c757d; text-decoration: none; font-weight: 600; font-size: 0.95rem; margin-bottom: 15px; transition: all 0.3s; }
    .btn-back:hover { color: #2d6a4f; transform: translateX(-4px); }
    
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .btn-add { background: #2d6a4f; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; border: none; cursor: pointer; }
    .btn-add:hover { background: #1b4332; transform: translateY(-2px); }
    
    .form-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; display: <?php echo $show_form ? 'block' : 'none'; ?>; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-weight: 600; color: #4a5568; margin-bottom: 6px; font-size: 0.9rem; }
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: border 0.3s; box-sizing: border-box; }
    .form-control:focus { outline: none; border-color: #2d6a4f; }
    textarea.form-control { resize: vertical; min-height: 80px; }
    
    .form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0; }
    .btn-cancel { background: #f8f9fa; color: #4a5568; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid #e2e8f0; cursor: pointer; }
    .btn-save { background: #2d6a4f; color: white; padding: 10px 24px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; }
    .btn-save:hover { background: #1b4332; }

    .product-img-sm { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
    .btn-action { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600; margin-right: 5px; display: inline-block; }
    .btn-edit { background: #dbeafe; color: #1e40af; }
    .btn-edit:hover { background: #bfdbfe; }
    .btn-delete { background: #fee2e2; color: #991b1b; }
    .btn-delete:hover { background: #fecaca; }
    
    .status-active { color: #065f46; background: #d1fae5; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    .status-inactive { color: #991b1b; background: #fee2e2; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }

    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
</style>

<a href="index.php" class="btn-back">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    Back to Dashboard
</a>

<div class="page-header">
    <h2 class="section-title" style="margin:0;">🌺 Manage Products</h2>
    <?php if (!$show_form): ?>
        <button type="button" class="btn-add" onclick="toggleForm(true)">+ Add New Product</button>
    <?php endif; ?>
</div>

<?php if ($message): ?>
    <div class="alert" style="padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; background: <?php echo $message_type === 'success' ? '#d1fae5' : '#fee2e2'; ?>; color: <?php echo $message_type === 'success' ? '#065f46' : '#991b1b'; ?>; border: 1px solid <?php echo $message_type === 'success' ? '#a7f3d0' : '#fecaca'; ?>;">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<!-- ADD / EDIT FORM -->
<div id="product-form" class="form-card">
    <h3 style="margin-top:0; color:#2d6a4f;"><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?></h3>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($edit_product): ?>
            <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($edit_product['image'] ?? 'default-flower.jpg'); ?>">
        <?php endif; ?>
        
        <div class="form-grid">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g., Red Rose Bouquet" value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    <?php 
                    if ($categories && $categories->num_rows > 0) {
                        $categories->data_seek(0);
                        while($cat = $categories->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_product && $edit_product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php 
                        endwhile;
                    } else {
                        echo '<option value="" disabled>No categories found. Please add categories first.</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Price (Rs.) *</label>
                <input type="number" step="0.01" min="0" name="price" class="form-control" placeholder="0.00" value="<?php echo $edit_product ? htmlspecialchars($edit_product['price']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Stock Quantity *</label>
                <input type="number" min="0" name="stock" class="form-control" placeholder="0" value="<?php echo $edit_product ? htmlspecialchars($edit_product['stock_quantity'] ?? '0') : '0'; ?>" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="active" <?php echo ($edit_product && $edit_product['status'] == 'active') ? 'selected' : (!$edit_product ? 'selected' : ''); ?>>✅ Active</option>
                    <option value="inactive" <?php echo ($edit_product && $edit_product['status'] == 'inactive') ? 'selected' : ''; ?>>⛔ Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label>Product Image <?php echo !$edit_product ? '*' : ''; ?></label>
                <input type="file" name="image" class="form-control" accept="image/*" <?php echo !$edit_product ? 'required' : ''; ?>>
                <small style="color:#6c757d; display:block; margin-top:5px;">Allowed: JPG, PNG, WEBP. Max size: 2MB</small>
                <?php if ($edit_product && !empty($edit_product['image'])): ?>
                    <div style="margin-top:10px;">
                        <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo htmlspecialchars($edit_product['image']); ?>" alt="Current" style="width:80px; height:80px; object-fit:cover; border-radius:8px; border:2px solid #e2e8f0;">
                        <br><small>Current image</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="form-group">
            <label>Description *</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Enter product description..." required><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="toggleForm(false)">Cancel</button>
            <button type="submit" name="<?php echo $edit_product ? 'edit_product' : 'add_product'; ?>" class="btn-save">
                <?php echo $edit_product ? ' Update Product' : 'Save Product'; ?>
            </button>
        </div>
    </form>
</div>

<script>
function toggleForm(show) {
    const form = document.getElementById('product-form');
    if (show) {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        form.style.display = 'none';
        // Optional: reload to clear form state cleanly
        window.location.href = 'products.php'; 
    }
}
</script>

<!-- PRODUCTS TABLE -->
<table class="admin-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($products && $products->num_rows > 0): ?>
            <?php while($p = $products->fetch_assoc()): ?>
                <tr>
                    <td>
                        <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo htmlspecialchars($p['image'] ?? 'default-flower.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-img-sm"
                             onerror="this.src='https://via.placeholder.com/50?text=No+Img'">
                    </td>
                    <td style="font-weight: 600;"><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?></td>
                    <td style="font-weight: 700; color: #2d6a4f;"><?php echo formatPrice($p['price']); ?></td>
                    <td><?php echo $p['stock_quantity'] ?? '0'; ?></td>
                    <td>
                        <span class="<?php echo $p['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo ucfirst($p['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="?edit=<?php echo $p['id']; ?>" class="btn-action btn-edit">Edit</a>
                        <a href="?delete=<?php echo $p['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this product? This cannot be undone.');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 50px; color: #718096;">
                    No products found. Add your first flower! 🌸
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
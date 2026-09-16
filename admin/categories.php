<?php
$page_title = 'Manage Categories';
require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/includes/admin-header.php';

$upload_dir = __DIR__ . '/../assets/images/';
$message = '';
$message_type = '';

// Ensure upload directory exists and is writable to prevent hanging
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// --- HANDLE FORM SUBMISSIONS ---

// 1. ADD CATEGORY
if (isset($_POST['add_category'])) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $image_name = 'default-category.jpg';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed)) {
            $image_name = 'category_' . time() . '_' . rand(100, 999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
        }
    }
    
    $sql = "INSERT INTO categories (name, description, image) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $description, $image_name);
    
    if ($stmt->execute()) {
        $message = "Category added successfully!";
        $message_type = "success";
    } else {
        $message = "Failed to add category: " . $conn->error;
        $message_type = "danger";
    }
}

// 2. EDIT CATEGORY
if (isset($_POST['edit_category'])) {
    $id = (int)$_POST['category_id'];
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $image_name = sanitize($_POST['current_image']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed)) {
            if ($image_name !== 'default-category.jpg' && file_exists($upload_dir . $image_name)) {
                unlink($upload_dir . $image_name);
            }
            $image_name = 'category_' . time() . '_' . rand(100, 999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
        }
    }
    
    $sql = "UPDATE categories SET name = ?, description = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $description, $image_name, $id);
    
    if ($stmt->execute()) {
        $message = "Category updated successfully!";
        $message_type = "success";
    } else {
        $message = "Failed to update category: " . $conn->error;
        $message_type = "danger";
    }
}

// 3. DELETE CATEGORY
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Fast count check
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $product_count = $check_stmt->get_result()->fetch_assoc()['count'];
    
    if ($product_count > 0) {
        $message = "Cannot delete. Assigned to $product_count product(s).";
        $message_type = "danger";
    } else {
        $img_stmt = $conn->prepare("SELECT image FROM categories WHERE id = ?");
        $img_stmt->bind_param("i", $id);
        $img_stmt->execute();
        $cat_img = $img_stmt->get_result()->fetch_assoc()['image'] ?? 'default-category.jpg';

        if ($cat_img !== 'default-category.jpg' && file_exists($upload_dir . $cat_img)) {
            unlink($upload_dir . $cat_img);
        }
        
        $del_stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $del_stmt->bind_param("i", $id);
        
        if ($del_stmt->execute()) {
            $message = "Category deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Failed to delete category.";
            $message_type = "danger";
        }
    }
    
    header("Location: categories.php?msg=" . urlencode($message) . "&type=" . $message_type);
    exit();
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $message_type = $_GET['type'];
}

// ⚡ OPTIMIZED QUERY: Uses a fast subquery instead of a slow LEFT JOIN + GROUP BY
$sql = "SELECT c.id, c.name, c.description, c.image, c.created_at, 
               (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count 
        FROM categories c 
        ORDER BY c.id DESC";
$categories = $conn->query($sql);

$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_category = $stmt->get_result()->fetch_assoc();
}

$show_form = isset($_GET['add']) || $edit_category || ($message_type === 'danger' && isset($_POST['add_category']));
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
    textarea.form-control { resize: vertical; min-height: 100px; }
    .form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0; }
    .btn-cancel { background: #f8f9fa; color: #4a5568; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid #e2e8f0; cursor: pointer; }
    .btn-save { background: #2d6a4f; color: white; padding: 10px 24px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; }
    .btn-save:hover { background: #1b4332; }
    .cat-img-sm { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
    .btn-action { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600; margin-right: 5px; display: inline-block; }
    .btn-edit { background: #dbeafe; color: #1e40af; }
    .btn-edit:hover { background: #bfdbfe; }
    .btn-delete { background: #fee2e2; color: #991b1b; }
    .btn-delete:hover { background: #fecaca; }
    .badge-count { background: #e2e8f0; color: #4a5568; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
</style>

<a href="index.php" class="btn-back">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    Back to Dashboard
</a>

<div class="page-header">
    <h2 class="section-title" style="margin:0;">Manage Categories</h2>
    <?php if (!$show_form): ?>
        <button type="button" class="btn-add" onclick="toggleForm(true)">+ Add New Category</button>
    <?php endif; ?>
</div>

<?php if ($message): ?>
    <div class="alert" style="padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; background: <?php echo $message_type === 'success' ? '#d1fae5' : '#fee2e2'; ?>; color: <?php echo $message_type === 'success' ? '#065f46' : '#991b1b'; ?>; border: 1px solid <?php echo $message_type === 'success' ? '#a7f3d0' : '#fecaca'; ?>;">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div id="category-form" class="form-card">
    <h3 style="margin-top:0; color:#2d6a4f;"><?php echo $edit_category ? 'Edit Category' : '➕ Add New Category'; ?></h3>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($edit_category): ?>
            <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($edit_category['image'] ?? 'default-category.jpg'); ?>">
        <?php endif; ?>
        
        <div class="form-grid">
            <div class="form-group">
                <label>Category Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g., Roses, Lilies" value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Category Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <?php if ($edit_category && !empty($edit_category['image'])): ?>
                    <small style="color:#6c757d; display:block; margin-top:5px;">Current: <?php echo htmlspecialchars($edit_category['image']); ?></small>
                    <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo htmlspecialchars($edit_category['image']); ?>" alt="Current" style="width:60px; height:60px; object-fit:cover; border-radius:8px; margin-top:8px; border:1px solid #e2e8f0;">
                <?php endif; ?>
            </div>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" placeholder="Brief description..."><?php echo $edit_category ? htmlspecialchars($edit_category['description']) : ''; ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="toggleForm(false)">Cancel</button>
            <button type="submit" name="<?php echo $edit_category ? 'edit_category' : 'add_category'; ?>" class="btn-save">
                <?php echo $edit_category ? ' Update Category' : '✨ Save Category'; ?>
            </button>
        </div>
    </form>
</div>

<script>
function toggleForm(show) {
    const form = document.getElementById('category-form');
    if (show) {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        window.location.href = 'categories.php'; 
    }
}
</script>

<table class="admin-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Category Name</th>
            <th>Description</th>
            <th>Products</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($categories && $categories->num_rows > 0): ?>
            <?php while($cat = $categories->fetch_assoc()): ?>
                <tr>
                    <td>
                        <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo htmlspecialchars($cat['image'] ?? 'default-category.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($cat['name']); ?>" class="cat-img-sm"
                             onerror="this.src='https://via.placeholder.com/50?text=No+Img'">
                    </td>
                    <td style="font-weight: 600; font-size: 1.05rem;"><?php echo htmlspecialchars($cat['name']); ?></td>
                    <td style="color: #6c757d; font-size: 0.9rem; max-width: 300px;"><?php echo htmlspecialchars($cat['description'] ?: 'No description'); ?></td>
                    <td><span class="badge-count"><?php echo $cat['product_count']; ?> Product(s)</span></td>
                    <td>
                        <a href="?edit=<?php echo $cat['id']; ?>" class="btn-action btn-edit">Edit</a>
                        <a href="?delete=<?php echo $cat['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Delete this category?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 50px; color: #718096;">No categories found. Add your first category! </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
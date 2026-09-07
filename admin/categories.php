<?php
$page_title = 'Manage Categories';
require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/includes/admin-header.php';

$message = '';
$message_type = '';

// --- HANDLE FORM SUBMISSIONS ---

// 1. ADD CATEGORY
if (isset($_POST['add_category'])) {
    $name = sanitize($_POST['name']);
    
    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    
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
    
    $sql = "UPDATE categories SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $id);
    
    if ($stmt->execute()) {
        $message = "Category updated successfully!";
        $message_type = "success";
    } else {
        $message = "Failed to update category: " . $conn->error;
        $message_type = "danger";
    }
}

// 3. DELETE CATEGORY (With Safety Check)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Check if any products are using this category
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $product_count = $check_stmt->get_result()->fetch_assoc()['count'];
    
    if ($product_count > 0) {
        $message = "Cannot delete category. It is currently assigned to $product_count product(s). Please reassign or delete those products first.";
        $message_type = "danger";
    } else {
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
    
    // Redirect to clear GET params
    header("Location: categories.php?msg=" . urlencode($message) . "&type=" . $message_type);
    exit();
}

// Fetch message from redirect
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $message_type = $_GET['type'];
}

// Fetch Categories with Product Counts
$sql = "SELECT c.id, c.name, c.created_at, COUNT(p.id) as product_count 
        FROM categories c 
        LEFT JOIN products p ON c.id = p.category_id 
        GROUP BY c.id 
        ORDER BY c.id DESC";
$categories = $conn->query($sql);

// Fetch specific category for Edit Form
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_category = $stmt->get_result()->fetch_assoc();
}

// Determine if form should be visible
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
    
    .form-group { margin-bottom: 20px; max-width: 500px; }
    .form-group label { display: block; font-weight: 600; color: #4a5568; margin-bottom: 6px; font-size: 0.9rem; }
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: border 0.3s; box-sizing: border-box; }
    .form-control:focus { outline: none; border-color: #2d6a4f; }
    
    .form-actions { display: flex; gap: 10px; justify-content: flex-start; margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0; }
    .btn-cancel { background: #f8f9fa; color: #4a5568; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid #e2e8f0; cursor: pointer; }
    .btn-save { background: #2d6a4f; color: white; padding: 10px 24px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; }
    .btn-save:hover { background: #1b4332; }

    .btn-action { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600; margin-right: 5px; display: inline-block; }
    .btn-edit { background: #dbeafe; color: #1e40af; }
    .btn-edit:hover { background: #bfdbfe; }
    .btn-delete { background: #fee2e2; color: #991b1b; }
    .btn-delete:hover { background: #fecaca; }
    
    .badge-count { background: #e2e8f0; color: #4a5568; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
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

<!-- ADD / EDIT FORM -->
<div id="category-form" class="form-card">
    <h3 style="margin-top:0; color:#2d6a4f;"><?php echo $edit_category ? ' Edit Category' : 'Add New Category'; ?></h3>
    <form method="POST">
        <?php if ($edit_category): ?>
            <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Category Name *</label>
            <input type="text" name="name" class="form-control" placeholder="e.g., Roses, Lilies, Seasonal" value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : ''; ?>" required>
        </div>
        
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="toggleForm(false)"> Cancel</button>
            <button type="submit" name="<?php echo $edit_category ? 'edit_category' : 'add_category'; ?>" class="btn-save">
                <?php echo $edit_category ? ' Update Category' : 'Save Category'; ?>
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

<!-- CATEGORIES TABLE -->
<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Products Assigned</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($categories && $categories->num_rows > 0): ?>
            <?php while($cat = $categories->fetch_assoc()): ?>
                <tr>
                    <td style="color: #718096;">#<?php echo $cat['id']; ?></td>
                    <td style="font-weight: 600; font-size: 1.05rem;"><?php echo htmlspecialchars($cat['name']); ?></td>
                    <td><span class="badge-count"><?php echo $cat['product_count']; ?> Product(s)</span></td>
                    <td style="color: #718096; font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($cat['created_at'])); ?></td>
                    <td>
                        <a href="?edit=<?php echo $cat['id']; ?>" class="btn-action btn-edit">Edit</a>
                        <a href="?delete=<?php echo $cat['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 50px; color: #718096;">
                    No categories found. Add your first category! 
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
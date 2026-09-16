<?php
$page_title = 'Manage Reviews';
require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/includes/admin-header.php';

$message = '';
$message_type = '';

// Handle Reply Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_id'])) {
    $reply_id = (int)$_POST['reply_id'];
    $admin_reply = sanitize($_POST['admin_reply']);
    
    $stmt = $conn->prepare("UPDATE reviews SET admin_reply = ? WHERE id = ?");
    $stmt->bind_param("si", $admin_reply, $reply_id);
    
    if ($stmt->execute()) {
        $message = "Reply saved successfully!";
        $message_type = "success";
    }
}

// Handle Deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $review_id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $review_id);
    if ($stmt->execute()) {
        $message = "Review deleted successfully!";
        $message_type = "success";
    }
}

// Fetch all reviews
$sql = "SELECT r.*, p.name as product_name, u.name as user_name 
        FROM reviews r 
        JOIN products p ON r.product_id = p.id 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.created_at DESC";
$reviews = $conn->query($sql);
?>

<style>
    .review-card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .review-meta { color: #6c757d; font-size: 0.9rem; }
    .review-stars { color: #ffd700; font-size: 1.2rem; margin: 10px 0; }
    .review-comment { color: #2d3748; line-height: 1.6; padding: 15px; background: #f8f9fa; border-radius: 8px; margin: 15px 0; }
    
    .admin-reply-box { background: #f0f9f4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 15px; margin-top: 15px; }
    .admin-reply-box textarea { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 10px; resize: vertical; min-height: 60px; font-family: inherit; }
    .admin-reply-box textarea:focus { outline: none; border-color: #40916c; }
    
    .action-buttons { display: flex; gap: 10px; margin-top: 15px; }
    .btn-save-reply { background: #40916c; color: white; padding: 8px 16px; border-radius: 6px; border: none; font-weight: 600; cursor: pointer; }
    .btn-save-reply:hover { background: #2d6a4f; }
    .btn-delete { background: #dc3545; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
    .btn-delete:hover { background: #c82333; }
    .published-badge { background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600; }
    .existing-reply { background: #e0f2e9; padding: 12px; border-radius: 6px; margin-bottom: 10px; color: #155724; font-style: italic; }
</style>

<h2 style="color: #2d6a4f; margin-bottom: 30px;">📝 Product Reviews Management</h2>

<?php if ($message): ?>
    <div style="padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; background: <?php echo $message_type === 'success' ? '#d1fae5' : '#fee2e2'; ?>; color: <?php echo $message_type === 'success' ? '#065f46' : '#991b1b'; ?>;">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<?php if ($reviews->num_rows > 0): ?>
    <?php while($review = $reviews->fetch_assoc()): ?>
        <div class="review-card">
            <div class="review-header">
                <div>
                    <strong><?php echo htmlspecialchars($review['user_name']); ?></strong> 
                    <span style="color: #6c757d;">reviewed</span> 
                    <strong><?php echo htmlspecialchars($review['product_name']); ?></strong>
                </div>
                <span class="published-badge">✓ Published</span>
            </div>
            
            <div class="review-meta">
                <?php echo date('F d, Y \a\t g:i A', strtotime($review['created_at'])); ?>
            </div>
            
            <div class="review-stars">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <?php if($i <= $review['rating']): ?>★<?php else: ?>☆<?php endif; ?>
                <?php endfor; ?>
            </div>
            
            <div class="review-comment">
                <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
            </div>
            
            <!-- ✅ ADMIN REPLY SECTION -->
            <form method="POST" action="reviews.php">
                <input type="hidden" name="reply_id" value="<?php echo $review['id']; ?>">
                <div class="admin-reply-box">
                    <?php if (!empty($review['admin_reply'])): ?>
                        <div class="existing-reply">
                            <strong>Current Reply:</strong> <?php echo nl2br(htmlspecialchars($review['admin_reply'])); ?>
                        </div>
                    <?php endif; ?>
                    <label style="font-weight: 600; color: #2d6a4f; display: block; margin-bottom: 8px;">Admin Reply:</label>
                    <textarea name="admin_reply" placeholder="Type your reply to the customer here..."><?php echo htmlspecialchars($review['admin_reply']); ?></textarea>
                    <button type="submit" class="btn-save-reply">Save Reply</button>
                </div>
            </form>
            
            <div class="action-buttons">
                <a href="?action=delete&id=<?php echo $review['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this review?');">
                     Delete Review
                </a>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align: center; padding: 60px; color: #6c757d; background: white; border-radius: 12px;">No reviews yet.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
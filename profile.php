<?php
$page_title = 'My Profile';
require_once __DIR__ . '/includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view your profile.', 'danger');
}

$user_id = $_SESSION['user_id'];

// Fetch current user details
$sql = "SELECT name, email, phone, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);

    // Validation
    if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        redirect('profile.php', 'Name can only contain letters and spaces.', 'danger');
    }
    
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        redirect('profile.php', 'Phone number must be exactly 10 digits.', 'danger');
    }

    // Update Database
    $update_sql = "UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssi", $name, $phone, $address, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['user_name'] = $name; // Update session name
        redirect('profile.php', 'Profile updated successfully!', 'success');
    } else {
        redirect('profile.php', 'Failed to update profile. Please try again.', 'danger');
    }
}
?>

<style>
    .profile-container { max-width: 600px; margin: 40px auto; padding: 20px; }
    .profile-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
    .profile-header { text-align: center; margin-bottom: 30px; }
    .profile-avatar { width: 80px; height: 80px; background: linear-gradient(135deg, #40916c 0%, #2d6a4f 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 15px; font-weight: 700; }
    .profile-title { font-size: 1.8rem; color: #2d6a4f; font-weight: 700; margin-bottom: 5px; }
    .profile-subtitle { color: #6c757d; font-size: 0.95rem; }
    
    .form-group-profile { margin-bottom: 20px; }
    .form-group-profile label { display: block; margin-bottom: 8px; font-weight: 600; color: #2d3748; font-size: 0.95rem; }
    .form-control-profile { width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; transition: all 0.3s; background: #f8f9fa; }
    .form-control-profile:focus { outline: none; border-color: #40916c; background: white; box-shadow: 0 0 0 3px rgba(64, 145, 108, 0.1); }
    .form-control-profile[readonly] { background: #e9ecef; cursor: not-allowed; color: #6c757d; }
    
    .btn-save { width: 100%; padding: 14px; background: linear-gradient(135deg, #40916c 0%, #2d6a4f 100%); color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s; margin-top: 10px; }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(64, 145, 108, 0.3); }
</style>

<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
            <h1 class="profile-title">My Profile</h1>
            <p class="profile-subtitle">Manage your personal information</p>
        </div>

        <form method="POST" action="profile.php">
            <div class="form-group-profile">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control-profile" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            
            <div class="form-group-profile">
                <label>Email Address</label>
                <input type="email" class="form-control-profile" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                <small style="color: #6c757d; font-size: 0.85rem;">Email cannot be changed</small>
            </div>
            
            <div class="form-group-profile">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control-profile" value="<?php echo htmlspecialchars($user['phone']); ?>" maxlength="10" placeholder="98XXXXXXXX" required>
            </div>
            
            <div class="form-group-profile">
                <label>Delivery Address</label>
                <textarea name="address" class="form-control-profile" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            
            <button type="submit" class="btn-save">Save Changes</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
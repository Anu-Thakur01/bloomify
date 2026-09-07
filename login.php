<?php
$page_title = 'Login';
require_once __DIR__ . '/includes/header.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, name, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                // Admins always go to dashboard
                redirect('admin/index.php', 'Welcome back, Admin!', 'success');
            } else {
                // Users go to where they wanted, or home by default
                $return_url = getReturnUrl();
                redirect($return_url, 'Login successful!', 'success');
            }
        } else {
            redirect('login.php', 'Invalid email or password.', 'danger');
        }
    } else {
        redirect('login.php', 'Invalid email or password.', 'danger');
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-image">
        <img src="<?php echo SITE_URL; ?>/assets/images/flower-login.svg" alt="Bloomify Shop">
    </div>
    
    <div class="auth-form-container">
        <div class="form-container" style="max-width: 400px;">
            <h2 style="text-align: center; color: #2d6a4f; margin-bottom: 10px;">Welcome Back</h2>
            <p style="text-align: center; color: #666; margin-bottom: 30px; font-size: 0.95rem;">
                Login to your Bloomify account
            </p>
            
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password', this)">👁️</button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
            
            <p style="text-align: center; margin-top: 25px; font-size: 0.95rem;">
                Don't have an account? <a href="register.php" style="color: #40916c; font-weight: 600;">Register here</a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
    } else {
        input.type = 'password';
        btn.textContent = '👁️';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
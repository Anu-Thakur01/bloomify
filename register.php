<?php
$page_title = 'Register';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);

    // Server-side Validation
    if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        redirect('register.php', 'Name can only contain letters and spaces.', 'danger');
    }
    
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        redirect('register.php', 'Phone number must be exactly 10 digits.', 'danger');
    }

    if (strlen($password) < 6) {
        redirect('register.php', 'Password must be at least 6 characters.', 'danger');
    }

    // Check if email exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        redirect('register.php', 'Email already registered. Please login.', 'danger');
    } else {
        // Insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_sql = "INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, 'user')";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sssss", $name, $email, $hashed_password, $phone, $address);
        
        if ($stmt->execute()) {
            redirect('login.php', 'Registration successful! Please login.', 'success');
        } else {
            redirect('register.php', 'Registration failed. Please try again.', 'danger');
        }
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-image">
        <img src="<?php echo SITE_URL; ?>/assets/images/flower-register.svg" alt="Bloomify Flowers">
    </div>
    
    <div class="auth-form-container">
        <div class="form-container compact-form" style="max-width: 580px;">
            <h2 style="text-align: center; color: #2d6a4f; margin-bottom: 10px;">Create an Account</h2>
            <p style="text-align: center; color: #666; margin-bottom: 30px; font-size: 0.95rem;">
                Join Bloomify and order beautiful flowers
            </p>
            
            <form method="POST" action="register.php" id="registerForm" novalidate>
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name <span style="color: #d90429; font-size: 0.8rem;">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" autocomplete="off" required>
                        <small class="validation-msg" id="nameError">Only letters and spaces allowed</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address <span style="color: #d90429; font-size: 0.8rem;">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" required>
                        <small class="validation-msg" id="emailError">Please enter a valid email</small>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password <span style="color: #d90429; font-size: 0.8rem;">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" class="form-control" minlength="6" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password', this)">👁️</button>
                        </div>
                        <small class="validation-msg" id="passwordError">Minimum 6 characters required</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number <span style="color: #d90429; font-size: 0.8rem;">*</span></label>
                        <input type="text" id="phone" name="phone" class="form-control" maxlength="10" autocomplete="off" required>
                        <small class="validation-msg" id="phoneError">Must be exactly 10 digits</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address <span style="color: #d90429; font-size: 0.8rem;">*</span></label>
                    <textarea id="address" name="address" class="form-control" rows="1" style="min-height: 40px;" required></textarea>
                    <small class="validation-msg" id="addressError">Please enter a valid address</small>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Register</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px; font-size: 0.95rem;">
                Already have an account? <a href="login.php" style="color: #40916c; font-weight: 600;">Login here</a>
            </p>
        </div>
    </div>
</div>

<script>
// Toggle Password Visibility
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

// BLOCK invalid characters in Name field (only letters and spaces)
document.getElementById('name').addEventListener('keypress', function(e) {
    if (!/[a-zA-Z\s]/.test(e.key)) {
        e.preventDefault();
        const error = document.getElementById('nameError');
        error.style.display = 'block';
        setTimeout(() => { error.style.display = 'none'; }, 2000);
    }
});

// BLOCK non-numbers in Phone field and limit to 10 digits
document.getElementById('phone').addEventListener('keypress', function(e) {
    if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
        const error = document.getElementById('phoneError');
        error.style.display = 'block';
        setTimeout(() => { error.style.display = 'none'; }, 2000);
    }
    if (this.value.length >= 10) {
        e.preventDefault();
    }
});

// Validate email format on blur
document.getElementById('email').addEventListener('blur', function(e) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const error = document.getElementById('emailError');
    if (this.value !== '' && !emailRegex.test(this.value)) {
        error.style.display = 'block';
        this.style.borderColor = '#d90429';
    } else {
        error.style.display = 'none';
        this.style.borderColor = '#e0e0e0';
    }
});

// Validate password length on blur
document.getElementById('password').addEventListener('blur', function(e) {
    const error = document.getElementById('passwordError');
    if (this.value !== '' && this.value.length < 6) {
        error.style.display = 'block';
        this.style.borderColor = '#d90429';
    } else {
        error.style.display = 'none';
        this.style.borderColor = '#e0e0e0';
    }
});

// Final Form Submission Validation
document.getElementById('registerForm').addEventListener('submit', function(e) {
    let hasError = false;
    const name = document.getElementById('name').value;
    const phone = document.getElementById('phone').value;
    const password = document.getElementById('password').value;
    const email = document.getElementById('email').value;
    
    if (!/^[a-zA-Z\s]+$/.test(name)) {
        document.getElementById('nameError').style.display = 'block';
        document.getElementById('name').style.borderColor = '#d90429';
        hasError = true;
    }
    if (phone.length !== 10) {
        document.getElementById('phoneError').style.display = 'block';
        document.getElementById('phone').style.borderColor = '#d90429';
        hasError = true;
    }
    if (password.length < 6) {
        document.getElementById('passwordError').style.display = 'block';
        document.getElementById('password').style.borderColor = '#d90429';
        hasError = true;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById('emailError').style.display = 'block';
        document.getElementById('email').style.borderColor = '#d90429';
        hasError = true;
    }
    
    if (hasError) e.preventDefault();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
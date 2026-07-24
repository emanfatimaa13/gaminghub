<?php
$page_title = 'Register';
require_once 'config/database.php';
require_once 'config/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $fullName = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $result = registerUser($username, $email, $password, $fullName, $phone, $address);
        if ($result['success']) {
            setMessage('success', $result['message']);
            header('Location: login.php');
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4 gaming-logo">
                        Create Account
                    </h3>
                    
                    <p class="text-center text-muted mb-4">Join the gaming community</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label text-light">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Enter your full name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label text-light">Username *</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label text-light">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label text-light">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Min 6 characters" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label text-light">Confirm Password *</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label text-light">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label text-light">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter your address"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">Already have an account? <a href="login.php" class="text-primary fw-bold">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
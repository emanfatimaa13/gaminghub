<?php
$page_title = 'Login';
require_once 'config/database.php';
require_once 'config/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $result = loginUser($email, $password);
        if ($result['success']) {
            setMessage('success', $result['message']);
            header('Location: index.php');
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
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4 gaming-logo">
                        Welcome Back
                    </h3>
                    
                    <p class="text-center text-muted mb-4">Login to your account</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label text-light">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label text-light">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">Don't have an account? <a href="register.php" class="text-primary fw-bold">Register</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


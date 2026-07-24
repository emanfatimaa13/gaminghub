<?php
$page_title = 'Contact Us';
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $messageText = sanitize($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($messageText)) {
        setMessage('error', 'Please fill in all fields');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setMessage('error', 'Please enter a valid email address');
    } else {
        // In a real application, you would send an email here
        setMessage('success', 'Thank you for your message. We will get back to you soon!');
    }
}
?>

<div class="container py-5">
    <?php displayMessage(); ?>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="text-center mb-4 section-title">Contact Us</h2>
            
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <i class="fas fa-map-marker-alt fa-2x text-primary mb-2"></i>
                            <h6 class="text-light">Address</h6>
                            <p class="text-muted">Gaming City, Pakistan</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="fas fa-phone fa-2x text-primary mb-2"></i>
                            <h6 class="text-light">Phone</h6>
                            <p class="text-muted">+92 300 1234567</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                            <h6 class="text-light">Email</h6>
                            <p class="text-muted">info@gamingstore.com</p>
                        </div>
                    </div>
                    
                    <hr class="border-secondary">
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light">Your Name *</label>
                                <input type="text" class="form-control" name="name" placeholder="Enter your name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light">Email Address *</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-light">Subject *</label>
                            <input type="text" class="form-control" name="subject" placeholder="Enter subject" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-light">Message *</label>
                            <textarea class="form-control" name="message" rows="5" placeholder="Your message here..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.section-title {
    font-family: 'Orbitron', sans-serif;
    color: #ffffff;
    font-weight: 700;
    position: relative;
    display: inline-block;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: linear-gradient(135deg, #6c5ce7 0%, #00d2ff 100%);
    border-radius: 10px;
}

.card {
    background: #14142e !important;
    border: 1px solid #2a2a5a !important;
    border-radius: 12px;
}

.card-body {
    padding: 30px !important;
}

.form-label {
    color: #e0e0e0 !important;
    font-weight: 500;
}

.form-control {
    background: #1a1a3a !important;
    border: 1px solid #2a2a5a !important;
    color: #e0e0e0 !important;
    border-radius: 8px;
    padding: 12px 16px;
}

.form-control:focus {
    background: #1a1a3a !important;
    border-color: #6c5ce7 !important;
    box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25) !important;
    color: #e0e0e0 !important;
}

.form-control::placeholder {
    color: #8888aa !important;
}

.text-light {
    color: #e0e0e0 !important;
}

.text-muted {
    color: #b0b0d0 !important;
}

.text-primary {
    color: #a29bfe !important;
}

.btn-primary {
    background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
    border: none;
    border-radius: 10px;
    padding: 14px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(108, 92, 231, 0.4);
}

hr.border-secondary {
    border-color: #2a2a5a !important;
    opacity: 1;
}

/* Responsive */
@media (max-width: 576px) {
    .card-body {
        padding: 20px !important;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
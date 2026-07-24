<?php
$page_title = 'About Us';
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="text-center mb-4 section-title">About Gaming Store</h2>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="text-primary mb-3"><i class="fas fa-gamepad me-2"></i>Our Story</h5>
                    <p class="text-light">Welcome to Gaming Store, your premier destination for high-quality gaming accessories. We're passionate about gaming and committed to providing gamers with the best equipment to enhance their gaming experience.</p>
                    
                    <h5 class="text-primary mb-3 mt-4"><i class="fas fa-bullseye me-2"></i>Our Mission</h5>
                    <p class="text-light">Our mission is to deliver premium gaming accessories at competitive prices, ensuring every gamer can access the tools they need to succeed. We carefully select each product in our collection to meet the highest standards of quality and performance.</p>
                    
                    <h5 class="text-primary mb-3 mt-4"><i class="fas fa-star me-2"></i>Why Choose Us?</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card bg-dark-card p-3 h-100">
                                <i class="fas fa-check-circle text-success mb-2"></i>
                                <h6 class="text-light">Quality Products</h6>
                                <p class="text-muted small mb-0">We only stock products from trusted brands and manufacturers.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-dark-card p-3 h-100">
                                <i class="fas fa-tag text-warning mb-2"></i>
                                <h6 class="text-light">Competitive Prices</h6>
                                <p class="text-muted small mb-0">Get the best value for your money with our competitive pricing.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-dark-card p-3 h-100">
                                <i class="fas fa-headset text-info mb-2"></i>
                                <h6 class="text-light">Customer Service</h6>
                                <p class="text-muted small mb-0">Our dedicated team is always ready to assist you.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-dark-card p-3 h-100">
                                <i class="fas fa-shield-alt text-primary mb-2"></i>
                                <h6 class="text-light">Secure Shopping</h6>
                                <p class="text-muted small mb-0">Shop with confidence with our secure payment options.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stats Section -->
            <div class="row g-4 mt-3">
                <div class="col-md-3 col-6">
                    <div class="card bg-dark-card text-center p-3">
                        <h2 class="text-primary mb-0">50+</h2>
                        <p class="text-muted small mb-0">Products</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-dark-card text-center p-3">
                        <h2 class="text-success mb-0">100+</h2>
                        <p class="text-muted small mb-0">Happy Customers</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-dark-card text-center p-3">
                        <h2 class="text-warning mb-0">8+</h2>
                        <p class="text-muted small mb-0">Categories</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-dark-card text-center p-3">
                        <h2 class="text-info mb-0">24/7</h2>
                        <p class="text-muted small mb-0">Support</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-dark-card {
    background: #1a1a3a !important;
    border: 1px solid #2a2a5a !important;
    border-radius: 12px;
}

.bg-dark-card:hover {
    border-color: #6c5ce7 !important;
    transition: all 0.3s ease;
}

.text-light {
    color: #e0e0e0 !important;
}

.text-muted {
    color: #b0b0d0 !important;
}

.card {
    background: #14142e !important;
    border: 1px solid #2a2a5a !important;
    border-radius: 12px;
}

.card-body {
    padding: 30px !important;
}

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

.text-primary {
    color: #a29bfe !important;
}

.text-success {
    color: #00d47e !important;
}

.text-warning {
    color: #ffb800 !important;
}

.text-info {
    color: #00d2ff !important;
}

/* Responsive */
@media (max-width: 576px) {
    .card-body {
        padding: 20px !important;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
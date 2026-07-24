<!-- ===== BEAUTIFUL FOOTER ===== -->
<footer class="footer">
    <div class="footer-wave">
        <svg viewBox="0 0 1200 100" preserveAspectRatio="none">
            <path d="M0,50 C300,100 600,0 900,50 L1200,50 L1200,100 L0,100 Z" fill="#1a1a3a" opacity="0.6"/>
            <path d="M0,70 C300,20 600,80 900,30 L1200,70 L1200,100 L0,100 Z" fill="#1a1a3a" opacity="0.8"/>
        </svg>
    </div>
    
    <div class="container">
        <div class="row g-4">
            <!-- About -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <h5 class="gaming-logo"><i class="fas fa-gamepad me-2"></i>GamingStore</h5>
                    <p class="text-muted">Your ultimate destination for premium gaming accessories. Quality products at competitive prices.</p>
                    <div class="social-icons">
                        <a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="social-link" aria-label="Discord"><i class="fab fa-discord"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-title">Quick Links</h6>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                    <li><a href="products.php"><i class="fas fa-chevron-right"></i> Products</a></li>
                    <li><a href="about.php"><i class="fas fa-chevron-right"></i> About Us</a></li>
                    <li><a href="contact.php"><i class="fas fa-chevron-right"></i> Contact</a></li>
                    <li><a href="faq.php"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                </ul>
            </div>
            
            <!-- Categories -->
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-title">Categories</h6>
                <ul class="footer-links">
                    <?php
                    $categories = getAllCategories();
                    $count = 0;
                    foreach ($categories as $cat) {
                        if ($count >= 5) break;
                        echo '<li><a href="category.php?id=' . $cat['category_id'] . '">';
                        echo '<i class="fas fa-chevron-right"></i> ' . htmlspecialchars($cat['category_name']);
                        echo '</a></li>';
                        $count++;
                    }
                    ?>
                </ul>
            </div>
            
            <!-- Contact -->
            <div class="col-lg-4 col-md-6">
                <h6 class="footer-title">Get In Touch</h6>
                <div class="footer-contact">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Gaming City, Pakistan</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>+92 300 1234567</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>info@gamingstore.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <span>Mon-Sat: 9:00 AM - 9:00 PM</span>
                    </div>
                </div>
                <div class="footer-newsletter mt-3">
                    <p class="text-muted small">Subscribe for updates</p>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your email" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <hr class="footer-divider">
        
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start text-center">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> GamingStore. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end text-center">
                    <div class="footer-payments">
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fab fa-cc-paypal"></i>
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- ===== FOOTER CSS ===== -->
<style>
.footer {
    background: #0f0f1a;
    padding-top: 60px;
    position: relative;
    margin-top: 60px;
    border-top: 3px solid #6c5ce7;
}

.footer-wave {
    position: absolute;
    top: -100px;
    left: 0;
    width: 100%;
    height: 100px;
    overflow: hidden;
}

.footer-wave svg {
    width: 100%;
    height: 100%;
}

.footer-brand .gaming-logo {
    font-size: 1.5rem;
    color: #ffffff;
}

.footer-brand .text-muted {
    color: #8888aa !important;
    line-height: 1.8;
    margin: 15px 0;
}

.social-icons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.social-link {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(108, 92, 231, 0.1);
    color: #b8b8d4;
    transition: all 0.3s ease;
    text-decoration: none;
}

.social-link:hover {
    background: #6c5ce7;
    color: #ffffff;
    transform: translateY(-3px);
}

.footer-title {
    color: #ffffff;
    font-weight: 600;
    margin-bottom: 20px;
    font-size: 1rem;
    letter-spacing: 1px;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 10px;
}

.footer-links a {
    color: #8888aa;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.footer-links a i {
    font-size: 0.5rem;
    color: #6c5ce7;
    transition: all 0.3s ease;
}

.footer-links a:hover {
    color: #ffffff;
    transform: translateX(5px);
}

.footer-links a:hover i {
    color: #a29bfe;
}

.footer-contact .contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    color: #8888aa;
}

.footer-contact .contact-item i {
    width: 18px;
    color: #6c5ce7;
}

.newsletter-form .input-group {
    background: #1a1a3a;
    border-radius: 50px;
    overflow: hidden;
    border: 1px solid #2a2a5a;
}

.newsletter-form .form-control {
    background: transparent;
    border: none;
    color: #ffffff;
    padding: 10px 18px;
}

.newsletter-form .form-control::placeholder {
    color: #8888aa;
}

.newsletter-form .form-control:focus {
    box-shadow: none;
    background: transparent;
}

.newsletter-form .btn {
    border-radius: 0 50px 50px 0;
    padding: 10px 20px;
}

.footer-divider {
    border-color: #2a2a5a;
    margin: 30px 0 25px;
    opacity: 1;
}

.footer-bottom {
    padding-bottom: 20px;
}

.footer-payments {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
}

.footer-payments i {
    font-size: 2rem;
    color: #8888aa;
    transition: all 0.3s ease;
}

.footer-payments i:hover {
    color: #ffffff;
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .footer-wave {
        display: none;
    }
    
    .footer {
        padding-top: 30px;
    }
    
    .footer-payments {
        justify-content: center;
        margin-top: 10px;
    }
}
</style>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- AOS Animation -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        easing: 'ease-out',
        once: true,
        offset: 100
    });
</script>

<!-- Custom JS -->
<script src="assets/js/script.js"></script>
</body>
</html>
<?php
$page_title = 'Home';
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Get active banners
$banners = [];
try {
    $banners = getActiveBanners();
} catch (Exception $e) {
    $banners = [];
}
$defaultBanner = !empty($banners) ? $banners[0] : null;
?>

<main>
    <!-- ===== HERO SECTION - ANIMATION FIXED ===== -->
<section class="hero-section">
    <div class="hero-backdrop"></div>
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-lg-7 hero-content" 
                 data-aos="fade-right" 
                 data-aos-duration="800" 
                 data-aos-delay="100"
                 data-aos-easing="ease-out">
                
                <span class="hero-badge pulse" 
                      data-aos="fade-down" 
                      data-aos-duration="600" 
                      data-aos-delay="0">
                    🔥 Limited Offer
                </span>
                
                <h1 class="hero-title" 
                    data-aos="fade-up" 
                    data-aos-duration="800" 
                    data-aos-delay="200">
                    <?php echo htmlspecialchars($defaultBanner['banner_title'] ?? 'Level Up Your'); ?>
                    <span class="gradient-text">Gaming</span>
                </h1>
                
                <p class="hero-subtitle" 
                   data-aos="fade-up" 
                   data-aos-duration="800" 
                   data-aos-delay="300">
                    <?php echo htmlspecialchars($defaultBanner['banner_subtitle'] ?? 'Premium gaming accessories for the ultimate gaming experience'); ?>
                </p>
                
                <div class="hero-buttons" 
                     data-aos="fade-up" 
                     data-aos-duration="800" 
                     data-aos-delay="400">
                    <a href="products.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-rocket me-2"></i>Explore Now
                    </a>
                    <a href="about.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-play-circle me-2"></i>Watch Story
                    </a>
                </div>
                
                <div class="hero-stats" 
                     data-aos="fade-up" 
                     data-aos-duration="800" 
                     data-aos-delay="500">
                    <div class="stat-item">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">Products</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">100+</span>
                        <span class="stat-label">Happy Gamers</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Support</span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5 hero-visual" 
                 data-aos="fade-left" 
                 data-aos-duration="800" 
                 data-aos-delay="200">
                <div class="hero-floating-card">
                    <div class="floating-item item-1">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <div class="floating-item item-2">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="floating-item item-3">
                        <i class="fas fa-keyboard"></i>
                    </div>
                    <div class="floating-item item-4">
                        <i class="fas fa-mouse"></i>
                    </div>
                    <div class="hero-card-glass">
                        <div class="glass-content">
                            <i class="fas fa-trophy"></i>
                            <span>#1 Gaming Store</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-scroll-indicator" data-aos="fade-up" data-aos-delay="600">
        <span>Scroll</span>
        <i class="fas fa-chevron-down"></i>
    </div>
</section>

    <div class="container py-5">
        <?php displayMessage(); ?>
        <!-- ===== CATEGORIES SECTION ===== -->
<section class="mb-5">
    <h2 class="section-title text-center mb-4" data-aos="fade-up">Categories</h2>
    
    <div class="row g-4">
        <?php
        $categories = getAllCategories();
        $index = 0;
        foreach ($categories as $category) {
            ?>
            <div class="col-4 col-md-3 col-lg-2" 
                 data-aos="fade-up" 
                 data-aos-duration="600"
                 data-aos-delay="<?php echo $index * 50; ?>">
                <a href="category.php?id=<?php echo $category['category_id']; ?>" class="category-link">
                    <img src="<?php echo getCategoryImage($category['category_image'] ?? ''); ?>" 
                         alt="<?php echo htmlspecialchars($category['category_name']); ?>"
                         class="category-img">
                    <span class="category-name"><?php echo htmlspecialchars($category['category_name']); ?></span>
                </a>
            </div>
            <?php
            $index++;
        }
        ?>
    </div>
</section>
        

        <!-- ===== FEATURED PRODUCTS - PREMIUM CARDS ===== -->
        <section>
            <div class="section-header" data-aos="fade-up">
                <span class="section-subtitle">Products</span>
                <h2 class="section-title-modern">Featured <span class="gradient-text">Gear</span></h2>
                <p class="section-desc">Handpicked products for the ultimate gaming experience</p>
            </div>
            
            <div class="row g-4">
                <?php
                $sql = "SELECT p.*, c.category_name 
                        FROM products p 
                        LEFT JOIN categories c ON p.category_id = c.category_id 
                        WHERE p.quantity > 0 
                        ORDER BY p.product_id DESC 
                        LIMIT 8";
                $products = executeQuery($sql);
                $index = 0;
                
                foreach ($products as $product) {
                    ?>
                    <div class="col-md-3 col-sm-6" 
                         data-aos="zoom-in" 
                         data-aos-duration="600"
                         data-aos-delay="<?php echo $index * 60; ?>">
                        <div class="product-card-premium">
                            <div class="product-badge">Hot</div>
                            <div class="product-image-wrapper">
                                <img src="<?php echo getProductImage($product['product_image']); ?>" 
                                     class="product-image-premium" 
                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            </div>
                            <div class="product-body">
                                <span class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></span>
                                <h6 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h6>
                                <div class="product-price">
                                    <span class="price-current"><?php echo formatCurrency($product['price']); ?></span>
                                    <?php if ($product['quantity'] > 0): ?>
                                        <span class="stock-badge in-stock">In Stock</span>
                                    <?php else: ?>
                                        <span class="stock-badge out-of-stock">Out of Stock</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-actions">
                                    <a href="product.php?id=<?php echo $product['product_id']; ?>" 
                                       class="btn btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <?php if ($product['quantity'] > 0): ?>
                                        <button class="btn btn-add add-to-cart" 
                                                data-product="<?php echo $product['product_id']; ?>">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $index++;
                }
                ?>
            </div>
        </section>
    </div>
</main>
<!-- ===== FAQ SECTION - HOME PAGE ===== -->
<section class="faq-section py-5" data-aos="fade-up">
    <div class="container">
        <div class="section-header text-center mb-5">
            <span class="section-subtitle">FAQ</span>
            <h2 class="section-title-modern">Frequently Asked <span class="gradient-text">Questions</span></h2>
            <p class="section-desc">Find answers to common questions about our products and services</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="faq-container">
                    <?php
                    $faqs = [
                        [
                            'question' => 'What payment methods do you accept?',
                            'answer' => 'We accept Cash on Delivery (COD) for all orders. Currently, we do not support online payment gateways.'
                        ],
                        [
                            'question' => 'How long does delivery take?',
                            'answer' => 'Delivery typically takes 3-5 business days within major cities. Rural areas may take 5-7 business days.'
                        ],
                        [
                            'question' => 'Do you offer product warranty?',
                            'answer' => 'Yes, all products come with a 6-month warranty against manufacturing defects. Please keep your order receipt.'
                        ],
                        [
                            'question' => 'Can I cancel my order?',
                            'answer' => 'Orders can be cancelled within 24 hours of placing the order. Please contact our support team for assistance.'
                        ],
                        [
                            'question' => 'Do you ship internationally?',
                            'answer' => 'Currently, we only ship within Pakistan. We plan to expand internationally in the future.'
                        ],
                        [
                            'question' => 'How do I track my order?',
                            'answer' => 'You can track your order by logging into your account and visiting the "My Orders" section.'
                        ]
                    ];
                    
                    foreach ($faqs as $index => $faq):
                    ?>
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                        <div class="faq-question" data-index="<?php echo $index; ?>">
                            <span><?php echo htmlspecialchars($faq['question']); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
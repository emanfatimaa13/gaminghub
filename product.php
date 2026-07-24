<?php
$page_title = 'Product Details';
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Get product ID
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    setMessage('error', 'Invalid product');
    header('Location: products.php');
    exit();
}

// Get product details
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.product_id = ?";
$product = getSingleRecord($sql, [$productId], 'i');

if (!$product) {
    setMessage('error', 'Product not found');
    header('Location: products.php');
    exit();
}

$inWishlist = isLoggedIn() ? isInWishlist($_SESSION['user_id'], $productId) : false;
?>

<div class="container py-4">
    <?php displayMessage(); ?>
    
    <div class="row">
        <!-- Product Image -->
        <div class="col-md-6 mb-4">
            <img src="<?php echo getProductImage($product['product_image']); ?>" 
                 class="img-fluid rounded shadow" 
                 alt="<?php echo htmlspecialchars($product['product_name']); ?>">
        </div>
        
        <!-- Product Details -->
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
            <p class="text-muted">Category: <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></p>
            
            <h3 class="text-primary"><?php echo formatCurrency($product['price']); ?></h3>
            
            <div class="mb-3">
                <?php echo getStockStatus($product['quantity']); ?>
                <span class="ms-2 text-muted">(<?php echo $product['quantity']; ?> available)</span>
            </div>
            
            <div class="mb-3">
                <h6>Description:</h6>
                <p><?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available')); ?></p>
            </div>
            
            <?php if ($product['quantity'] > 0): ?>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary add-to-cart" data-product="<?php echo $product['product_id']; ?>">
                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                    </button>
                    <button class="btn btn-outline-danger toggle-wishlist" data-product="<?php echo $product['product_id']; ?>">
                        <i class="fas <?php echo $inWishlist ? 'fa-heart' : 'fa-heart-o'; ?>"></i>
                    </button>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">This product is currently out of stock.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
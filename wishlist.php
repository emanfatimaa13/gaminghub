<?php
$page_title = 'Wishlist';
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

requireLogin();

$userId = $_SESSION['user_id'];

// Handle remove from wishlist
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $productId = (int)$_GET['remove'];
    $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    executeQuery($sql, [$userId, $productId], 'ii');
    setMessage('success', 'Product removed from wishlist');
    header('Location: wishlist.php');
    exit();
}

$sql = "SELECT w.*, p.product_name, p.price, p.product_image, p.quantity as stock 
        FROM wishlist w 
        JOIN products p ON w.product_id = p.product_id 
        WHERE w.user_id = ?";
$wishlistItems = executeQuery($sql, [$userId], 'i');
?>

<div class="container py-4">
    <h2><i class="fas fa-heart me-2"></i>My Wishlist</h2>
    
    <?php displayMessage(); ?>
    
    <?php if (empty($wishlistItems)): ?>
        <div class="alert alert-info">
            Your wishlist is empty. <a href="products.php">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($wishlistItems as $item): ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card product-card h-100 shadow-sm">
                        <img src="<?php echo getProductImage($item['product_image']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                            <p class="card-text fw-bold text-primary"><?php echo formatCurrency($item['price']); ?></p>
                            <div class="d-flex gap-2">
                                <a href="product.php?id=<?php echo $item['product_id']; ?>" 
                                   class="btn btn-sm btn-outline-primary flex-grow-1">View</a>
                                <a href="wishlist.php?remove=<?php echo $item['product_id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Remove from wishlist?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
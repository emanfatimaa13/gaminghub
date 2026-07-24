<?php
$page_title = 'Category Products';
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($categoryId <= 0) {
    setMessage('error', 'Invalid category');
    header('Location: products.php');
    exit();
}

// Get category details
$category = getSingleRecord("SELECT * FROM categories WHERE category_id = ?", [$categoryId], 'i');

if (!$category) {
    setMessage('error', 'Category not found');
    header('Location: products.php');
    exit();
}

// Get products in this category
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.category_id = ? 
        ORDER BY p.product_id DESC";
$products = executeQuery($sql, [$categoryId], 'i');
?>

<div class="container py-4">
    <h2><?php echo htmlspecialchars($category['category_name']); ?></h2>
    <p class="text-muted"><?php echo htmlspecialchars($category['category_description'] ?? ''); ?></p>
    
    <?php if (empty($products)): ?>
        <div class="alert alert-info">No products found in this category.</div>
    <?php else: ?>
        <div class="row g-4 mt-3">
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card product-card h-100 shadow-sm">
                        <img src="<?php echo getProductImage($product['product_image']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h6>
                            <p class="text-muted small"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></p>
                            <p class="card-text fw-bold text-primary"><?php echo formatCurrency($product['price']); ?></p>
                            <div class="d-flex justify-content-between">
                                <a href="product.php?id=<?php echo $product['product_id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">View</a>
                                <?php if ($product['quantity'] > 0): ?>
                                    <button class="btn btn-sm btn-primary add-to-cart" 
                                            data-product="<?php echo $product['product_id']; ?>">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
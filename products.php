<?php
$page_title = 'Products';
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Get filter parameters
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;

// Build query
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE 1=1";
$params = [];
$types = '';

if ($category > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category;
    $types .= 'i';
}

if ($minPrice > 0) {
    $sql .= " AND p.price >= ?";
    $params[] = $minPrice;
    $types .= 'd';
}

if ($maxPrice > 0) {
    $sql .= " AND p.price <= ?";
    $params[] = $maxPrice;
    $types .= 'd';
}

$sql .= " ORDER BY p.product_id DESC";

$products = executeQuery($sql, $params, $types);
$categories = getAllCategories();
?>

<div class="container py-4">
    <?php displayMessage(); ?>
    
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-filter me-2"></i>Filters</h5>
                    
                    <form method="GET" action="">
                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="0">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>" 
                                            <?php echo ($category == $cat['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" 
                                           placeholder="Min" value="<?php echo $minPrice > 0 ? $minPrice : ''; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" 
                                           placeholder="Max" value="<?php echo $maxPrice > 0 ? $maxPrice : ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                        
                        <a href="products.php" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-undo me-2"></i>Reset Filters
                        </a>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><?php echo count($products); ?> Products Found</h4>
            </div>
            
            <?php if (empty($products)): ?>
                <div class="alert alert-info">No products found matching your criteria.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 col-sm-6">
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
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
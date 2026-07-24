<?php
$page_title = 'Search Results';
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

$searchTerm = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build query
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE 1=1";
$params = [];
$types = '';

if (!empty($searchTerm)) {
    $sql .= " AND (p.product_name LIKE ? OR p.description LIKE ? OR c.category_name LIKE ?)";
    $searchPattern = '%' . $searchTerm . '%';
    $params[] = $searchPattern;
    $params[] = $searchPattern;
    $params[] = $searchPattern;
    $types .= 'sss';
}

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

// Sorting
switch($sort) {
    case 'price_low':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'name':
        $sql .= " ORDER BY p.product_name ASC";
        break;
    default:
        $sql .= " ORDER BY p.product_id DESC";
}

$products = executeQuery($sql, $params, $types);
$categories = getAllCategories();
?>

<div class="container py-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-filter me-2"></i>Filters</h5>
                    
                    <form method="GET" action="" id="filterForm">
                        <!-- Search Input -->
                        <div class="mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" name="q" 
                                   value="<?php echo htmlspecialchars($searchTerm); ?>" 
                                   placeholder="Search products...">
                        </div>
                        
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
                        
                        <!-- Sort -->
                        <div class="mb-3">
                            <label class="form-label">Sort By</label>
                            <select name="sort" class="form-select">
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest</option>
                                <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                        
                        <a href="search.php" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-undo me-2"></i>Reset
                        </a>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Results -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><?php echo count($products); ?> Results Found</h4>
                <?php if (!empty($searchTerm)): ?>
                    <span class="text-muted">Search: "<?php echo htmlspecialchars($searchTerm); ?>"</span>
                <?php endif; ?>
            </div>
            
            <?php if (empty($products)): ?>
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h5>No products found</h5>
                    <p class="text-muted">Try adjusting your filters or search term</p>
                    <a href="products.php" class="btn btn-primary">Browse All Products</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="card product-card h-100">
                                <img src="<?php echo getProductImage($product['product_image']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h6>
                                    <p class="text-muted small"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></p>
                                    <p class="card-text fw-bold text-primary"><?php echo formatCurrency($product['price']); ?></p>
                                    <a href="product.php?id=<?php echo $product['product_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary w-100">View Details</a>
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
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Get image to delete
    $product = getSingleRecord("SELECT product_image FROM products WHERE product_id = ?", [$id], 'i');
    if ($product && $product['product_image']) {
        $imagePath = '../uploads/products/' . $product['product_image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    $sql = "DELETE FROM products WHERE product_id = ?";
    executeQuery($sql, [$id], 'i');
    setMessage('success', 'Product deleted successfully');
    header('Location: products.php');
    exit();
}

// Handle add/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $productName = sanitize($_POST['product_name']);
    $categoryId = (int)$_POST['category_id'];
    $description = sanitize($_POST['description']);
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    
    // Handle image upload
    $imageName = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['product_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $imageName = time() . '_' . uniqid() . '.' . $ext;
            $uploadPath = '../uploads/products/' . $imageName;
            
            // Create directory if not exists
            if (!is_dir('../uploads/products/')) {
                mkdir('../uploads/products/', 0777, true);
            }
            
            move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadPath);
        }
    }
    
    if ($action === 'add') {
        $sql = "INSERT INTO products (product_name, category_id, description, price, quantity, product_image) 
                VALUES (?, ?, ?, ?, ?, ?)";
        executeQuery($sql, [$productName, $categoryId, $description, $price, $quantity, $imageName], 'sisdis');
        setMessage('success', 'Product added successfully');
    } elseif ($action === 'edit' && isset($_POST['product_id'])) {
        $productId = (int)$_POST['product_id'];
        if ($imageName) {
            // Delete old image
            $old = getSingleRecord("SELECT product_image FROM products WHERE product_id = ?", [$productId], 'i');
            if ($old && $old['product_image']) {
                $oldPath = '../uploads/products/' . $old['product_image'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $sql = "UPDATE products SET product_name=?, category_id=?, description=?, price=?, quantity=?, product_image=? 
                    WHERE product_id=?";
            executeQuery($sql, [$productName, $categoryId, $description, $price, $quantity, $imageName, $productId], 'sisdisi');
        } else {
            $sql = "UPDATE products SET product_name=?, category_id=?, description=?, price=?, quantity=? 
                    WHERE product_id=?";
            executeQuery($sql, [$productName, $categoryId, $description, $price, $quantity, $productId], 'sisdii');
        }
        setMessage('success', 'Product updated successfully');
    }
    header('Location: products.php');
    exit();
}

$products = executeQuery("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.product_id DESC");
$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-sidebar { background: #1a1a2e; min-height: 100vh; border-right: 1px solid #3a3a5a; padding: 0; }
        .admin-sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; border-radius: 0; transition: all 0.3s; }
        .admin-sidebar .nav-link:hover { color: #fff; background: rgba(108, 92, 231, 0.15); }
        .admin-sidebar .nav-link.active { color: #fff; background: #6c5ce7; }
        .admin-sidebar .nav-link i { width: 20px; margin-right: 10px; }
        .admin-content { background: #0f0f1a; min-height: 100vh; padding: 20px; }
        .card-dark { background: #1e1e3e; border: 1px solid #2a2a5a; border-radius: 12px; }
        .table-dark { color: #e0e0e0; }
        .table-dark th { color: #b8b8d4; font-weight: 600; border-bottom: 2px solid #2a2a5a; }
        .table-dark td { vertical-align: middle; border-color: #2a2a5a; }
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #2a2a5a; background: #0f0f1a; }
        .product-img-placeholder { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: #0f0f1a; border-radius: 8px; border: 1px solid #2a2a5a; color: #3a3a5a; font-size: 1.5rem; }
        .modal-content { background: #1a1a2e; border: 1px solid #2a2a5a; border-radius: 12px; color: #e0e0e0; }
        .modal-header { border-bottom: 1px solid #2a2a5a; padding: 15px 20px; }
        .modal-body { padding: 20px; }
        .modal-footer { border-top: 1px solid #2a2a5a; padding: 15px 20px; }
        .form-label { color: #e0e0e0; font-weight: 500; }
        .form-control, .form-select { background: #0f0f1a; border: 1px solid #2a2a5a; color: #e0e0e0; border-radius: 8px; }
        .form-control:focus, .form-select:focus { background: #0f0f1a; border-color: #6c5ce7; box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25); color: #e0e0e0; }
        .form-control::placeholder { color: #8888aa; }
        .section-title { color: #ffffff; font-family: 'Orbitron', sans-serif; font-weight: 700; }
        .btn-close { filter: invert(1); }
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 4rem; color: #3a3a5a; margin-bottom: 20px; }
        .gaming-logo { font-family: 'Orbitron', sans-serif; font-weight: 900; letter-spacing: 2px; background: linear-gradient(135deg, #6c5ce7 0%, #00d2ff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        #imagePreviewContainer img { max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #2a2a5a; background: #0f0f1a; }
        .badge-status { padding: 4px 12px; border-radius: 20px; font-weight: 500; font-size: 0.75rem; }
        .badge-status.in-stock { background: rgba(0, 212, 126, 0.2); color: #00d47e; }
        .badge-status.low-stock { background: rgba(255, 184, 0, 0.2); color: #ffb800; }
        .badge-status.out-of-stock { background: rgba(255, 71, 87, 0.2); color: #ff4757; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 admin-sidebar">
                <div class="p-3 border-bottom border-secondary">
                    <h5 class="gaming-logo" style="font-size: 1.2rem;">
                        <i class="fas fa-gamepad me-2" style="-webkit-text-fill-color: #6c5ce7;"></i>Admin
                    </h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-dashboard"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                    <li class="nav-item"><a class="nav-link active" href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="banners.php"><i class="fas fa-image"></i> Banners</a></li>
                    <li class="nav-item"><a class="nav-link" href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li class="nav-item mt-3"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 admin-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title">Manage Products</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </button>
                </div>
                
                <?php displayMessage(); ?>
                
                <div class="card-dark">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-white">Total Products: <?php echo count($products); ?></span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($products)): ?>
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <h5>No Products Found</h5>
                                <p>Click the "Add Product" button to create your first product.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">ID</th>
                                            <th style="width: 80px;">Image</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Status</th>
                                            <th style="width: 120px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $p): ?>
                                        <tr>
                                            <td>#<?php echo $p['product_id']; ?></td>
                                            <td>
                                                <?php 
                                                $imagePath = '../uploads/products/' . ($p['product_image'] ?? '');
                                                if (!empty($p['product_image']) && file_exists($imagePath)): 
                                                ?>
                                                    <img src="<?php echo $imagePath; ?>" 
                                                         class="product-img" 
                                                         alt="<?php echo htmlspecialchars($p['product_name']); ?>">
                                                <?php else: ?>
                                                    <div class="product-img-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($p['product_name']); ?></strong>
                                                <?php if (!empty($p['description'])): ?>
                                                    <div class="text-muted small"><?php echo htmlspecialchars(substr($p['description'], 0, 50)); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?></td>
                                            <td class="text-primary fw-bold"><?php echo formatCurrency($p['price']); ?></td>
                                            <td><?php echo $p['quantity']; ?></td>
                                            <td>
                                                <?php
                                                if ($p['quantity'] <= 0) {
                                                    echo '<span class="badge-status out-of-stock">Out of Stock</span>';
                                                } elseif ($p['quantity'] < 5) {
                                                    echo '<span class="badge-status low-stock">Low Stock</span>';
                                                } else {
                                                    echo '<span class="badge-status in-stock">In Stock</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-info btn-sm edit-product" 
                                                        data-id="<?php echo $p['product_id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($p['product_name']); ?>"
                                                        data-category="<?php echo $p['category_id']; ?>"
                                                        data-description="<?php echo htmlspecialchars($p['description'] ?? ''); ?>"
                                                        data-price="<?php echo $p['price']; ?>"
                                                        data-quantity="<?php echo $p['quantity']; ?>"
                                                        data-image="<?php echo $p['product_image']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="?delete=<?php echo $p['product_id']; ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Are you sure you want to delete this product?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="productForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="product_id" id="productId">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="product_name" id="productName" placeholder="Enter product name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" name="category_id" id="categoryId" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['category_id']; ?>">
                                                <?php echo htmlspecialchars($cat['category_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="3" placeholder="Enter product description"></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Price (PKR) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="price" id="price" placeholder="0.00" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="quantity" id="quantity" placeholder="0" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Product Image</label>
                                    <input type="file" class="form-control" name="product_image" id="productImage" accept="image/*">
                                    <small class="text-muted">Leave empty to keep current image</small>
                                    <div id="imagePreviewContainer" class="mt-2" style="display: none;">
                                        <img id="imagePreview" src="" alt="Preview">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit product - fill modal with data
        document.querySelectorAll('.edit-product').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('modalTitle').textContent = 'Edit Product';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('productId').value = this.dataset.id;
                document.getElementById('productName').value = this.dataset.name;
                document.getElementById('categoryId').value = this.dataset.category;
                document.getElementById('description').value = this.dataset.description;
                document.getElementById('price').value = this.dataset.price;
                document.getElementById('quantity').value = this.dataset.quantity;
                document.getElementById('productImage').required = false;
                
                // Show current image
                const preview = document.getElementById('imagePreview');
                const container = document.getElementById('imagePreviewContainer');
                if (this.dataset.image) {
                    preview.src = '../uploads/products/' + this.dataset.image;
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
                
                new bootstrap.Modal(document.getElementById('productModal')).show();
            });
        });
        
        // Reset modal on close
        document.getElementById('productModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('formAction').value = 'add';
            document.getElementById('productId').value = '';
            document.getElementById('productName').value = '';
            document.getElementById('categoryId').value = '';
            document.getElementById('description').value = '';
            document.getElementById('price').value = '';
            document.getElementById('quantity').value = '';
            document.getElementById('productImage').value = '';
            document.getElementById('productImage').required = true;
            document.getElementById('imagePreviewContainer').style.display = 'none';
        });
        
        // Image preview
        document.getElementById('productImage').addEventListener('change', function(e) {
            const file = this.files[0];
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('imagePreviewContainer');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    container.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                container.style.display = 'none';
            }
        });
    </script>
</body>
</html>
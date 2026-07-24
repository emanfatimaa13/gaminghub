<?php
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

// Handle stock update
if (isset($_POST['update_stock']) && isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $sql = "UPDATE products SET quantity = ? WHERE product_id = ?";
    executeQuery($sql, [$quantity, $productId], 'ii');
    setMessage('success', 'Stock updated successfully');
    header('Location: inventory.php');
    exit();
}

$products = executeQuery("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.product_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-sidebar { background: #1a1a2e; min-height: 100vh; border-right: 1px solid #3a3a5a; }
        .admin-sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 0.75rem 1.5rem; border-radius: 0; }
        .admin-sidebar .nav-link:hover { color: #fff; background: rgba(108, 92, 231, 0.1); }
        .admin-sidebar .nav-link.active { color: #fff; background: #6c5ce7; }
        .admin-content { background: #0f0f1a; min-height: 100vh; }
        .card-dark { background: #2d2d44; border: 1px solid #3a3a5a; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 admin-sidebar p-0">
                <div class="p-3"><h5 class="text-white gaming-logo"><i class="fas fa-gamepad me-2"></i>Admin</h5></div>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-dashboard me-2"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="categories.php"><i class="fas fa-tags me-2"></i>Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php"><i class="fas fa-box me-2"></i>Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="orders.php"><i class="fas fa-shopping-cart me-2"></i>Orders</a></li>
                    <li class="nav-item"><a class="nav-link active" href="inventory.php"><i class="fas fa-warehouse me-2"></i>Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php"><i class="fas fa-users me-2"></i>Users</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 admin-content p-4">
                <h2 class="text-white mb-4">Inventory Management</h2>
                <?php displayMessage(); ?>
                
                <div class="card-dark rounded p-3">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Current Stock</th>
                                    <th>Status</th>
                                    <th>Update Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?></td>
                                    <td><?php echo formatCurrency($p['price']); ?></td>
                                    <td><?php echo $p['quantity']; ?></td>
                                    <td><?php echo getStockStatus($p['quantity']); ?></td>
                                    <td>
                                        <form method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="product_id" value="<?php echo $p['product_id']; ?>">
                                            <input type="number" name="quantity" class="form-control form-control-sm" 
                                                   style="width: 80px;" value="<?php echo $p['quantity']; ?>" min="0">
                                            <button type="submit" name="update_stock" class="btn btn-sm btn-primary">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
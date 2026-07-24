<?php
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

$page_title = 'Dashboard';

// Get stats
$totalUsers = getCount("SELECT COUNT(*) as count FROM users");
$totalProducts = getCount("SELECT COUNT(*) as count FROM products");
$totalOrders = getCount("SELECT COUNT(*) as count FROM orders");
$totalCategories = getCount("SELECT COUNT(*) as count FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gaming Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-sidebar {
            background: #1a1a2e;
            min-height: 100vh;
            border-right: 1px solid #3a3a5a;
        }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 0.75rem 1.5rem;
            border-radius: 0;
        }
        .admin-sidebar .nav-link:hover {
            color: #fff;
            background: rgba(108, 92, 231, 0.1);
        }
        .admin-sidebar .nav-link.active {
            color: #fff;
            background: #6c5ce7;
        }
        .stat-card {
            background: #2d2d44;
            border: 1px solid #3a3a5a;
            border-radius: 10px;
            padding: 1.5rem;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 admin-sidebar p-0">
                <div class="p-3">
                    <h5 class="text-white gaming-logo"><i class="fas fa-gamepad me-2"></i>Admin</h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-dashboard me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-tags me-2"></i>Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-box me-2"></i>Products
                        </a>
                    </li>
                    <li class="nav-item">
    <a class="nav-link" href="banners.php">
        <i class="fas fa-image"></i> Banners
    </a>
</li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">
                            <i class="fas fa-shopping-cart me-2"></i>Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inventory.php">
                            <i class="fas fa-warehouse me-2"></i>Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2>Dashboard</h2>
                <?php displayMessage(); ?>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Users</h6>
                                    <h2 class="text-white"><?php echo $totalUsers; ?></h2>
                                </div>
                                <div class="stat-icon bg-primary bg-opacity-25">
                                    <i class="fas fa-users text-primary fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Products</h6>
                                    <h2 class="text-white"><?php echo $totalProducts; ?></h2>
                                </div>
                                <div class="stat-icon bg-success bg-opacity-25">
                                    <i class="fas fa-box text-success fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Orders</h6>
                                    <h2 class="text-white"><?php echo $totalOrders; ?></h2>
                                </div>
                                <div class="stat-icon bg-warning bg-opacity-25">
                                    <i class="fas fa-shopping-cart text-warning fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Categories</h6>
                                    <h2 class="text-white"><?php echo $totalCategories; ?></h2>
                                </div>
                                <div class="stat-icon bg-info bg-opacity-25">
                                    <i class="fas fa-tags text-info fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="stat-card">
                    <h5 class="text-white">Recent Orders</h5>
                    <?php
                    $sql = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5";
                    $orders = executeQuery($sql);
                    ?>
                    <?php if (empty($orders)): ?>
                        <p class="text-muted">No orders yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                            <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                            <td><?php echo formatCurrency($order['total_amount']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo match($order['status']) {
                                                    'Pending' => 'warning',
                                                    'Processing' => 'info',
                                                    'Delivered' => 'success',
                                                    'Cancelled' => 'danger',
                                                    default => 'secondary'
                                                }; ?>">
                                                    <?php echo $order['status']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
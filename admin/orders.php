<?php
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];
    $status = sanitize($_POST['status']);
    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    executeQuery($sql, [$status, $orderId], 'si');
    setMessage('success', 'Order status updated successfully');
    header('Location: orders.php');
    exit();
}

$orders = executeQuery("SELECT * FROM orders ORDER BY order_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
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
                    <li class="nav-item"><a class="nav-link active" href="orders.php"><i class="fas fa-shopping-cart me-2"></i>Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="inventory.php"><i class="fas fa-warehouse me-2"></i>Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php"><i class="fas fa-users me-2"></i>Users</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 admin-content p-4">
                <h2 class="text-white mb-4">Manage Orders</h2>
                <?php displayMessage(); ?>
                
                <div class="card-dark rounded p-3">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['email']); ?></td>
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
                                    <td>
                                        <button class="btn btn-sm btn-info view-order" 
                                                data-order="<?php echo $order['order_id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning update-status" 
                                                data-order="<?php echo $order['order_id']; ?>"
                                                data-status="<?php echo $order['status']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
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
    
    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="statusOrderId">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="statusSelect">
                                <option value="Pending">Pending</option>
                                <option value="Processing">Processing</option>
                                <option value="Delivered">Delivered</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.update-status').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('statusOrderId').value = this.dataset.order;
                document.getElementById('statusSelect').value = this.dataset.status;
                new bootstrap.Modal(document.getElementById('statusModal')).show();
            });
        });
    </script>
</body>
</html>
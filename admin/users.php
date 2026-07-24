<?php
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM users WHERE user_id = ?";
    executeQuery($sql, [$id], 'i');
    setMessage('success', 'User deleted successfully');
    header('Location: users.php');
    exit();
}

$users = executeQuery("SELECT * FROM users ORDER BY user_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
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
                    <li class="nav-item"><a class="nav-link" href="inventory.php"><i class="fas fa-warehouse me-2"></i>Inventory</a></li>
                    <li class="nav-item"><a class="nav-link active" href="users.php"><i class="fas fa-users me-2"></i>Users</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 admin-content p-4">
                <h2 class="text-white mb-4">Manage Users</h2>
                <?php displayMessage(); ?>
                
                <div class="card-dark rounded p-3">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Registered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $user['user_id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Delete this user?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
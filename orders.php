<?php
$page_title = 'My Orders';
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

requireLogin();

$userId = $_SESSION['user_id'];

$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$orders = executeQuery($sql, [$userId], 'i');
?>

<div class="container py-4">
    <h2><i class="fas fa-list me-2"></i>My Orders</h2>
    
    <?php displayMessage(); ?>
    
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            You haven't placed any orders yet. <a href="products.php">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                            <td><?php echo formatCurrency($order['total_amount']); ?></td>
                            <td>
                                <?php
                                $badgeClass = match($order['status']) {
                                    'Pending' => 'warning',
                                    'Processing' => 'info',
                                    'Delivered' => 'success',
                                    'Cancelled' => 'danger',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?php echo $badgeClass; ?>"><?php echo $order['status']; ?></span>
                            </td>
                            <td><?php echo $order['payment_method']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
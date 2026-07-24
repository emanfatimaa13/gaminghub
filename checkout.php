<?php
$page_title = 'Checkout';
require_once 'config/database.php';
require_once 'config/functions.php';

requireLogin();

$userId = $_SESSION['user_id'];
$user = getCurrentUser();

// Get cart items
$sql = "SELECT c.*, p.product_name, p.price, p.quantity as stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?";
$cartItems = executeQuery($sql, [$userId], 'i');

if (empty($cartItems)) {
    setMessage('error', 'Your cart is empty');
    header('Location: products.php');
    exit();
}

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    
    // Validation
    if (empty($fullName) || empty($email) || empty($phone) || empty($address)) {
        setMessage('error', 'Please fill in all required fields');
    } else {
        // Create order
        $orderNumber = generateOrderNumber();
        $status = 'Pending';
        $paymentMethod = 'Cash on Delivery';
        
        $sql = "INSERT INTO orders (user_id, order_number, full_name, email, phone, address, total_amount, status, payment_method) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $result = executeQuery($sql, [$userId, $orderNumber, $fullName, $email, $phone, $address, $total, $status, $paymentMethod], 
                                    'isssssdss');
            
            if ($result && $result['affected_rows'] > 0) {
                $orderId = $result['insert_id'];
                
                // Add order items
                foreach ($cartItems as $item) {
                    $subtotal = $item['price'] * $item['quantity'];
                    $sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price, subtotal) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    executeQuery($sql, [$orderId, $item['product_id'], $item['product_name'], $item['quantity'], $item['price'], $subtotal], 
                                  'iisidd');
                    
                    // Update product stock
                    $sql = "UPDATE products SET quantity = quantity - ? WHERE product_id = ?";
                    executeQuery($sql, [$item['quantity'], $item['product_id']], 'ii');
                }
                
                // Clear cart
                $sql = "DELETE FROM cart WHERE user_id = ?";
                executeQuery($sql, [$userId], 'i');
                
                setMessage('success', 'Order placed successfully!');
                header('Location: orders.php');
                exit();
            }
        } catch (Exception $e) {
            setMessage('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="container py-4">
    <?php displayMessage(); ?>
    
    <h2 class="text-light mb-4"><i class="fas fa-check-circle text-primary me-2"></i>Checkout</h2>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="text-light mb-3"><i class="fas fa-user text-primary me-2"></i>Customer Information</h5>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label text-light">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="full_name" 
                                   value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" 
                                   placeholder="Enter your full name" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" 
                                       placeholder="Enter your email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light">Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                       placeholder="Enter your phone number" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-light">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="address" rows="3" 
                                      placeholder="Enter your delivery address" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-2"></i>Place Order (Cash on Delivery)
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="text-light mb-3"><i class="fas fa-shopping-bag text-primary me-2"></i>Order Summary</h5>
                    <hr class="border-secondary">
                    
                    <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-light"><?php echo htmlspecialchars($item['product_name']); ?> <span class="text-muted">x<?php echo $item['quantity']; ?></span></span>
                            <span class="text-primary fw-bold"><?php echo formatCurrency($item['price'] * $item['quantity']); ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <hr class="border-secondary">
                    
                    <div class="d-flex justify-content-between fw-bold">
                        <span class="text-light">Total:</span>
                        <span class="text-primary" style="font-size: 1.25rem;"><?php echo formatCurrency($total); ?></span>
                    </div>
                    
                    <div class="mt-3 p-3 bg-dark rounded">
                        <p class="text-muted small mb-0">
                            <i class="fas fa-truck text-info me-1"></i> 
                            <span class="text-light">Cash on Delivery</span>
                        </p>
                        <p class="text-muted small mb-0 mt-1">
                            <i class="fas fa-clock text-warning me-1"></i> 
                            <span class="text-light">Estimated delivery: 3-5 business days</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Checkout Page Specific Styles */
.text-light {
    color: #e0e0e0 !important;
}

.text-muted {
    color: #b0b0d0 !important;
}

.text-primary {
    color: #a29bfe !important;
}

.text-danger {
    color: #ff6b81 !important;
}

.text-info {
    color: #00d2ff !important;
}

.text-warning {
    color: #ffb800 !important;
}

.card {
    background: #14142e !important;
    border: 1px solid #2a2a5a !important;
    border-radius: 12px;
}

.card-body {
    padding: 25px;
}

.form-label {
    color: #e0e0e0 !important;
    font-weight: 500;
}

.form-control {
    background: #1a1a3a !important;
    border: 2px solid #2a2a5a !important;
    color: #ffffff !important;
    border-radius: 8px;
    padding: 12px 16px;
    transition: all 0.3s ease;
}

.form-control:focus {
    background: #1a1a3a !important;
    border-color: #6c5ce7 !important;
    box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.15) !important;
    color: #ffffff !important;
}

.form-control::placeholder {
    color: #8888aa !important;
}

.btn-success {
    background: linear-gradient(135deg, #00d47e 0%, #00b894 100%);
    border: none;
    border-radius: 10px;
    padding: 14px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 212, 126, 0.4);
}

hr.border-secondary {
    border-color: #2a2a5a !important;
    opacity: 1;
}

.bg-dark {
    background: #0f0f1a !important;
}

.alert {
    background: var(--dark-card);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 16px 20px;
    color: var(--text-primary);
}

.alert-success {
    border-color: #00d47e;
    color: #00d47e;
}

.alert-danger {
    border-color: #ff4757;
    color: #ff6b81;
}

/* Responsive */
@media (max-width: 576px) {
    .card-body {
        padding: 18px !important;
    }
    
    h2.text-light {
        font-size: 1.5rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
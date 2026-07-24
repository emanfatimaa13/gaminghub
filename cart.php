<?php
$page_title = 'Shopping Cart';
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

requireLogin();

$userId = $_SESSION['user_id'];

// Handle cart actions
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $cartId = (int)$_GET['remove'];
    $sql = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
    executeQuery($sql, [$cartId, $userId], 'ii');
    setMessage('success', 'Product removed from cart');
    header('Location: cart.php');
    exit();
}

if (isset($_GET['update']) && is_numeric($_GET['update'])) {
    $cartId = (int)$_GET['update'];
    $quantity = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    if ($quantity > 0) {
        $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?";
        executeQuery($sql, [$quantity, $cartId, $userId], 'iii');
    }
    header('Location: cart.php');
    exit();
}

// Get cart items
$sql = "SELECT c.*, p.product_name, p.price, p.product_image, p.quantity as stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?";
$cartItems = executeQuery($sql, [$userId], 'i');

$total = 0;
?>

<div class="container py-4">
    <h2><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h2>
    
    <?php displayMessage(); ?>
    
    <?php if (empty($cartItems)): ?>
        <div class="alert alert-info">
            Your cart is empty. <a href="products.php">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo getProductImage($item['product_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                         style="width: 50px; height: 50px; object-fit: cover;" class="me-3">
                                    <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                </div>
                            </td>
                            <td><?php echo formatCurrency($item['price']); ?></td>
                            <td>
                                <form method="GET" action="cart.php" class="d-flex align-items-center">
                                    <input type="hidden" name="update" value="<?php echo $item['cart_id']; ?>">
                                    <input type="number" name="qty" value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="<?php echo $item['stock']; ?>" 
                                           class="form-control form-control-sm" style="width: 70px;">
                                    <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </form>
                            </td>
                            <td><?php echo formatCurrency($subtotal); ?></td>
                            <td>
                                <a href="cart.php?remove=<?php echo $item['cart_id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Remove from cart?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total:</td>
                        <td colspan="2" class="fw-bold text-primary"><?php echo formatCurrency($total); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="products.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
            </a>
            <a href="checkout.php" class="btn btn-primary">
                Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
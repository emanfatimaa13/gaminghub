<?php
require_once '../config/database.php';
require_once '../config/functions.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit();
}

$userId = $_SESSION['user_id'];

// Check if product exists and has stock
$product = getSingleRecord("SELECT quantity FROM products WHERE product_id = ?", [$productId], 'i');
if (!$product || $product['quantity'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Product out of stock']);
    exit();
}

// Check if already in cart
$existing = getSingleRecord("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?", [$userId, $productId], 'ii');

if ($existing) {
    $newQty = $existing['quantity'] + 1;
    if ($newQty > $product['quantity']) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        exit();
    }
    $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    executeQuery($sql, [$newQty, $existing['cart_id']], 'ii');
} else {
    $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
    executeQuery($sql, [$userId, $productId], 'ii');
}

$cartCount = getCartCount($userId);
echo json_encode(['success' => true, 'cartCount' => $cartCount, 'message' => 'Added to cart']);
?>
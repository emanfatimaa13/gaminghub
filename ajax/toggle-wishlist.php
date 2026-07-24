<?php
require_once '../config/database.php';
require_once '../config/functions.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first', 'redirect' => 'login.php']);
    exit();
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit();
}

$userId = $_SESSION['user_id'];

// Check if in wishlist
$existing = getSingleRecord("SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?", [$userId, $productId], 'ii');

if ($existing) {
    // Remove from wishlist
    $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    executeQuery($sql, [$userId, $productId], 'ii');
    echo json_encode(['success' => true, 'added' => false]);
} else {
    // Add to wishlist
    $sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
    executeQuery($sql, [$userId, $productId], 'ii');
    echo json_encode(['success' => true, 'added' => true]);
}
?>
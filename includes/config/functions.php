<?php
// Common utility functions

// Start session if not already started
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Check if user is logged in
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if admin is logged in
function isAdminLoggedIn() {
    startSession();
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Get current user ID
function getCurrentUserId() {
    startSession();
    return $_SESSION['user_id'] ?? null;
}

// Get current admin ID
function getCurrentAdminId() {
    startSession();
    return $_SESSION['admin_id'] ?? null;
}

// Set session message
function setMessage($type, $message) {
    startSession();
    $_SESSION['message'] = [
        'type' => $type,
        'text' => $message
    ];
}

// Get and clear session message
function getMessage() {
    startSession();
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return null;
}

// Display alert message
function displayMessage() {
    $message = getMessage();
    if ($message) {
        $alertClass = $message['type'] === 'success' ? 'alert-success' : 
                     ($message['type'] === 'error' ? 'alert-danger' : 'alert-info');
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($message['text']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}

// Logout user
function logoutUser() {
    startSession();
    $_SESSION = array();
    session_destroy();
}

// Check authentication for pages
function requireLogin() {
    if (!isLoggedIn()) {
        setMessage('error', 'Please login to access this page');
        header('Location: login.php');
        exit();
    }
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        setMessage('error', 'Please login as admin to access this page');
        header('Location: admin/login.php');
        exit();
    }
}

// Format currency
function formatCurrency($amount) {
    return 'PKR ' . number_format($amount, 2);
}

// Truncate text
function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

// Get category name by ID
function getCategoryName($categoryId) {
    if (!$categoryId) return 'Uncategorized';
    
    $sql = "SELECT category_name FROM categories WHERE category_id = ?";
    $result = getSingleRecord($sql, [$categoryId], 'i');
    return $result ? $result['category_name'] : 'Uncategorized';
}

// Get product image path
function getProductImage($imageName) {
    if ($imageName && file_exists('uploads/products/' . $imageName)) {
        return 'uploads/products/' . $imageName;
    }
    return 'assets/images/no-image.png';
}

// Get user cart count
function getCartCount($userId) {
    if (!$userId) return 0;
    
    $sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $result = getSingleRecord($sql, [$userId], 'i');
    return $result ? (int)$result['total'] : 0;
}

// Get wishlist count
function getWishlistCount($userId) {
    if (!$userId) return 0;
    
    $sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?";
    $result = getSingleRecord($sql, [$userId], 'i');
    return $result ? (int)$result['count'] : 0;
}

// Check if product is in wishlist
function isInWishlist($userId, $productId) {
    if (!$userId || !$productId) return false;
    
    $sql = "SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?";
    $result = getSingleRecord($sql, [$userId, $productId], 'ii');
    return $result !== null;
}

// Check if product is in cart
function isInCart($userId, $productId) {
    if (!$userId || !$productId) return false;
    
    $sql = "SELECT cart_id FROM cart WHERE user_id = ? AND product_id = ?";
    $result = getSingleRecord($sql, [$userId, $productId], 'ii');
    return $result !== null;
}

// Get product stock status
function getStockStatus($quantity) {
    if ($quantity <= 0) {
        return '<span class="badge bg-danger">Out of Stock</span>';
    } elseif ($quantity < 5) {
        return '<span class="badge bg-warning text-dark">Low Stock</span>';
    } else {
        return '<span class="badge bg-success">In Stock</span>';
    }
}

// Sanitize input
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Generate order number
function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . uniqid();
}

// Get user full name
function getUserName($userId) {
    if (!$userId) return 'Guest';
    
    $sql = "SELECT full_name FROM users WHERE user_id = ?";
    $result = getSingleRecord($sql, [$userId], 'i');
    return $result ? $result['full_name'] : 'User';
}

// Get categories for dropdown
function getCategoriesOptions($selected = null) {
    $sql = "SELECT category_id, category_name FROM categories ORDER BY category_name";
    $categories = executeQuery($sql);
    
    $html = '';
    foreach ($categories as $category) {
        $sel = ($selected == $category['category_id']) ? 'selected' : '';
        $html .= '<option value="' . $category['category_id'] . '" ' . $sel . '>';
        $html .= htmlspecialchars($category['category_name']);
        $html .= '</option>';
    }
    return $html;
}

// Get total price of cart
function getCartTotal($userId) {
    if (!$userId) return 0;
    
    $sql = "SELECT SUM(p.price * c.quantity) as total 
            FROM cart c 
            JOIN products p ON c.product_id = p.product_id 
            WHERE c.user_id = ?";
    $result = getSingleRecord($sql, [$userId], 'i');
    return $result ? (float)$result['total'] : 0;
}

// Get all categories
function getAllCategories() {
    $sql = "SELECT * FROM categories ORDER BY category_name";
    return executeQuery($sql);
}

// Register user
function registerUser($username, $email, $password, $fullName, $phone = '', $address = '') {
    // Check if username exists
    $sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
    $existing = getSingleRecord($sql, [$username, $email], 'ss');
    
    if ($existing) {
        return ['success' => false, 'message' => 'Username or email already exists'];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $sql = "INSERT INTO users (username, email, password, full_name, phone, address) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    try {
        $result = executeQuery($sql, [$username, $email, $hashedPassword, $fullName, $phone, $address], 'ssssss');
        if ($result && $result['affected_rows'] > 0) {
            return ['success' => true, 'message' => 'Registration successful! Please login.', 'user_id' => $result['insert_id']];
        }
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Login user
function loginUser($email, $password) {
    $sql = "SELECT user_id, username, email, password, full_name FROM users WHERE email = ?";
    $user = getSingleRecord($sql, [$email], 's');
    
    if (!$user) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    if (password_verify($password, $user['password'])) {
        startSession();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['full_name'];
        return ['success' => true, 'message' => 'Login successful!'];
    }
    
    return ['success' => false, 'message' => 'Invalid email or password'];
}

// Login admin
function loginAdmin($username, $password) {
    $sql = "SELECT admin_id, username, password, full_name, email FROM admins WHERE username = ?";
    $admin = getSingleRecord($sql, [$username], 's');
    
    if (!$admin) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    if (password_verify($password, $admin['password'])) {
        startSession();
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_email'] = $admin['email'];
        return ['success' => true, 'message' => 'Admin login successful!'];
    }
    
    return ['success' => false, 'message' => 'Invalid username or password'];
}

// Get current user data
function getCurrentUser() {
    $userId = getCurrentUserId();
    if (!$userId) return null;
    
    $sql = "SELECT * FROM users WHERE user_id = ?";
    return getSingleRecord($sql, [$userId], 'i');
}

// Get current admin data
function getCurrentAdmin() {
    $adminId = getCurrentAdminId();
    if (!$adminId) return null;
    
    $sql = "SELECT * FROM admins WHERE admin_id = ?";
    return getSingleRecord($sql, [$adminId], 'i');
}
?>
<?php
// Add these functions to your existing functions.php

// Get active banners
function getActiveBanners() {
    $sql = "SELECT * FROM banners WHERE is_active = 1 ORDER BY display_order ASC, banner_id DESC";
    return executeQuery($sql);
}

// Get single banner
function getBanner($bannerId) {
    $sql = "SELECT * FROM banners WHERE banner_id = ? AND is_active = 1";
    return getSingleRecord($sql, [$bannerId], 'i');
}

// Get banner image path
function getBannerImage($imageName) {
    if ($imageName && file_exists('uploads/banners/' . $imageName)) {
        return 'uploads/banners/' . $imageName;
    }
    return 'assets/images/default-banner.jpg';
}

// Get site setting
function getSetting($key) {
    $sql = "SELECT setting_value FROM settings WHERE setting_key = ?";
    $result = getSingleRecord($sql, [$key], 's');
    return $result ? $result['setting_value'] : null;
}

// Update site setting
function updateSetting($key, $value) {
    $sql = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
    return executeQuery($sql, [$value, $key], 'ss');
}

// Add banner
function addBanner($title, $subtitle, $image, $buttonText, $buttonLink, $isActive = 1) {
    $sql = "INSERT INTO banners (banner_title, banner_subtitle, banner_image, button_text, button_link, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)";
    return executeQuery($sql, [$title, $subtitle, $image, $buttonText, $buttonLink, $isActive], 'sssssi');
}

// Update banner
function updateBanner($bannerId, $title, $subtitle, $image, $buttonText, $buttonLink, $isActive) {
    if ($image) {
        $sql = "UPDATE banners SET banner_title=?, banner_subtitle=?, banner_image=?, button_text=?, button_link=?, is_active=? 
                WHERE banner_id=?";
        return executeQuery($sql, [$title, $subtitle, $image, $buttonText, $buttonLink, $isActive, $bannerId], 'sssssii');
    } else {
        $sql = "UPDATE banners SET banner_title=?, banner_subtitle=?, button_text=?, button_link=?, is_active=? 
                WHERE banner_id=?";
        return executeQuery($sql, [$title, $subtitle, $buttonText, $buttonLink, $isActive, $bannerId], 'ssssii');
    }
}

// Delete banner
function deleteBanner($bannerId) {
    $banner = getSingleRecord("SELECT banner_image FROM banners WHERE banner_id = ?", [$bannerId], 'i');
    if ($banner && $banner['banner_image']) {
        $imagePath = 'uploads/banners/' . $banner['banner_image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    $sql = "DELETE FROM banners WHERE banner_id = ?";
    return executeQuery($sql, [$bannerId], 'i');
}
// Get category image path
function getCategoryImage($imageName) {
    if (!empty($imageName) && file_exists('uploads/categories/' . $imageName)) {
        return 'uploads/categories/' . $imageName;
    }
    // Return default placeholder if no image
    return 'assets/images/no-image.png';
}

// Get category image path for admin
function getCategoryImageAdmin($imageName) {
    if (!empty($imageName) && file_exists('../uploads/categories/' . $imageName)) {
        return '../uploads/categories/' . $imageName;
    }
    return '../assets/images/no-image.png';
}
?>
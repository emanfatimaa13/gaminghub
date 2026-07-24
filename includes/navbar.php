<?php
// Start session for navbar
startSession();

// ===== CART COUNT =====
$cartCount = 0;
$wishlistCount = 0;

if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    
    // Get cart count
    $sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $result = getSingleRecord($sql, [$userId], 'i');
    $cartCount = $result ? (int)$result['total'] : 0;
    
    // Get wishlist count
    $sql = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?";
    $result = getSingleRecord($sql, [$userId], 'i');
    $wishlistCount = $result ? (int)$result['total'] : 0;
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <!-- Brand Logo -->
        <a class="navbar-brand fw-bold gaming-logo" href="index.php">
            GamingStore
        </a>
        
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Left Menu -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="products.php">
                        Products
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        Categories
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                        $categories = getAllCategories();
                        foreach ($categories as $cat) {
                            echo '<li><a class="dropdown-item" href="category.php?id=' . $cat['category_id'] . '">';
                            echo htmlspecialchars($cat['category_name']);
                            echo '</a></li>';
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="about.php">
                        About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="contact.php">
                        Contact
                    </a>
                </li>
            </ul>
            
            <!-- Search Form -->
            <form class="d-flex me-3" action="search.php" method="GET">
                <div class="input-group">
                    <input class="form-control form-control-sm" type="search" name="q" placeholder="Search products..." aria-label="Search">
                    <button class="btn btn-outline-light btn-sm" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <!-- Right Menu -->
            <ul class="navbar-nav align-items-center">
                
                <!-- ===== THEME TOGGLE BUTTON ===== -->
                <li class="nav-item">
                    <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()" title="Toggle Theme">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>
                
                <?php if (isLoggedIn()): ?>
                    <!-- Wishlist -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="wishlist.php" title="Wishlist">
                            <i class="fas fa-heart"></i>
                            <?php if ($wishlistCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $wishlistCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <!-- Cart -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="cart.php" title="Cart">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if ($cartCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $cartCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                            <li><a class="dropdown-item" href="wishlist.php">Wishlist</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Login & Register -->
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary btn-sm px-3 ms-2" href="register.php">
                            Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
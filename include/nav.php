<?php 
// Check if session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm py-2">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand fw-bold fs-4" href="index.php">
            <i class="fas fa-shopping-bag me-1"></i>ShopEase
        </a>
        
        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Navigation Links -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link px-1 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active fw-bold' : ''; ?>" 
                       href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-1 <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active fw-bold' : ''; ?>" 
                       href="products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-1 <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active fw-bold' : ''; ?>" 
                       href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-1 <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active fw-bold' : ''; ?>" 
                       href="contact.php">Contact</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-1" href="#" id="categoriesDropdown" 
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categories
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                        <li><a class="dropdown-item" href="products.php?category=electronics">Electronics</a></li>
                        <li><a class="dropdown-item" href="products.php?category=fashion">Fashion</a></li>
                        <li><a class="dropdown-item" href="products.php?category=travel">Travel Things</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="products.php">All Categories</a></li>
                    </ul>
                </li>
            </ul>
            
            <!-- Right Section: Search + Buttons (Same line) -->
            <div class="d-flex align-items-center gap-1">
                <!-- Search Bar -->
                <form class="d-flex search-form" action="index.php" method="GET">
                    <div class="input-group input-group-sm">
                        <input type="search" class="form-control border-end-0" 
                               placeholder="Search products..." name="q" 
                               aria-label="Search" style="width: 250px;">
                        <button class="btn btn-outline-primary border" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- User Authentication Section -->
                <div class="d-flex align-items-center gap-2">
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                        <!-- User is logged in - Show single button with username and logout -->
                        <div class="dropdown">
                            <a href="#" class="btn btn-primary dropdown-toggle d-flex align-items-center" 
                               id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-2"></i>
                                <div class="d-flex flex-column align-items-start">
                                    <span class="fw-semibold" style="font-size: 0.85rem; line-height: 1;">
                                        <?php 
                                        // Display first name only if available
                                        if(isset($_SESSION['user_name'])) {
                                            $name_parts = explode(' ', $_SESSION['user_name']);
                                            echo htmlspecialchars($name_parts[0]);
                                        }
                                        ?>
                                    </span>
                                    <small class="text-white opacity-75" style="font-size: 0.7rem; line-height: 1;">
                                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                                    </small>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <div class="dropdown-header">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user fa-lg"></i>
                                            </div>
                                            <div>
                                                <?php if(isset($_SESSION['user_name'])): ?>
                                                    <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                                                <?php endif; ?>
                                                <?php if(isset($_SESSION['user_email'])): ?>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($_SESSION['user_email']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="profile.php">
                                        <i class="fas fa-user-circle me-2"></i> My Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="orders.php">
                                        <i class="fas fa-shopping-bag me-2"></i> My Orders
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="wishlist.php">
                                        <i class="fas fa-heart me-2"></i> Wishlist
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="dashboard.php">
                                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="settings.php">
                                        <i class="fas fa-cog me-2"></i> Settings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                        
                    <?php else: ?>
                        <!-- User is NOT logged in - Show Login and Sign Up buttons -->
                        <a href="login.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                        <a href="sign up.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-user-plus me-1"></i> Sign Up
                        </a>
                    <?php endif; ?>
                    
                    <!-- Cart Button (Always visible) -->
                    <a href="cart.php" class="btn btn-outline-primary position-relative">
                        <i class="fas fa-shopping-cart me-1"></i> Cart
                        <?php
                        // Get cart count from session or set default
                        $cart_count = isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0;
                        if ($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
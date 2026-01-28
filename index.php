<?php 
// Debug mode - set to true to see errors, false for production
$debug_mode = true;
if($debug_mode) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

include "admin/db_connection.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase - Premium Online Store</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/index.css">
</head>
<body>
<?php include "include/nav.php"; ?>    

<?php
// Handle search functionality
$search_query = '';
$search_results = false;
$search_result = null;
$search_count = 0;

if(isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search_query = trim($_GET['q']);
    $search_results = true;
    
    // Sanitize the search query for SQL
    $search_term = $conn->real_escape_string($search_query);
    
    // First, let's check if products table exists and has data
    $check_table = $conn->query("SHOW TABLES LIKE 'products'");
    if($check_table && $check_table->num_rows > 0) {
        // Table exists, now check if it has columns we need
        $check_columns = $conn->query("SHOW COLUMNS FROM products");
        $columns = [];
        if($check_columns) {
            while($col = $check_columns->fetch_assoc()) {
                $columns[] = $col['Field'];
            }
        }
        
        // Build search query based on available columns
        $where_conditions = [];
        if(in_array('name', $columns)) {
            $where_conditions[] = "name LIKE '%$search_term%'";
        }
        if(in_array('description', $columns)) {
            $where_conditions[] = "description LIKE '%$search_term%'";
        }
        if(in_array('category', $columns)) {
            $where_conditions[] = "category LIKE '%$search_term%'";
        }
        
        if(!empty($where_conditions)) {
            $search_sql = "SELECT * FROM products WHERE " . implode(" OR ", $where_conditions);
            
            // Debug: Show the SQL query
            if($debug_mode) {
                echo "<!-- Search SQL: " . htmlspecialchars($search_sql) . " -->";
            }
            
            // Execute query
            $search_result = $conn->query($search_sql);
            
            if($search_result === false) {
                // If query fails, show error in debug mode
                if($debug_mode) {
                    echo '<div class="container alert alert-danger mt-3">';
                    echo 'SQL Error: ' . $conn->error;
                    echo '</div>';
                }
            } else {
                $search_count = $search_result->num_rows;
            }
        } else {
            if($debug_mode) {
                echo '<div class="container alert alert-warning mt-3">';
                echo 'No searchable columns found in products table.';
                echo '</div>';
            }
        }
    } else {
        if($debug_mode) {
            echo '<div class="container alert alert-warning mt-3">';
            echo 'Products table does not exist.';
            echo '</div>';
        }
    }
    
    // Also check total products count for debugging
    $total_products = $conn->query("SELECT COUNT(*) as total FROM products");
    if($total_products && $debug_mode) {
        $total = $total_products->fetch_assoc();
        echo "<!-- Total products in database: " . $total['total'] . " -->";
    }
}
?>

<!-- Product Carousel - Only show when not searching -->
<?php if(!$search_results): ?>
<section id="home" class="container my-5">
    <h2 class="section-title">Featured Products</h2>
    <?php
    // Fetch featured products for carousel
    $featured_query = "SELECT * FROM products WHERE badge = 'NEW' OR badge = 'SALE' LIMIT 3";
    $featured_result = $conn->query($featured_query);
    
    if($featured_result === false) {
        echo '<div class="alert alert-warning">Featured products will be available soon!</div>';
        include "include/static_carousel.php";
    } else {
    ?>
    <div id="productCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php
            $carousel_count = 0;
            
            if($featured_result->num_rows > 0) {
                while($row = $featured_result->fetch_assoc()) {
                    $active_class = $carousel_count == 0 ? 'active' : '';
                    echo '<button type="button" data-bs-target="#productCarousel" data-bs-slide-to="'.$carousel_count.'" class="'.$active_class.'"></button>';
                    $carousel_count++;
                }
                $featured_result->data_seek(0);
            } else {
                echo '<button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active"></button>';
                echo '<button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1"></button>';
                echo '<button type="button" data-bs-target="#productCarousel" data-bs-slide-to="2"></button>';
            }
            ?>
        </div>
        <div class="carousel-inner">
            <?php
            if($featured_result->num_rows > 0) {
                $carousel_index = 0;
                
                while($row = $featured_result->fetch_assoc()) {
                    $active_class = $carousel_index == 0 ? 'active' : '';
                    echo '<div class="carousel-item '.$active_class.'">';
                    echo '<img src="'.$row['image_url'].'" class="d-block w-100" alt="'.$row['name'].'" style="height:500px;object-fit:cover;">';
                    echo '<div class="carousel-caption d-none d-md-block">';
                    echo '<h3>'.$row['name'].'</h3>';
                    echo '<p>'.substr($row['description'], 0, 100).'...</p>';
                    echo '<a href="product_details.php?id='.$row['id'].'" class="btn btn-primary">Shop Now</a>';
                    echo '</div>';
                    echo '</div>';
                    $carousel_index++;
                }
            } else {
                ?>
                <div class="carousel-item active">
                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" class="d-block w-100" alt="Headphones" style="height:500px;object-fit:cover;">
                    <div class="carousel-caption d-none d-md-block">
                        <h3>Premium Wireless Headphones</h3>
                        <p>Noise cancellation with 30-hour battery life</p>
                        <a href="#" class="btn btn-primary">Shop Now</a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" class="d-block w-100" alt="Camera" style="height:500px;object-fit:cover;">
                    <div class="carousel-caption d-none d-md-block">
                        <h3>Professional DSLR Camera</h3>
                        <p>Capture memories in 4K resolution</p>
                        <a href="#" class="btn btn-primary">Shop Now</a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1546868871-7041f2a55e12?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" class="d-block w-100" alt="Smart Watch" style="height:500px;object-fit:cover;">
                    <div class="carousel-caption d-none d-md-block">
                        <h3>Smart Fitness Watch</h3>
                        <p>Track your health and fitness goals</p>
                        <a href="#" class="btn btn-primary">Shop Now</a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
    <?php } ?>
</section>
<?php endif; ?>

<!-- Main Products Section -->
<section id="products" class="container my-5">
    <?php if($search_results): ?>
        <!-- Search Results Header -->
        <h2 class="section-title">Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
        
        <?php if($debug_mode && $search_result === false): ?>
            <div class="alert alert-info">
                <h5>Debug Information:</h5>
                <p>Search term: <?php echo htmlspecialchars($search_query); ?></p>
                <p>Search term (escaped): <?php echo htmlspecialchars($search_term); ?></p>
                <p>Search count: <?php echo $search_count; ?></p>
                <p>Products table exists: <?php echo ($check_table && $check_table->num_rows > 0) ? 'Yes' : 'No'; ?></p>
            </div>
        <?php endif; ?>
        
        <p class="text-muted mb-4">Found <?php echo $search_count; ?> product(s)</p>
        
        <!-- Clear Search Button -->
        <div class="mb-4">
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i>Clear Search
            </a>
        </div>
        
        <?php
        if($search_result !== false && $search_count > 0): ?>
            <div class="row">
                <?php while($product = $search_result->fetch_assoc()): 
                    // Determine badge type
                    $badge_html = '';
                    if(!empty($product['badge'])) {
                        if($product['badge'] == 'NEW') {
                            $badge_html = '<span class="badge badge-new">NEW</span>';
                        } elseif($product['badge'] == 'SALE') {
                            $badge_html = '<span class="badge badge-sale">SALE</span>';
                        } else {
                            $badge_html = '<span class="badge">'.$product['badge'].'</span>';
                        }
                    }
                    
                    // Calculate price display
                    $price_html = '$'.number_format($product['price'], 2);
                    if(!empty($product['original_price']) && $product['original_price'] > 0 && $product['original_price'] < $product['price']) {
                        $price_html = '$'.number_format($product['original_price'], 2).' <small class="text-muted text-decoration-line-through">$'.number_format($product['price'], 2).'</small>';
                    }
                ?>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card product-card">
                        <?php echo $badge_html; ?>
                        <img src="<?php echo $product['image_url']; ?>" class="card-img-top product-img" alt="<?php echo $product['name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title product-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text"><?php echo substr($product['description'], 0, 100); ?>...</p>
                            <?php if(isset($product['category']) && !empty($product['category'])): ?>
                                <p class="card-text"><small class="text-muted">Category: <?php echo $product['category']; ?></small></p>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price"><?php echo $price_html; ?></span>
                                <div>
                                    <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm">View</a>
                                    <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">Add to Cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php elseif($search_result === false): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                There was an error with the search query. Please try again.
                <?php if($debug_mode && $conn->error): ?>
                    <br><small>Error details: <?php echo $conn->error; ?></small>
                <?php endif; ?>
            </div>
            <div class="text-center py-3">
                <a href="index.php" class="btn btn-primary">Back to Home</a>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>No products found for "<?php echo htmlspecialchars($search_query); ?>"</h4>
                <p class="text-muted">Try different keywords or browse our categories</p>
                
                <?php if($debug_mode): ?>
                    <div class="alert alert-info mt-3">
                        <h5>Debug Info:</h5>
                        <p>Search query executed successfully but returned 0 results.</p>
                        <p>Try searching for common terms or check if products exist in the database.</p>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <a href="products.php" class="btn btn-primary">Browse All Products</a>
                    <a href="index.php" class="btn btn-outline-secondary ms-2">Back to Home</a>
                </div>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- Normal Products Display (When not searching) -->
        <h2 class="section-title">Our Products</h2>
        <div class="row">
            <?php
            // Fetch all products
            $products_query = "SELECT * FROM products ORDER BY created_at DESC LIMIT 6";
            $products_result = $conn->query($products_query);
            
            if($products_result === false) {
                echo '<div class="col-12 text-center"><div class="alert alert-warning">Products will be available soon!</div></div>';
            } elseif($products_result->num_rows > 0) {
                while($product = $products_result->fetch_assoc()) {
                    // Determine badge type
                    $badge_html = '';
                    if(!empty($product['badge'])) {
                        if($product['badge'] == 'NEW') {
                            $badge_html = '<span class="badge badge-new">NEW</span>';
                        } elseif($product['badge'] == 'SALE') {
                            $badge_html = '<span class="badge badge-sale">SALE</span>';
                        } else {
                            $badge_html = '<span class="badge">'.$product['badge'].'</span>';
                        }
                    }
                    
                    // Calculate price display
                    $price_html = '$'.number_format($product['price'], 2);
                    if(!empty($product['original_price']) && $product['original_price'] > 0 && $product['original_price'] < $product['price']) {
                        $price_html = '$'.number_format($product['original_price'], 2).' <small class="text-muted text-decoration-line-through">$'.number_format($product['price'], 2).'</small>';
                    }
                    
                    echo '<div class="col-md-4 col-sm-6 mb-4">';
                    echo '<div class="card product-card">';
                    echo $badge_html;
                    echo '<img src="'.$product['image_url'].'" class="card-img-top product-img" alt="'.$product['name'].'">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title product-title">'.$product['name'].'</h5>';
                    echo '<p class="card-text">'.substr($product['description'], 0, 100).'...</p>';
                    echo '<div class="d-flex justify-content-between align-items-center">';
                    echo '<span class="product-price">'.$price_html.'</span>';
                    echo '<div>';
                    echo '<a href="product_details.php?id='.$product['id'].'" class="btn btn-outline-primary btn-sm me-2">View</a>';
                    echo '<a href="add_to_cart.php?id='.$product['id'].'" class="btn btn-primary btn-sm">Add to Cart</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12 text-center"><p>No products available at the moment. Check back soon!</p></div>';
                if($debug_mode) {
                    echo '<div class="alert alert-info mt-3">';
                    echo 'The products table exists but has no data. Add some products to the database.';
                    echo '</div>';
                }
            }
            ?>
        </div>
        
        <!-- View All Products Button -->
        <div class="text-center mt-4">
            <a href="products.php" class="btn btn-outline-primary btn-lg">View All Products</a>
        </div>
    <?php endif; ?>
</section>

<!-- Trending Products Section - Only show when not searching -->
<?php if(!$search_results): ?>
<section id="trending" class="container my-5">
    <h2 class="section-title">Trending Now <span class="trending-badge">HOT</span></h2>
    <div class="row">
        <?php
        // Fetch trending products
        $trending_query = "SELECT * FROM products ORDER BY created_at DESC LIMIT 4";
        $trending_result = $conn->query($trending_query);
        
        if($trending_result === false) {
            echo '<div class="col-12 text-center"><div class="alert alert-warning">Trending products coming soon!</div></div>';
        } elseif($trending_result->num_rows > 0) {
            while($product = $trending_result->fetch_assoc()) {
                $price_html = '$'.number_format($product['price'], 2);
                if(!empty($product['original_price']) && $product['original_price'] > 0) {
                    $price_html = '$'.number_format($product['original_price'], 2);
                }
                
                echo '<div class="col-md-3 col-sm-6 mb-4">';
                echo '<div class="card product-card">';
                echo '<img src="'.$product['image_url'].'" class="card-img-top product-img" alt="'.$product['name'].'">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title product-title" style="font-size: 1rem;">'.$product['name'].'</h5>';
                echo '<div class="d-flex justify-content-between align-items-center">';
                echo '<span class="product-price" style="font-size: 1.1rem;">'.$price_html.'</span>';
                echo '<a href="product_details.php?id='.$product['id'].'" class="btn btn-primary btn-sm">View</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="col-12 text-center"><p>No trending products available.</p></div>';
        }
        ?>
    </div>
</section>
<?php endif; ?>

<?php include "include/footer.php"; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if(targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if(targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 70,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Highlight active nav link on scroll
    window.addEventListener('scroll', function() {
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.nav-link');
        
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if(scrollY >= (sectionTop - 100)) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if(link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });
    
    // Initialize carousel only if it exists
    var carouselElement = document.getElementById('productCarousel');
    if(carouselElement) {
        var carousel = new bootstrap.Carousel(carouselElement, {
            interval: 3000,
            wrap: true
        });
    }
    
    // Preserve search query in search box
    <?php if($search_results): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="q"]');
        if(searchInput) {
            searchInput.value = "<?php echo htmlspecialchars($search_query, ENT_QUOTES); ?>";
        }
    });
    <?php endif; ?>
</script>
</body>
</html>
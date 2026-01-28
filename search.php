<?php
// search.php
session_start();
include "admin/db_connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - ShopEase</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding-top: 80px;
            background-color: #f8f9fa;
        }
        
        .search-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .product-card {
            border: 1px solid #eaeaea;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .product-img {
            height: 200px;
            object-fit: cover;
        }
        
        .badge-new {
            background: #28a745;
            color: white;
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 5px 10px;
            border-radius: 3px;
        }
        
        .badge-sale {
            background: #dc3545;
            color: white;
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 5px 10px;
            border-radius: 3px;
        }
        
        .no-results {
            text-align: center;
            padding: 50px 20px;
        }
        
        .search-query {
            color: #764ba2;
            font-weight: bold;
        }
        
        .results-count {
            color: #666;
            font-size: 1.1rem;
        }
        
        .sort-options {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include "include/nav.php"; ?>
    
    <div class="container">
        <div class="search-header text-center">
            <h1><i class="fas fa-search"></i> Search Results</h1>
            <p class="lead">Find the perfect products for you</p>
        </div>
        
        <?php
        // Get search query
        $search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
        $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'relevance';
        
        if (empty($search_query)) {
            echo '<div class="alert alert-warning">Please enter a search term.</div>';
            echo '<div class="text-center mt-4"><a href="index.php" class="btn btn-primary">Go Back to Home</a></div>';
        } else {
            echo '<div class="row mb-4">';
            echo '<div class="col-md-8">';
            echo '<h4>Search results for: <span class="search-query">"' . htmlspecialchars($search_query) . '"</span></h4>';
            echo '</div>';
            echo '<div class="col-md-4">';
            ?>
            <!-- Sort Options -->
            <form method="GET" class="sort-options">
                <input type="hidden" name="query" value="<?php echo htmlspecialchars($search_query); ?>">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-sort"></i></span>
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="relevance" <?php echo $sort_by == 'relevance' ? 'selected' : ''; ?>>Sort by Relevance</option>
                        <option value="price_low" <?php echo $sort_by == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo $sort_by == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name_asc" <?php echo $sort_by == 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                        <option value="name_desc" <?php echo $sort_by == 'name_desc' ? 'selected' : ''; ?>>Name: Z to A</option>
                        <option value="newest" <?php echo $sort_by == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    </select>
                </div>
            </form>
            <?php
            echo '</div>';
            echo '</div>';
            
            // Prepare search query
            $search_terms = explode(' ', $search_query);
            $where_clauses = [];
            $params = [];
            $types = '';
            
            foreach ($search_terms as $term) {
                if (!empty($term)) {
                    $where_clauses[] = "(products.name LIKE ? OR products.description LIKE ? OR products.category LIKE ?)";
                    $term = "%$term%";
                    $params[] = $term;
                    $params[] = $term;
                    $params[] = $term;
                    $types .= 'sss';
                }
            }
            
            if (empty($where_clauses)) {
                $where_clauses[] = "1=1";
            }
            
            $where_sql = implode(' AND ', $where_clauses);
            
            // Sort order
            $order_by = "ORDER BY ";
            switch ($sort_by) {
                case 'price_low':
                    $order_by .= "COALESCE(NULLIF(original_price, 0), price) ASC";
                    break;
                case 'price_high':
                    $order_by .= "COALESCE(NULLIF(original_price, 0), price) DESC";
                    break;
                case 'name_asc':
                    $order_by .= "name ASC";
                    break;
                case 'name_desc':
                    $order_by .= "name DESC";
                    break;
                case 'newest':
                    $order_by .= "created_at DESC";
                    break;
                default: // relevance
                    $order_by .= "(CASE 
                        WHEN name LIKE ? THEN 1 
                        WHEN description LIKE ? THEN 2 
                        WHEN category LIKE ? THEN 3 
                        ELSE 4 
                    END)";
                    array_unshift($params, "%$search_query%", "%$search_query%", "%$search_query%");
                    $types = 'sss' . $types;
                    break;
            }
            
            // Count total results
            $count_sql = "SELECT COUNT(*) as total FROM products WHERE $where_sql";
            $count_stmt = $conn->prepare($count_sql);
            
            if ($count_stmt) {
                if (!empty($params)) {
                    $count_stmt->bind_param($types, ...$params);
                }
                $count_stmt->execute();
                $count_result = $count_stmt->get_result();
                $total_count = $count_result->fetch_assoc()['total'];
                $count_stmt->close();
                
                echo '<p class="results-count">Found ' . $total_count . ' products</p>';
            }
            
            // Fetch products with pagination
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $per_page = 12;
            $offset = ($page - 1) * $per_page;
            
            $sql = "SELECT * FROM products WHERE $where_sql $order_by LIMIT ? OFFSET ?";
            
            // Add LIMIT and OFFSET to params
            $params[] = $per_page;
            $params[] = $offset;
            $types .= 'ii';
            
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    echo '<div class="row">';
                    
                    while ($product = $result->fetch_assoc()) {
                        // Determine badge type
                        $badge_html = '';
                        if (!empty($product['badge'])) {
                            if ($product['badge'] == 'NEW') {
                                $badge_html = '<span class="badge badge-new">NEW</span>';
                            } elseif ($product['badge'] == 'SALE') {
                                $badge_html = '<span class="badge badge-sale">SALE</span>';
                            } else {
                                $badge_html = '<span class="badge">' . $product['badge'] . '</span>';
                            }
                        }
                        
                        // Calculate price display
                        $price_html = '$' . number_format($product['price'], 2);
                        if (!empty($product['original_price']) && $product['original_price'] > 0 && $product['original_price'] < $product['price']) {
                            $price_html = '$' . number_format($product['original_price'], 2) . 
                                         ' <small class="text-muted text-decoration-line-through">$' . 
                                         number_format($product['price'], 2) . '</small>';
                        }
                        
                        echo '<div class="col-md-3 col-sm-6 mb-4">';
                        echo '<div class="card product-card">';
                        echo $badge_html;
                        echo '<img src="' . $product['image_url'] . '" class="card-img-top product-img" alt="' . $product['name'] . '">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title product-title">' . $product['name'] . '</h5>';
                        echo '<p class="card-text text-muted" style="font-size: 0.9rem;">';
                        echo substr($product['description'], 0, 80) . '...';
                        echo '</p>';
                        echo '<div class="d-flex justify-content-between align-items-center">';
                        echo '<span class="product-price fw-bold">' . $price_html . '</span>';
                        echo '<a href="add_to_cart.php?id=' . $product['id'] . '" class="btn btn-primary btn-sm">';
                        echo '<i class="fas fa-cart-plus"></i>';
                        echo '</a>';
                        echo '</div>';
                        echo '<div class="mt-2 text-center">';
                        echo '<a href="product_details.php?id=' . $product['id'] . '" class="btn btn-outline-secondary btn-sm w-100">View Details</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    
                    echo '</div>';
                    
                    // Pagination
                    if ($total_count > $per_page) {
                        $total_pages = ceil($total_count / $per_page);
                        
                        echo '<nav aria-label="Search results pagination">';
                        echo '<ul class="pagination justify-content-center">';
                        
                        // Previous button
                        if ($page > 1) {
                            echo '<li class="page-item">';
                            echo '<a class="page-link" href="?query=' . urlencode($search_query) . 
                                 '&sort=' . $sort_by . '&page=' . ($page - 1) . '">Previous</a>';
                            echo '</li>';
                        }
                        
                        // Page numbers
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        for ($i = $start_page; $i <= $end_page; $i++) {
                            $active = $i == $page ? ' active' : '';
                            echo '<li class="page-item' . $active . '">';
                            echo '<a class="page-link" href="?query=' . urlencode($search_query) . 
                                 '&sort=' . $sort_by . '&page=' . $i . '">' . $i . '</a>';
                            echo '</li>';
                        }
                        
                        // Next button
                        if ($page < $total_pages) {
                            echo '<li class="page-item">';
                            echo '<a class="page-link" href="?query=' . urlencode($search_query) . 
                                 '&sort=' . $sort_by . '&page=' . ($page + 1) . '">Next</a>';
                            echo '</li>';
                        }
                        
                        echo '</ul>';
                        echo '</nav>';
                    }
                } else {
                    echo '<div class="no-results">';
                    echo '<i class="fas fa-search fa-4x text-muted mb-3"></i>';
                    echo '<h3>No products found</h3>';
                    echo '<p class="text-muted">Try different keywords or browse our categories</p>';
                    echo '<a href="index.php" class="btn btn-primary mt-3">Browse All Products</a>';
                    echo '</div>';
                }
                
                $stmt->close();
            } else {
                echo '<div class="alert alert-danger">Error in search query: ' . $conn->error . '</div>';
            }
        }
        ?>
    </div>
    
    <?php include "include/footer.php"; ?>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Highlight search terms in results
        document.addEventListener('DOMContentLoaded', function() {
            const searchQuery = "<?php echo addslashes($search_query); ?>";
            if (searchQuery) {
                const searchTerms = searchQuery.toLowerCase().split(' ');
                const productTitles = document.querySelectorAll('.product-title');
                
                productTitles.forEach(title => {
                    const originalText = title.textContent;
                    let highlightedText = originalText;
                    
                    searchTerms.forEach(term => {
                        if (term.length > 2) {
                            const regex = new RegExp(`(${term})`, 'gi');
                            highlightedText = highlightedText.replace(regex, '<mark>$1</mark>');
                        }
                    });
                    
                    if (highlightedText !== originalText) {
                        title.innerHTML = highlightedText;
                    }
                });
            }
        });
    </script>
</body>
</html>
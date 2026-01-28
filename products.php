<?php include"admin/db_connection.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - ShopEase Premium Online Store</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles/products.css">
</head>
<body>
<?php include"include/nav.php"; ?> 
    <!-- Main Content -->
    <div class="container mt-5">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="section-title">Our Products</h1>
                <p class="text-muted">Discover our wide range of premium products</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex justify-content-end align-items-center">
                    <span class="me-2">Sort by:</span>
                    <select class="form-select sort-dropdown w-auto">
                        <option selected>Featured</option>
                        <option value="price-asc">Price: Low to High</option>
                        <option value="price-desc">Price: High to Low</option>
                        <option value="name-asc">Name: A to Z</option>
                        <option value="name-desc">Name: Z to A</option>
                        <option value="newest">Newest Arrivals</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 filter-section">
                <div class="filter-card">
                    <h5 class="filter-title">Categories</h5>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="electronics" checked>
                        <label class="form-check-label" for="electronics">
                            Electronics
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="fashion">
                        <label class="form-check-label" for="fashion">
                            Fashion & Accessories
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="home">
                        <label class="form-check-label" for="home">
                            Home & Kitchen
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="sports">
                        <label class="form-check-label" for="sports">
                            Sports & Outdoors
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="books">
                        <label class="form-check-label" for="books">
                            Books & Media
                        </label>
                    </div>
                </div>
                
                <div class="filter-card">
                    <h5 class="filter-title">Price Range</h5>
                    <input type="range" class="form-range price-range" min="0" max="2000" value="1000">
                    <div class="price-values">
                        <span>$0</span>
                        <span>$1000</span>
                        <span>$2000</span>
                    </div>
                </div>
                
                <div class="filter-card">
                    <h5 class="filter-title">Brand</h5>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="brand1" checked>
                        <label class="form-check-label" for="brand1">
                            TechPro
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="brand2" checked>
                        <label class="form-check-label" for="brand2">
                            UrbanStyle
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="brand3">
                        <label class="form-check-label" for="brand3">
                            HomeEssentials
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="brand4">
                        <label class="form-check-label" for="brand4">
                            ActiveLife
                        </label>
                    </div>
                </div>
                
                <div class="filter-card">
                    <h5 class="filter-title">Rating</h5>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="rating5">
                        <label class="form-check-label" for="rating5">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            4.5 & above
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="rating4">
                        <label class="form-check-label" for="rating4">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="far fa-star text-warning"></i>
                            4.0 & above
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="rating3">
                        <label class="form-check-label" for="rating3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="far fa-star text-warning"></i>
                            <i class="far fa-star text-warning"></i>
                            3.0 & above
                        </label>
                    </div>
                </div>
                
                <button class="btn btn-primary w-100 mb-3">Apply Filters</button>
                <button class="btn btn-outline-primary w-100">Reset Filters</button>
            </div>
            
            <!-- Products Grid -->
            <div class="col-lg-9">
                <div class="row" id="productsGrid">
                    <!-- Product 1 -->
                    <div class="col-md-4 col-sm-6 product-item" data-category="electronics" data-price="199.99" data-rating="4.5">
                        <div class="card product-card">
                            <span class="badge badge-new">NEW</span>
                            <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top product-img" alt="Headphones">
                            <div class="card-body">
                                <h5 class="card-title product-title">Premium Wireless Headphones</h5>
                                <p class="card-text">Noise cancellation with 30-hour battery life. Perfect for travel and work.</p>
                                <div class="product-rating mb-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                    <span class="text-muted ms-1">(4.5)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">$199.99</span>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-product-id="1">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 2 -->
                    <div class="col-md-4 col-sm-6 product-item" data-category="electronics" data-price="749.99" data-rating="4.8">
                        <div class="card product-card">
                            <img src="https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top product-img" alt="Camera">
                            <div class="card-body">
                                <h5 class="card-title product-title">Professional DSLR Camera</h5>
                                <p class="card-text">24.2MP DSLR camera with 18-55mm lens. Perfect for photography enthusiasts.</p>
                                <div class="product-rating mb-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <span class="text-muted ms-1">(4.8)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">$749.99</span>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-product-id="2">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 3 -->
                    <div class="col-md-4 col-sm-6 product-item" data-category="electronics" data-price="299.99" data-rating="4.3">
                        <div class="card product-card">
                            <span class="badge badge-sale">SALE</span>
                            <img src="https://images.unsplash.com/photo-1546868871-7041f2a55e12?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top product-img" alt="Smart Watch">
                            <div class="card-body">
                                <h5 class="card-title product-title">Smart Fitness Watch</h5>
                                <p class="card-text">Track heart rate, sleep, and workouts. Water resistant up to 50 meters.</p>
                                <div class="product-rating mb-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    <span class="text-muted ms-1">(4.3)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">$299.99 <small class="text-muted text-decoration-line-through">$349.99</small></span>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-product-id="3">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 4 -->
                    <div class="col-md-4 col-sm-6 product-item" data-category="electronics" data-price="899.99" data-rating="4.6">
                        <div class="card product-card">
                            <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top product-img" alt="Smartphone">
                            <div class="card-body">
                                <h5 class="card-title product-title">Latest Smartphone</h5>
                                <p class="card-text">6.7" display, 128GB storage, triple camera system with night mode.</p>
                                <div class="product-rating mb-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                    <span class="text-muted ms-1">(4.6)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">$899.99</span>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-product-id="4">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 5 -->
                    <div class="col-md-4 col-sm-6 product-item" data-category="electronics" data-price="1299.99" data-rating="4.7">
                        <div class="card product-card">
                            <img src="https://images.unsplash.com/photo-1498049794561-7780e7231661?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top product-img" alt="Laptop">
                            <div class="card-body">
                                <h5 class="card-title product-title">Ultra-Thin Laptop</h5>
                                <p class="card-text">13-inch laptop with 16GB RAM, 512GB SSD, and 10-hour battery life.</p>
                                <div class="product-rating mb-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <span class="text-muted ms-1">(4.7)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">$1,299.99</span>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-product-id="5">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 6 -->
                    <div class="col-md-4 col-sm-6 product-item" data-category="electronics" data-price="499.99" data-rating="4.2">
                        <div class="card product-card">
                            <span class="badge badge-new">NEW</span>
                            <img src="https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top product-img" alt="Tablet">
                            <div class="card-body">
                                <h5 class="card-title product-title">Tablet with Stylus</h5>
                                <p class="card-text">10.5" tablet with included stylus for drawing and note-taking.</p>
                                <div class="product-rating mb-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    <span class="text-muted ms-1">(4.2)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">$499.99</span>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-product-id="6">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 7 -->
                    <div class="col-md-4 col-sm-6 product-item" data-category="fashion" data-price="149.99" data-rating="4.4">
                        <div class="card product-card">
                            <img src="https://images.unsplash.com/photo-1572635196237-14b3f281503f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top product-img" alt="Sunglasses">
                            <div class="card-body">
                                <h5 class="card-title product-title">Designer Sunglasses</h5>
                                <p class="card-text">UV protection with polarized lenses. Stylish design for all occasions.</p>
                                <div class="product-rating mb-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    <span class="text-muted ms-1">(4.4)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">$149.99</span>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-product-id="7">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 8 -->
                    <div class="col-md-4 col-sm-6 product-item" data-category="electronics" data-price="89.99" data-rating="4.1">
                        <div class="card product-card">
                            <img src="https://images.unsplash.com/photo-1559056199-641a0ac8b55e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top product-img" alt="Speaker">
                            <div class="card-body">
                                <h5 class="card-title product-title">Bluetooth Speaker</h5>
                                <p class="card-text">360° sound with deep bass. 12-hour battery life and waterproof design.</p>
                                <div class="product-rating mb-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    <span class="text-muted ms-1">(4.1)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">$89.99</span>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-product-id="8">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 9 -->
                    <div class="col-md-4 col-sm-6 product-item" data-category="sports" data-price="119.99" data-rating="4.6">
                        <div class="card product-card">
                            <span class="badge badge-new">NEW</span>
                            <img src="https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top product-img" alt="Shoes">
                            <div class="card-body">
                                <h5 class="card-title product-title">Running Shoes</h5>
                                <p class="card-text">Lightweight running shoes with superior cushioning and support.</p>
                                <div class="product-rating mb-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                    <span class="text-muted ms-1">(4.6)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">$119.99</span>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-product-id="9">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Product pagination" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
 <?php include"include/footer.php"; ?>
    <!-- Product Detail Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title product-detail-title" id="productModalLabel">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <img id="modalProductImage" src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="modal-product-img" alt="Product Image">
                            <div class="row mt-3">
                                <div class="col-3">
                                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" class="img-thumbnail" alt="Thumbnail 1">
                                </div>
                                <div class="col-3">
                                    <img src="https://images.unsplash.com/photo-1484704849700-f032a568e944?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" class="img-thumbnail" alt="Thumbnail 2">
                                </div>
                                <div class="col-3">
                                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" class="img-thumbnail" alt="Thumbnail 3">
                                </div>
                                <div class="col-3">
                                    <img src="https://images.unsplash.com/photo-1484704849700-f032a568e944?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" class="img-thumbnail" alt="Thumbnail 4">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h3 id="modalProductTitle">Premium Wireless Headphones</h3>
                            <div class="product-rating mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                <span class="text-muted ms-1">(4.5) 128 Reviews</span>
                            </div>
                            <h4 id="modalProductPrice" class="product-price mb-3">$199.99</h4>
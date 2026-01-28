<?php
// Include database connection - FIXED LINE
require_once 'db_connection.php';

// Handle product deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $success_msg = "Product deleted successfully!";
    } else {
        $error_msg = "Error deleting product: " . $stmt->error;
    }
    $stmt->close();
    
    // Redirect to remove delete_id from URL
    header("Location: products.php?msg=" . ($success_msg ? "deleted" : "error"));
    exit();
}

// Handle form submission for adding/editing products
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $badge = !empty($_POST['badge']) ? trim($_POST['badge']) : NULL;
    $original_price = !empty($_POST['original_price']) ? floatval($_POST['original_price']) : NULL;
    
    // Handle image upload
    $image_url = $_POST['existing_image'] ?? '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/products/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $file_name;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_url = $target_path;
        }
    }
    
    if ($id > 0) {
        // Update existing product
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, image_url = ?, 
                category = ?, badge = ?, original_price = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsssdi", $name, $description, $price, $image_url, 
                         $category, $badge, $original_price, $id);
    } else {
        // Insert new product
        $sql = "INSERT INTO products (name, description, price, image_url, category, badge, original_price) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsssd", $name, $description, $price, $image_url, 
                         $category, $badge, $original_price);
    }
    
    if ($stmt->execute()) {
        $success_msg = $id > 0 ? "Product updated successfully!" : "Product added successfully!";
        // Redirect to avoid form resubmission
        header("Location: products.php?msg=" . ($id > 0 ? "updated" : "added"));
        exit();
    } else {
        $error_msg = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all products
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);

// Fetch product for editing
$edit_product = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_product = $edit_result->fetch_assoc();
    $stmt->close();
}

// Get statistics
$total_products = $result->num_rows;
$sql_stats = "SELECT 
    COUNT(*) as total,
    SUM(price) as total_value,
    COUNT(CASE WHEN badge = 'SALE' THEN 1 END) as sale_items,
    COUNT(CASE WHEN badge = 'NEW' THEN 1 END) as new_items
    FROM products";
$stats_result = $conn->query($sql_stats);
$stats = $stats_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopNow Admin - Product Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles - Reusing from user.php */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #2c3e50, #1a252f);
            color: white;
            padding: 20px 0;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }

        .logo {
            text-align: center;
            padding: 20px 15px;
            border-bottom: 1px solid #34495e;
            margin-bottom: 20px;
        }

        .logo h2 {
            font-size: 1.8rem;
            color: #3498db;
        }

        .logo span {
            color: #e74c3c;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .nav-link:hover, .nav-link.active {
            background-color: #34495e;
            color: white;
            border-left: 4px solid #3498db;
        }

        .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #e0e0e0;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 1.8rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.5rem;
        }

        .card-info h3 {
            font-size: 2rem;
            color: #2c3e50;
        }

        .card-info p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .card-products .card-icon {
            background-color: #e3f2fd;
            color: #2196f3;
        }

        .card-value .card-icon {
            background-color: #e8f5e9;
            color: #4caf50;
        }

        .card-sale .card-icon {
            background-color: #fff3e0;
            color: #ff9800;
        }

        .card-new .card-icon {
            background-color: #f3e5f5;
            color: #9c27b0;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-header h3 {
            color: #2c3e50;
        }

        .search-box {
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 8px 15px;
            width: 300px;
        }

        .search-box i {
            color: #7f8c8d;
            margin-right: 10px;
        }

        .search-box input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 0.95rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #f0f0f0;
            color: #2c3e50;
            font-weight: 600;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 10px;
        }

        .product-cell {
            display: flex;
            align-items: center;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-new {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-sale {
            background-color: #f8d7da;
            color: #721c24;
        }

        .price {
            font-weight: 600;
            color: #2c3e50;
        }

        .original-price {
            text-decoration: line-through;
            color: #95a5a6;
            font-size: 0.9rem;
            margin-left: 5px;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-edit {
            background-color: #e3f2fd;
            color: #2196f3;
        }

        .btn-edit:hover {
            background-color: #2196f3;
            color: white;
        }

        .btn-delete {
            background-color: #fde8e8;
            color: #e74c3c;
        }

        .btn-delete:hover {
            background-color: #e74c3c;
            color: white;
        }

        .btn-add {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-add:hover {
            background-color: #3498db;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: #2c3e50;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #7f8c8d;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .image-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
            display: none;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }

        .btn-cancel {
            background-color: #f8f9fa;
            color: #333;
        }

        .btn-cancel:hover {
            background-color: #e9ecef;
        }

        .btn-save {
            background-color: #3498db;
            color: white;
        }

        .btn-save:hover {
            background-color: #2980b9;
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .close-alert {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: inherit;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                padding: 20px 5px;
            }
            
            .logo h2 {
                font-size: 1.2rem;
            }
            
            .nav-link span {
                display: none;
            }
            
            .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .search-box {
                width: 100%;
            }
            
            .actions {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn {
                width: 100%;
                text-align: center;
            }
            
            .product-cell {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .product-image {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include "sidebar.php"; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <h1>Product Management</h1>
                <div class="user-info">
                    <div class="user-avatar">A</div>
                    <div>
                        <p>Admin User</p>
                        <small>Administrator</small>
                    </div>
                </div>
            </div>

            <!-- Display Messages -->
            <?php if (isset($_GET['msg'])): ?>
                <?php 
                $msg = $_GET['msg'];
                $alert_class = '';
                $alert_text = '';
                
                if ($msg == 'added') {
                    $alert_class = 'alert-success';
                    $alert_text = 'Product added successfully!';
                } elseif ($msg == 'updated') {
                    $alert_class = 'alert-success';
                    $alert_text = 'Product updated successfully!';
                } elseif ($msg == 'deleted') {
                    $alert_class = 'alert-success';
                    $alert_text = 'Product deleted successfully!';
                } elseif ($msg == 'error') {
                    $alert_class = 'alert-error';
                    $alert_text = 'An error occurred!';
                }
                ?>
                <div class="alert <?php echo $alert_class; ?>">
                    <span><?php echo $alert_text; ?></span>
                    <button class="close-alert" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card card-products">
                    <div class="card-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="card-info">
                        <h3><?php echo $total_products; ?></h3>
                        <p>Total Products</p>
                    </div>
                </div>
                
                <div class="card card-value">
                    <div class="card-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="card-info">
                        <h3>$<?php echo number_format($stats['total_value'], 2); ?></h3>
                        <p>Total Value</p>
                    </div>
                </div>
                
                <div class="card card-sale">
                    <div class="card-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="card-info">
                        <h3><?php echo $stats['sale_items']; ?></h3>
                        <p>On Sale</p>
                    </div>
                </div>
                
                <div class="card card-new">
                    <div class="card-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="card-info">
                        <h3><?php echo $stats['new_items']; ?></h3>
                        <p>New Arrivals</p>
                    </div>
                </div>
            </div>

            <!-- Product Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3>All Products</h3>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-input" placeholder="Search products...">
                    </div>
                </div>
                
                <button class="btn btn-add" id="add-product-btn" onclick="openModal()">
                    <i class="fas fa-plus"></i> Add New Product
                </button>
                
                <table id="products-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Badge</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td>
                                        <div class="product-cell">
                                            <?php if ($row['image_url']): ?>
                                                <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['name']; ?>" class="product-image">
                                            <?php endif; ?>
                                            <div>
                                                <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        $description = htmlspecialchars($row['description']);
                                        echo strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description;
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td>
                                        <span class="price">$<?php echo number_format($row['price'], 2); ?></span>
                                        <?php if ($row['original_price']): ?>
                                            <span class="original-price">$<?php echo number_format($row['original_price'], 2); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['badge']): ?>
                                            <span class="badge badge-<?php echo strtolower($row['badge']); ?>">
                                                <?php echo $row['badge']; ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="?edit_id=<?php echo $row['id']; ?>" class="btn btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?delete_id=<?php echo $row['id']; ?>" 
                                               class="btn btn-delete" 
                                               onclick="return confirm('Are you sure you want to delete this product?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-box-open" style="font-size: 3rem; color: #ddd; margin-bottom: 10px;"></i>
                                    <p>No products found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal" id="product-modal" <?php echo $edit_product ? 'style="display: flex;"' : ''; ?>>
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title"><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?></h3>
                <button class="close-btn" id="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <form id="product-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $edit_product ? $edit_product['id'] : ''; ?>">
                <?php if ($edit_product && $edit_product['image_url']): ?>
                    <input type="hidden" name="existing_image" value="<?php echo $edit_product['image_url']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required 
                           value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="original_price">Original Price (for discount)</label>
                    <input type="number" id="original_price" name="original_price" step="0.01" min="0"
                           value="<?php echo $edit_product ? $edit_product['original_price'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Electronics" <?php echo ($edit_product && $edit_product['category'] == 'Electronics') ? 'selected' : ''; ?>>Electronics</option>
                        <option value="Wearables" <?php echo ($edit_product && $edit_product['category'] == 'Wearables') ? 'selected' : ''; ?>>Wearables</option>
                        <option value="Computers" <?php echo ($edit_product && $edit_product['category'] == 'Computers') ? 'selected' : ''; ?>>Computers</option>
                        <option value="Tablets" <?php echo ($edit_product && $edit_product['category'] == 'Tablets') ? 'selected' : ''; ?>>Tablets</option>
                        <option value="Accessories" <?php echo ($edit_product && $edit_product['category'] == 'Accessories') ? 'selected' : ''; ?>>Accessories</option>
                        <option value="Other" <?php echo ($edit_product && $edit_product['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="badge">Badge</label>
                    <select id="badge" name="badge">
                        <option value="">No Badge</option>
                        <option value="NEW" <?php echo ($edit_product && $edit_product['badge'] == 'NEW') ? 'selected' : ''; ?>>NEW</option>
                        <option value="SALE" <?php echo ($edit_product && $edit_product['badge'] == 'SALE') ? 'selected' : ''; ?>>SALE</option>
                        <option value="HOT" <?php echo ($edit_product && $edit_product['badge'] == 'HOT') ? 'selected' : ''; ?>>HOT</option>
                        <option value="BESTSELLER" <?php echo ($edit_product && $edit_product['badge'] == 'BESTSELLER') ? 'selected' : ''; ?>>BESTSELLER</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                    <?php if ($edit_product && $edit_product['image_url']): ?>
                        <img src="<?php echo $edit_product['image_url']; ?>" alt="Current Image" class="image-preview" id="image-preview" style="display: block;">
                    <?php else: ?>
                        <img src="" alt="Image Preview" class="image-preview" id="image-preview">
                    <?php endif; ?>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-save"><?php echo $edit_product ? 'Update' : 'Add'; ?> Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // DOM Elements
        const searchInput = document.getElementById('search-input');
        const productModal = document.getElementById('product-modal');
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');

        // Open modal
        function openModal() {
            productModal.style.display = 'flex';
            // Clear form if not editing
            <?php if (!$edit_product): ?>
                document.getElementById('product-form').reset();
                imagePreview.style.display = 'none';
                imagePreview.src = '';
            <?php endif; ?>
        }

        // Close modal
        function closeModal() {
            productModal.style.display = 'none';
            // Redirect to clear edit_id from URL
            <?php if ($edit_product): ?>
                window.location.href = 'products.php';
            <?php endif; ?>
        }

        // Preview image before upload
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#products-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === productModal) {
                closeModal();
            }
        });

        // Auto open modal if editing
        <?php if ($edit_product): ?>
            document.addEventListener('DOMContentLoaded', function() {
                openModal();
            });
        <?php endif; ?>
    </script>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>
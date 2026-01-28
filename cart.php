<?php
session_start();

// Check if database connection exists
if (!file_exists("admin/db_connection.php")) {
    die("Database connection file not found!");
}

include "admin/db_connection.php";

// Check if connection is successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Generate session ID for guest users
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}

$session_id = $_SESSION['session_id'];

// Handle quantity updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_quantity'])) {
        $cart_id = intval($_POST['cart_id']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity > 0) {
            $update_query = "UPDATE cart SET quantity = ? WHERE id = ? AND session_id = ?";
            $stmt = $conn->prepare($update_query);
            
            if ($stmt) {
                $stmt->bind_param("iis", $quantity, $cart_id, $session_id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Quantity updated to $quantity!";
                } else {
                    $_SESSION['error_message'] = "Failed to update cart: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $_SESSION['error_message'] = "SQL Error: " . $conn->error;
            }
        } else {
            // Remove item if quantity is 0
            $delete_query = "DELETE FROM cart WHERE id = ? AND session_id = ?";
            $stmt = $conn->prepare($delete_query);
            
            if ($stmt) {
                $stmt->bind_param("is", $cart_id, $session_id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Item removed from cart!";
                } else {
                    $_SESSION['error_message'] = "Failed to remove item: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $_SESSION['error_message'] = "SQL Error: " . $conn->error;
            }
        }
    } elseif (isset($_POST['remove_item'])) {
        $cart_id = intval($_POST['cart_id']);
        
        $delete_query = "DELETE FROM cart WHERE id = ? AND session_id = ?";
        $stmt = $conn->prepare($delete_query);
        
        if ($stmt) {
            $stmt->bind_param("is", $cart_id, $session_id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Item removed from cart!";
            } else {
                $_SESSION['error_message'] = "Failed to remove item: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "SQL Error: " . $conn->error;
        }
    } elseif (isset($_POST['clear_cart'])) {
        $clear_query = "DELETE FROM cart WHERE session_id = ?";
        $stmt = $conn->prepare($clear_query);
        
        if ($stmt) {
            $stmt->bind_param("s", $session_id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Cart cleared successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to clear cart: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "SQL Error: " . $conn->error;
        }
    }
    
    // Redirect to avoid form resubmission
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - ShopEase</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 70px;
        }
        
        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .cart-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .cart-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .cart-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .product-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .quantity-input {
            width: 70px;
            text-align: center;
        }
        
        .summary-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 100px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .total-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px 20px;
        }
        
        .empty-cart-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .btn-checkout {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: transform 0.3s ease;
        }
        
        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
        
        .btn-danger {
            border-radius: 25px;
            padding: 8px 20px;
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .db-error {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        
        /* Notification Styles */
        .notification-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        }
        
        .notification {
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            color: white;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: slideIn 0.3s ease;
            transition: all 0.3s ease;
        }
        
        .notification-success {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            border-left: 4px solid #388E3C;
        }
        
        .notification-error {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
            border-left: 4px solid #c62828;
        }
        
        .notification-info {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            border-left: 4px solid #0D47A1;
        }
        
        .notification-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            margin-left: 10px;
            opacity: 0.8;
        }
        
        .notification-close:hover {
            opacity: 1;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        /* Cart count badge in navbar */
        .cart-count {
            font-size: 0.7rem;
            padding: 3px 8px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <?php include "include/nav.php"; ?>
    
    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer">
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="notification notification-success" id="autoNotification">
            <span><?php echo htmlspecialchars($_SESSION['success_message']); ?></span>
            <button class="notification-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="notification notification-error" id="autoNotification">
            <span><?php echo htmlspecialchars($_SESSION['error_message']); ?></span>
            <button class="notification-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
    </div>
    
    <div class="cart-container">
        <!-- Header -->
        <div class="cart-header">
            <h1><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h1>
            <p class="lead">Review your items and proceed to checkout</p>
        </div>
        
        <?php
        // Check if cart table exists
        $check_table = $conn->query("SHOW TABLES LIKE 'cart'");
        if (!$check_table || $check_table->num_rows == 0): ?>
            <div class="db-error">
                <h3><i class="fas fa-exclamation-triangle"></i> Cart System Not Ready</h3>
                <p>The cart table doesn't exist in the database.</p>
                <p>Please run the SQL setup script to create the cart table.</p>
                <a href="setup_cart.php" class="btn btn-primary mt-3">Setup Cart System</a>
            </div>
        <?php else: ?>
        
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <?php
                // Fetch cart items
                $cart_query = "
                    SELECT cart.*, products.name, products.description, products.price, 
                           products.original_price, products.image_url, cart.quantity
                    FROM cart 
                    JOIN products ON cart.product_id = products.id 
                    WHERE cart.session_id = ?
                    ORDER BY cart.added_at DESC";
                
                $stmt = $conn->prepare($cart_query);
                
                if ($stmt) {
                    $stmt->bind_param("s", $session_id);
                    $stmt->execute();
                    $cart_result = $stmt->get_result();
                    
                    if ($cart_result->num_rows > 0) {
                        while ($item = $cart_result->fetch_assoc()) {
                            $product_price = !empty($item['original_price']) && $item['original_price'] > 0 
                                ? $item['original_price'] 
                                : $item['price'];
                            
                            $item_total = $product_price * $item['quantity'];
                            ?>
                            
                            <div class="cart-item">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                             class="product-image">
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                                        <p class="text-muted mb-2" style="font-size: 0.9rem;">
                                            <?php echo substr(htmlspecialchars($item['description']), 0, 100); ?>...
                                        </p>
                                        <p class="mb-0">
                                            <strong>Price:</strong> 
                                            $<?php echo number_format($product_price, 2); ?>
                                            <?php if (!empty($item['original_price']) && $item['original_price'] > 0): ?>
                                                <span class="text-muted text-decoration-line-through ms-2">
                                                    $<?php echo number_format($item['price'], 2); ?>
                                                </span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <form method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                            <div class="input-group" style="width: 150px;">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">-</button>
                                                <input type="number" name="quantity" 
                                                       value="<?php echo $item['quantity']; ?>" 
                                                       min="1" max="100"
                                                       class="form-control text-center quantity-input"
                                                       id="quantity-<?php echo $item['id']; ?>"
                                                       onchange="this.form.submit()">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">+</button>
                                            </div>
                                            <button type="submit" name="update_quantity" 
                                                    class="btn btn-link text-primary ms-2" title="Update">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        </form>
                                        <small class="text-muted">Max: 100 items</small>
                                    </div>
                                    
                                    <div class="col-md-3 text-end">
                                        <h5 class="text-primary">$<?php echo number_format($item_total, 2); ?></h5>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" name="remove_item" 
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Remove this item from cart?')">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="empty-cart">
                            <div class="empty-cart-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3>Your cart is empty</h3>
                            <p class="text-muted">Looks like you haven't added any items to your cart yet.</p>
                            <a href="index.php" class="btn btn-primary btn-lg mt-3">
                                <i class="fas fa-shopping-bag"></i> Continue Shopping
                            </a>
                        </div>
                        <?php
                    }
                    
                    $stmt->close();
                } else {
                    echo '<div class="alert alert-danger">SQL Error: ' . $conn->error . '</div>';
                }
                ?>
                
                <!-- Clear Cart Button -->
                <?php 
                if (isset($cart_result) && $cart_result->num_rows > 0): ?>
                    <div class="text-end mb-4">
                        <form method="POST" class="d-inline">
                            <button type="submit" name="clear_cart" 
                                    class="btn btn-outline-danger"
                                    onclick="return confirm('Are you sure you want to clear your entire cart?')">
                                <i class="fas fa-trash-alt"></i> Clear Cart
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Order Summary -->
            <?php if (isset($cart_result) && $cart_result->num_rows > 0): ?>
                <div class="col-lg-4">
                    <div class="summary-card">
                        <h3 class="mb-4">Order Summary</h3>
                        
                        <?php
                        $summary_query = "
                            SELECT cart.quantity, products.price, products.original_price
                            FROM cart 
                            JOIN products ON cart.product_id = products.id 
                            WHERE cart.session_id = ?";
                        
                        $stmt2 = $conn->prepare($summary_query);
                        
                        if ($stmt2) {
                            $stmt2->bind_param("s", $session_id);
                            $stmt2->execute();
                            $summary_result = $stmt2->get_result();
                            
                            $subtotal = 0;
                            $total_items = 0;
                            
                            while ($item = $summary_result->fetch_assoc()) {
                                $product_price = !empty($item['original_price']) && $item['original_price'] > 0 
                                    ? $item['original_price'] 
                                    : $item['price'];
                                
                                $subtotal += $product_price * $item['quantity'];
                                $total_items += $item['quantity'];
                            }
                            
                            $stmt2->close();
                            
                            // Calculate shipping (free over $100)
                            $shipping = $subtotal > 100 ? 0 : 10.00;
                            $tax = $subtotal * 0.08; // 8% tax rate
                            $total = $subtotal + $shipping + $tax;
                            ?>
                            
                            <div class="summary-item">
                                <span>Items (<?php echo $total_items; ?>)</span>
                                <span>$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            
                            <div class="summary-item">
                                <span>Shipping</span>
                                <span>
                                    <?php if ($shipping == 0): ?>
                                        <span class="text-success">FREE</span>
                                    <?php else: ?>
                                        $<?php echo number_format($shipping, 2); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            
                            <div class="summary-item">
                                <span>Estimated Tax</span>
                                <span>$<?php echo number_format($tax, 2); ?></span>
                            </div>
                            
                            <hr>
                            
                            <div class="summary-item total-price">
                                <span>Total</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                            
                            <div class="mt-4">
                                <button class="btn btn-checkout w-100" onclick="showNotification('Checkout feature coming soon!', 'info')">
                                    <i class="fas fa-lock"></i> Proceed to Checkout
                                </button>
                            </div>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt"></i> Secure checkout • SSL encrypted
                                </small>
                            </div>
                            
                            <div class="mt-4">
                                <h5>Continue Shopping</h5>
                                <div class="d-grid gap-2">
                                    <a href="index.php" class="btn btn-outline-primary">
                                        <i class="fas fa-home"></i> Homepage
                                    </a>
                                    <a href="products.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-store"></i> All Products
                                    </a>
                                </div>
                            </div>
                            <?php
                        } else {
                            echo '<div class="alert alert-danger">Failed to calculate order summary.</div>';
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

<?php include "include/footer.php"; ?>    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Function to update quantity with +/- buttons
        function updateQuantity(cartId, change) {
            const input = document.getElementById('quantity-' + cartId);
            let newValue = parseInt(input.value) + change;
            
            // Ensure quantity doesn't go below 1 or above max
            const max = parseInt(input.max) || 100;
            if (newValue < 1) newValue = 1;
            if (newValue > max) newValue = max;
            
            input.value = newValue;
            
            // Auto-submit if value changed
            if (newValue !== parseInt(input.defaultValue)) {
                showNotification(`Changing quantity to ${newValue}...`, 'info');
                input.form.submit();
            }
        }
        
        // Function to show notification
        function showNotification(message, type = 'info') {
            const container = document.getElementById('notificationContainer');
            if (!container) return;
            
            // Remove existing auto notifications
            const autoNotif = document.getElementById('autoNotification');
            if (autoNotif) {
                autoNotif.remove();
            }
            
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <span>${message}</span>
                <button class="notification-close" onclick="this.parentElement.remove()">&times;</button>
            `;
            
            container.appendChild(notification);
            
            // Auto remove after 3 seconds for info, 5 for success
            const duration = type === 'success' ? 5000 : 3000;
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, duration);
        }
        
        // Add smooth animations
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-remove notifications after 5 seconds
            const autoNotification = document.getElementById('autoNotification');
            if (autoNotification) {
                setTimeout(() => {
                    autoNotification.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => {
                        if (autoNotification.parentNode) {
                            autoNotification.remove();
                        }
                    }, 300);
                }, 5000);
            }
            
            // Animate cart items on load
            const cartItems = document.querySelectorAll('.cart-item');
            cartItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    item.style.transition = 'all 0.5s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Show cart count notification
            const itemCount = document.querySelectorAll('.cart-item').length;
            if (itemCount > 0 && !autoNotification) {
                showNotification(`You have ${itemCount} item(s) in your cart`, 'info');
            }
            
            // Demo notifications (1, 2, 3)
            setTimeout(() => {
                showNotification('🎉 Welcome to your shopping cart!', 'info');
            }, 1000);
            
            setTimeout(() => {
                showNotification('➕ Click + or - to change quantities', 'info');
            }, 3000);
            
            setTimeout(() => {
                showNotification('💾 Changes auto-save when you click update', 'info');
            }, 5000);
        });
    </script>
</body>
</html>
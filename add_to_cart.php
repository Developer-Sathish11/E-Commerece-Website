<?php
session_start();

// Include database connection
include "admin/db_connection.php";

// Generate session ID for guest users
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}

// Check if product ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $session_id = $_SESSION['session_id'];
    
    // Check if product exists
    $product_query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($product_query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();
    
    if ($product_result->num_rows > 0) {
        // Check if product is already in cart
        $check_query = "SELECT * FROM cart WHERE product_id = ? AND session_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("is", $product_id, $session_id);
        $check_stmt->execute();
        $cart_result = $check_stmt->get_result();
        
        if ($cart_result->num_rows > 0) {
            // Update quantity if already in cart
            $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE product_id = ? AND session_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("is", $product_id, $session_id);
            $update_stmt->execute();
            $_SESSION['success'] = "Quantity updated in cart!";
        } else {
            // Add new item to cart
            $insert_query = "INSERT INTO cart (session_id, product_id, quantity, added_at) VALUES (?, ?, 1, NOW())";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("si", $session_id, $product_id);
            $insert_stmt->execute();
            $_SESSION['success'] = "Product added to cart!";
        }
        
        $check_stmt->close();
    } else {
        $_SESSION['error'] = "Product not found!";
    }
    
    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid product!";
}

// Redirect back
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit();
?>
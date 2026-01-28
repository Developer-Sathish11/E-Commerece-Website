<?php
// Include database connection
require_once 'db_connection.php';

// Handle order status update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        $success_msg = "Order status updated successfully!";
    } else {
        $error_msg = "Error updating order status: " . $stmt->error;
    }
    $stmt->close();
    
    header("Location: orders.php?msg=updated");
    exit();
}

// Handle order deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $success_msg = "Order deleted successfully!";
    } else {
        $error_msg = "Error deleting order: " . $stmt->error;
    }
    $stmt->close();
    
    header("Location: orders.php?msg=deleted");
    exit();
}

// Fetch all orders with item count
$sql = "SELECT o.*, 
        (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
        FROM orders o 
        ORDER BY created_at DESC";
$result = $conn->query($sql);

// Get statistics
$total_orders = $result->num_rows;
$sql_stats = "SELECT 
    COUNT(*) as total,
    SUM(total_amount) as total_revenue,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
    COUNT(CASE WHEN status = 'processing' THEN 1 END) as processing_orders,
    COUNT(CASE WHEN status = 'shipped' THEN 1 END) as shipped_orders,
    COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_orders,
    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders
    FROM orders";
$stats_result = $conn->query($sql_stats);
$stats = $stats_result->fetch_assoc();

// Get recent orders (last 5)
$sql_recent = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5";
$recent_result = $conn->query($sql_recent);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopNow Admin - Order Management</title>
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

        /* Sidebar Styles */
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

        .card-orders .card-icon {
            background-color: #e3f2fd;
            color: #2196f3;
        }

        .card-revenue .card-icon {
            background-color: #e8f5e9;
            color: #4caf50;
        }

        .card-pending .card-icon {
            background-color: #fff3e0;
            color: #ff9800;
        }

        .card-processing .card-icon {
            background-color: #e1f5fe;
            color: #03a9f4;
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-shipped {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
            margin-bottom: 30px;
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

        .customer-cell {
            display: flex;
            flex-direction: column;
        }

        .customer-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .customer-email {
            font-size: 0.9rem;
            color: #7f8c8d;
        }

        .amount {
            font-weight: 600;
            color: #2c3e50;
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

        .btn-view {
            background-color: #e3f2fd;
            color: #2196f3;
        }

        .btn-view:hover {
            background-color: #2196f3;
            color: white;
        }

        .btn-edit {
            background-color: #fff3cd;
            color: #ff9800;
        }

        .btn-edit:hover {
            background-color: #ff9800;
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

        /* Status Select */
        .status-select {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: white;
            font-size: 0.9rem;
            cursor: pointer;
            min-width: 120px;
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
            max-width: 800px;
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

        .order-details {
            margin-bottom: 30px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }

        .detail-label {
            font-weight: 600;
            color: #2c3e50;
            width: 150px;
        }

        .detail-value {
            flex: 1;
            color: #555;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .items-table th {
            background-color: #f8f9fa;
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

        /* Charts Container */
        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-box {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .chart-box h3 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .chart-placeholder {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 8px;
            color: #7f8c8d;
            font-size: 1.2rem;
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
            
            .charts-container {
                grid-template-columns: 1fr;
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
            
            .detail-row {
                flex-direction: column;
                gap: 5px;
            }
            
            .detail-label {
                width: 100%;
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
                <h1>Order Management</h1>
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
                $alert_class = 'alert-success';
                $alert_text = '';
                
                if ($msg == 'updated') {
                    $alert_text = 'Order status updated successfully!';
                } elseif ($msg == 'deleted') {
                    $alert_text = 'Order deleted successfully!';
                }
                ?>
                <div class="alert <?php echo $alert_class; ?>">
                    <span><?php echo $alert_text; ?></span>
                    <button class="close-alert" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card card-orders">
                    <div class="card-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="card-info">
                        <h3><?php echo $total_orders; ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                
                <div class="card card-revenue">
                    <div class="card-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="card-info">
                        <h3>$<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                
                <div class="card card-pending">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-info">
                        <h3><?php echo $stats['pending_orders']; ?></h3>
                        <p>Pending Orders</p>
                    </div>
                </div>
                
                <div class="card card-processing">
                    <div class="card-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="card-info">
                        <h3><?php echo $stats['processing_orders']; ?></h3>
                        <p>Processing</p>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-container">
                <div class="chart-box">
                    <h3>Orders by Status</h3>
                    <div class="chart-placeholder">
                        <i class="fas fa-chart-pie" style="font-size: 3rem; margin-right: 10px;"></i>
                        <span>Status Distribution Chart</span>
                    </div>
                </div>
                
                <div class="chart-box">
                    <h3>Revenue Trend</h3>
                    <div class="chart-placeholder">
                        <i class="fas fa-chart-line" style="font-size: 3rem; margin-right: 10px;"></i>
                        <span>Revenue Chart</span>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3>All Orders</h3>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-input" placeholder="Search orders...">
                    </div>
                </div>
                
                <table id="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['order_number']; ?></td>
                                    <td>
                                        <div class="customer-cell">
                                            <span class="customer-name"><?php echo htmlspecialchars($row['customer_name']); ?></span>
                                            <span class="customer-email"><?php echo htmlspecialchars($row['customer_email']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td><?php echo $row['item_count']; ?> items</td>
                                    <td class="amount">$<?php echo number_format($row['total_amount'], 2); ?></td>
                                    <td>
                                        <form method="POST" class="status-form" style="display: inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $row['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $row['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $row['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $row['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td>
                                        <?php if ($row['payment_status'] == 'paid'): ?>
                                            <span class="status-badge status-delivered">Paid</span>
                                        <?php elseif ($row['payment_status'] == 'pending'): ?>
                                            <span class="status-badge status-pending">Pending</span>
                                        <?php else: ?>
                                            <span class="status-badge status-cancelled">Failed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="btn btn-view" onclick="viewOrder(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <a href="?delete_id=<?php echo $row['id']; ?>" 
                                               class="btn btn-delete" 
                                               onclick="return confirm('Are you sure you want to delete this order?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ddd; margin-bottom: 10px;"></i>
                                    <p>No orders found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Recent Orders -->
            <div class="table-container">
                <div class="table-header">
                    <h3>Recent Orders</h3>
                </div>
                
                <table id="recent-orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_result->num_rows > 0): ?>
                            <?php while($row = $recent_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['order_number']; ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td class="amount">$<?php echo number_format($row['total_amount'], 2); ?></td>
                                    <td>
                                        <?php 
                                        $status_class = 'status-' . $row['status'];
                                        ?>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 20px;">
                                    <i class="fas fa-shopping-cart" style="font-size: 2rem; color: #ddd; margin-bottom: 10px;"></i>
                                    <p>No recent orders</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal" id="order-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Order Details</h3>
                <button class="close-btn" id="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <div id="order-details-content">
                <!-- Order details will be loaded here via AJAX -->
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #3498db;"></i>
                    <p>Loading order details...</p>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const searchInput = document.getElementById('search-input');
        const orderModal = document.getElementById('order-modal');
        const orderDetailsContent = document.getElementById('order-details-content');

        // Open modal
        function openModal() {
            orderModal.style.display = 'flex';
        }

        // Close modal
        function closeModal() {
            orderModal.style.display = 'none';
        }

        // View order details
        function viewOrder(orderId) {
            openModal();
            
            // Load order details via AJAX
            fetch(`get_order_details.php?id=${orderId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    orderDetailsContent.innerHTML = html;
                })
                .catch(error => {
                    orderDetailsContent.innerHTML = `
                        <div style="text-align: center; padding: 40px;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #e74c3c; margin-bottom: 10px;"></i>
                            <p>Error loading order details. Please try again.</p>
                        </div>
                    `;
                    console.error('Error:', error);
                });
        }

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#orders-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === orderModal) {
                closeModal();
            }
        });

        // Auto-submit status forms when changed
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>
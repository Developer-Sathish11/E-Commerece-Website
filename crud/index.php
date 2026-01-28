<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projects";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Delete via AJAX
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Person deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting person']);
    }
    $stmt->close();
    exit();
}

// Fetch all persons
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persons List - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .header {
            background: #fff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        h1 {
            color: #333;
            font-weight: 600;
            margin: 0;
        }
        
        .btn-add {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .btn-add:hover {
            background: #218838;
            color: white;
        }
        
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .table {
            margin: 0;
        }
        
        .table thead {
            background: #f8f9fa;
        }
        
        .table thead th {
            border-bottom: 2px solid #dee2e6;
            padding: 15px;
            font-weight: 600;
            color: #495057;
        }
        
        .table tbody tr {
            border-bottom: 1px solid #f1f1f1;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .badge-id {
            background: #e9ecef;
            color: #495057;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .action-buttons .btn {
            padding: 6px 12px;
            font-size: 0.85rem;
            margin-right: 5px;
        }
        
        .btn-edit {
            background: #ffc107;
            color: #212529;
            border: none;
        }
        
        .btn-edit:hover {
            background: #e0a800;
            color: #212529;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
        }
        
        .btn-delete:hover {
            background: #c82333;
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .email-link {
            color: #0d6efd;
            text-decoration: none;
        }
        
        .email-link:hover {
            text-decoration: underline;
        }
        
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Alert Messages -->
        <div id="alertContainer"></div>
        
        <!-- Header -->
        <div class="header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-users me-2"></i>Persons Management</h1>
                    <p class="text-muted mb-0">Manage registered persons list</p>
                </div>
                <div>
                    <a href="add.php" class="btn btn-add">
                        <i class="fas fa-user-plus me-2"></i> Add New Person
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Persons Table -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th width="80">ID</th>
                        <th>Name</th>
                        <th width="100">Age</th>
                        <th width="150">Contact</th>
                        <th>Email</th>
                        <th width="150">Place</th>
                        <th width="150" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="personsTableBody">
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr id="personRow<?php echo $row['id']; ?>">
                            <td>
                                <span class="badge-id"><?php echo $row['id']; ?></span>
                            </td>
                            <td id="name<?php echo $row['id']; ?>">
                                <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                            </td>
                            <td id="age<?php echo $row['id']; ?>">
                                <?php echo $row['age'] ? $row['age'] : 'N/A'; ?>
                            </td>
                            <td id="contact<?php echo $row['id']; ?>">
                                <?php echo $row['contact'] ? $row['contact'] : 'N/A'; ?>
                            </td>
                            <td id="email<?php echo $row['id']; ?>">
                                <?php if($row['email']): ?>
                                    <a href="mailto:<?php echo $row['email']; ?>" class="email-link">
                                        <?php echo $row['email']; ?>
                                    </a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td id="place<?php echo $row['id']; ?>">
                                <?php echo $row['place'] ? $row['place'] : 'N/A'; ?>
                            </td>
                            <td class="text-center action-buttons">
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-edit btn-sm">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <button onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>')" 
                                        class="btn btn-delete btn-sm">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr id="emptyRow">
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash fa-3x mb-3"></i>
                                    <h4>No Persons Found</h4>
                                    <p>There are no persons registered in the database yet.</p>
                                    <a href="add.php" class="btn btn-add">
                                        <i class="fas fa-user-plus me-2"></i> Add First Person
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Info Footer -->
        <div class="mt-4 text-center text-muted">
            <p>
                <i class="fas fa-database me-1"></i> 
                Total Persons: <strong id="totalPersons"><?php echo $result->num_rows; ?></strong> | 
                Table: <strong>users</strong> | 
                Last Updated: <?php echo date('M d, Y h:i A'); ?>
            </p>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteName"></strong>?</p>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let deleteId = null;
        
        // Show alert message
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
        
        // Delete Person
        function confirmDelete(id, name) {
            deleteId = id;
            document.getElementById('deleteName').textContent = name;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
        
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', deleteId);
            
            fetch('persons.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();
                    
                    // Show success message
                    showAlert(data.message, 'success');
                    
                    // Remove row from table
                    removePersonFromTable(deleteId);
                    deleteId = null;
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Error: ' + error, 'danger');
            });
        });
        
        // Helper functions
        function removePersonFromTable(id) {
            const row = document.getElementById(`personRow${id}`);
            if (row) {
                row.remove();
                
                // Check if table is empty
                const tableBody = document.getElementById('personsTableBody');
                if (tableBody.children.length === 0) {
                    // Add empty state
                    tableBody.innerHTML = `
                        <tr id="emptyRow">
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash fa-3x mb-3"></i>
                                    <h4>No Persons Found</h4>
                                    <p>There are no persons registered in the database yet.</p>
                                    <a href="add.php" class="btn btn-add">
                                        <i class="fas fa-user-plus me-2"></i> Add First Person
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                }
                
                // Update total count
                updateTotalCount();
            }
        }
        
        function updateTotalCount() {
            const tableBody = document.getElementById('personsTableBody');
            const emptyRow = document.getElementById('emptyRow');
            const count = emptyRow ? 0 : tableBody.children.length;
            document.getElementById('totalPersons').textContent = count;
        }
        
        // Check for URL parameters for success messages
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('message')) {
            const message = urlParams.get('message');
            const type = urlParams.get('type') || 'success';
            showAlert(decodeURIComponent(message), type);
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
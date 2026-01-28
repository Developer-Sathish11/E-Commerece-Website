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

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: persons.php?message=" . urlencode("Invalid person ID!") . "&type=danger");
    exit();
}

$id = intval($_GET['id']);

// Fetch person data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: persons.php?message=" . urlencode("Person not found!") . "&type=danger");
    exit();
}

$person = $result->fetch_assoc();
$stmt->close();

// Handle form submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $age = isset($_POST['age']) ? intval($_POST['age']) : null;
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $place = trim($_POST['place']);
    
    // Validation
    if (empty($name)) {
        $error = "Name is required!";
    } else {
        $sql = "UPDATE users SET name=?, age=?, contact=?, email=?, place=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisssi", $name, $age, $contact, $email, $place, $id);
        
        if ($stmt->execute()) {
            $success = true;
            // Update local data
            $person['name'] = $name;
            $person['age'] = $age;
            $person['contact'] = $contact;
            $person['email'] = $email;
            $person['place'] = $place;
            
            // Redirect to persons list with success message
            header("Location: persons.php?message=" . urlencode("Person updated successfully!") . "&type=success");
            exit();
        } else {
            $error = "Error updating person: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Person - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 800px;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: #ffc107;
            color: #212529;
            border-radius: 10px 10px 0 0 !important;
            padding: 20px 30px;
        }
        
        .card-title {
            margin: 0;
            font-weight: 600;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }
        
        .btn-submit {
            background: #ffc107;
            color: #212529;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .btn-submit:hover {
            background: #e0a800;
            color: #212529;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            color: white;
        }
        
        .required {
            color: #dc3545;
        }
        
        .alert {
            border-radius: 5px;
        }
        
        .back-link {
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: #495057;
            text-decoration: underline;
        }
        
        .person-id {
            background: #e9ecef;
            color: #495057;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <!-- Error Alert -->
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-edit me-2"></i>Edit Person
                            </h3>
                            <a href="persons.php" class="back-link">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Person ID Display -->
                        <div class="mb-4">
                            <span class="person-id">ID: <?php echo $person['id']; ?></span>
                            <small class="text-muted ms-3">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Last updated: <?php echo date('M d, Y', strtotime($person['updated_at'] ?? 'now')); ?>
                            </small>
                        </div>
                        
                        <form method="POST" action="edit.php?id=<?php echo $person['id']; ?>">
                            <div class="row">
                                <!-- Name -->
                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Full Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="name" required
                                           placeholder="Enter full name" 
                                           value="<?php echo htmlspecialchars($person['name']); ?>">
                                    <div class="form-text">Enter the person's full name</div>
                                </div>
                                
                                <!-- Age -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Age</label>
                                    <input type="number" class="form-control" name="age" 
                                           min="1" max="150" placeholder="Enter age"
                                           value="<?php echo $person['age'] ? htmlspecialchars($person['age']) : ''; ?>">
                                    <div class="form-text">Age in years</div>
                                </div>
                                
                                <!-- Contact -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" name="contact" 
                                           placeholder="Enter contact number"
                                           value="<?php echo $person['contact'] ? htmlspecialchars($person['contact']) : ''; ?>">
                                    <div class="form-text">Phone or mobile number</div>
                                </div>
                                
                                <!-- Email -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" 
                                           placeholder="Enter email address"
                                           value="<?php echo $person['email'] ? htmlspecialchars($person['email']) : ''; ?>">
                                    <div class="form-text">Valid email address</div>
                                </div>
                                
                                <!-- Place -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Place</label>
                                    <input type="text" class="form-control" name="place" 
                                           placeholder="Enter place or city"
                                           value="<?php echo $person['place'] ? htmlspecialchars($person['place']) : ''; ?>">
                                    <div class="form-text">City or place of residence</div>
                                </div>
                            </div>
                            
                            <!-- Form Buttons -->
                            <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                                <div>
                                    <a href="persons.php" class="btn btn-cancel">
                                        <i class="fas fa-times me-2"></i> Cancel
                                    </a>
                                    <button type="button" class="btn btn-danger ms-2" onclick="confirmDelete()">
                                        <i class="fas fa-trash me-2"></i> Delete
                                    </button>
                                </div>
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-save me-2"></i> Update Person
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Info Footer -->
                <div class="mt-4 text-center text-muted">
                    <p>
                        <i class="fas fa-info-circle me-1"></i>
                        All fields marked with <span class="required">*</span> are required.
                        Click "Update Person" to save changes.
                    </p>
                </div>
            </div>
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
                   
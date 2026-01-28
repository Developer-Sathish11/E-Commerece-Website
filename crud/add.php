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
        $sql = "INSERT INTO users (name, age, contact, email, place) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisss", $name, $age, $contact, $email, $place);
        
        if ($stmt->execute()) {
            $success = true;
            // Redirect to persons list with success message
            header("Location: persons.php?message=" . urlencode("Person added successfully!") . "&type=success");
            exit();
        } else {
            $error = "Error adding person: " . $stmt->error;
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
    <title>Add Person - Admin</title>
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
            background: #28a745;
            color: white;
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
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .btn-submit {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .btn-submit:hover {
            background: #218838;
            color: white;
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
            color: #28a745;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: #218838;
            text-decoration: underline;
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
                                <i class="fas fa-user-plus me-2"></i>Add New Person
                            </h3>
                            <a href="add.php" class="back-link">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" action="add.php">
                            <div class="row">
                                <!-- Name -->
                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Full Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="name" required
                                           placeholder="Enter full name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                                    <div class="form-text">Enter the person's full name</div>
                                </div>
                                
                                <!-- Age -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Age</label>
                                    <input type="number" class="form-control" name="age" 
                                           min="1" max="150" placeholder="Enter age"
                                           value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>">
                                    <div class="form-text">Age in years</div>
                                </div>
                                
                                <!-- Contact -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" name="contact" 
                                           placeholder="Enter contact number"
                                           value="<?php echo isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>">
                                    <div class="form-text">Phone or mobile number</div>
                                </div>
                                
                                <!-- Email -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" 
                                           placeholder="Enter email address"
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    <div class="form-text">Valid email address</div>
                                </div>
                                
                                <!-- Place -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Place</label>
                                    <input type="text" class="form-control" name="place" 
                                           placeholder="Enter place or city"
                                           value="<?php echo isset($_POST['place']) ? htmlspecialchars($_POST['place']) : ''; ?>">
                                    <div class="form-text">City or place of residence</div>
                                </div>
                            </div>
                            
                            <!-- Form Buttons -->
                            <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                                <a href="persons.php" class="btn btn-cancel">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-save me-2"></i> Save Person
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
                        Form data will be saved to the <strong>users</strong> table.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.querySelector('input[name="name"]').value.trim();
            
            if (!name) {
                e.preventDefault();
                alert('Please enter a name for the person.');
                document.querySelector('input[name="name"]').focus();
                return false;
            }
            
            // Validate email if provided
            const email = document.querySelector('input[name="email"]').value.trim();
            if (email && !validateEmail(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                document.querySelector('input[name="email"]').focus();
                return false;
            }
            
            return true;
        });
        
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        // Auto-format contact number
        document.querySelector('input[name="contact"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header h2 {
            color: #333;
            font-weight: 600;
        }
        
        .register-header p {
            color: #666;
        }
        
        .form-label {
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            transition: transform 0.3s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .alert {
            border-radius: 8px;
            padding: 12px 15px;
        }
        
        .error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2>Create Your Account</h2>
            <p>Join our community today</p>
        </div>

        <?php
        // Database configuration
        $servername = "localhost";
        $username = "root"; // Change if needed
        $password = ""; // Change if needed
        $dbname = "projects"; // Change to your database name

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Initialize variables
        $name = $age = $contact = $email = $place = "";
        $errors = [];
        $success = false;

        // Process form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Validate and sanitize inputs
            $name = trim($_POST['name'] ?? '');
            $age = trim($_POST['age'] ?? '');
            $contact = trim($_POST['contact'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $place = trim($_POST['place'] ?? '');
            
            // Validation
            if (empty($name)) {
                $errors['name'] = "Name is required";
            } elseif (!preg_match("/^[a-zA-Z ]*$/", $name)) {
                $errors['name'] = "Only letters and spaces allowed";
            }

            if (empty($age)) {
                $errors['age'] = "Age is required";
            } elseif (!is_numeric($age) || $age < 1 || $age > 150) {
                $errors['age'] = "Please enter a valid age (1-150)";
            }

            if (empty($contact)) {
                $errors['contact'] = "Contact number is required";
            } elseif (!preg_match("/^[0-9]{10}$/", $contact)) {
                $errors['contact'] = "Please enter a valid 10-digit contact number";
            }

            if (empty($email)) {
                $errors['email'] = "Email is required";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Please enter a valid email address";
            } else {
                // Check if email already exists
                $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $check_email->bind_param("s", $email);
                $check_email->execute();
                $check_email->store_result();
                if ($check_email->num_rows > 0) {
                    $errors['email'] = "This email is already registered";
                }
                $check_email->close();
            }

            if (empty($place)) {
                $errors['place'] = "Place is required";
            }

            // If no errors, insert into database
            if (empty($errors)) {
                // Create prepared statement
                $stmt = $conn->prepare("INSERT INTO users (name, age, contact, email, place, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("sisss", $name, $age, $contact, $email, $place);
                
                if ($stmt->execute()) {
                    $success = true;
                    // Clear form
                    $name = $age = $contact = $email = $place = "";
                } else {
                    $errors['database'] = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>Success!</strong> Registration completed successfully.
            </div>
        <?php endif; ?>

        <?php if (isset($errors['database'])): ?>
            <div class="alert alert-danger">
                <strong>Error!</strong> <?php echo htmlspecialchars($errors['database']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!-- Name Field -->
            <div class="mb-3">
                <label for="name" class="form-label">Full Name *</label>
                <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                       id="name" name="name" value="<?php echo htmlspecialchars($name); ?>"
                       placeholder="Enter your full name" required>
                <?php if (isset($errors['name'])): ?>
                    <div class="error"><?php echo htmlspecialchars($errors['name']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Age Field -->
            <div class="mb-3">
                <label for="age" class="form-label">Age *</label>
                <input type="number" class="form-control <?php echo isset($errors['age']) ? 'is-invalid' : ''; ?>" 
                       id="age" name="age" value="<?php echo htmlspecialchars($age); ?>"
                       placeholder="Enter your age" min="1" max="150" required>
                <?php if (isset($errors['age'])): ?>
                    <div class="error"><?php echo htmlspecialchars($errors['age']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Contact Field -->
            <div class="mb-3">
                <label for="contact" class="form-label">Contact Number *</label>
                <input type="tel" class="form-control <?php echo isset($errors['contact']) ? 'is-invalid' : ''; ?>" 
                       id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>"
                       placeholder="Enter 10-digit mobile number" pattern="[0-9]{10}" required>
                <?php if (isset($errors['contact'])): ?>
                    <div class="error"><?php echo htmlspecialchars($errors['contact']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Email Field -->
            <div class="mb-3">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                       id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"
                       placeholder="Enter your email" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="error"><?php echo htmlspecialchars($errors['email']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Place Field -->
            <div class="mb-3">
                <label for="place" class="form-label">Place/City *</label>
                <input type="text" class="form-control <?php echo isset($errors['place']) ? 'is-invalid' : ''; ?>" 
                       id="place" name="place" value="<?php echo htmlspecialchars($place); ?>"
                       placeholder="Enter your city" required>
                <?php if (isset($errors['place'])): ?>
                    <div class="error"><?php echo htmlspecialchars($errors['place']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-register">Register Now</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Client-side validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                let valid = true;
                
                // Name validation
                const name = document.getElementById('name');
                if (!/^[a-zA-Z ]+$/.test(name.value.trim())) {
                    showError(name, 'Only letters and spaces allowed');
                    valid = false;
                }
                
                // Age validation
                const age = document.getElementById('age');
                if (age.value < 1 || age.value > 150) {
                    showError(age, 'Please enter valid age (1-150)');
                    valid = false;
                }
                
                // Contact validation
                const contact = document.getElementById('contact');
                if (!/^[0-9]{10}$/.test(contact.value)) {
                    showError(contact, 'Please enter 10-digit number');
                    valid = false;
                }
                
                // Email validation
                const email = document.getElementById('email');
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                    showError(email, 'Please enter valid email');
                    valid = false;
                }
                
                if (!valid) {
                    e.preventDefault();
                }
            });
            
            function showError(input, message) {
                input.classList.add('is-invalid');
                
                // Remove existing error message
                const existingError = input.nextElementSibling;
                if (existingError && existingError.classList.contains('error')) {
                    existingError.remove();
                }
                
                // Add new error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error';
                errorDiv.textContent = message;
                input.parentNode.appendChild(errorDiv);
            }
            
            // Real-time validation
            const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="number"]');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    const error = this.nextElementSibling;
                    if (error && error.classList.contains('error')) {
                        error.remove();
                    }
                });
            });
        });
    </script>
</body>
</html> 
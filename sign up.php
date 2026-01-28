<?php
// Start session first
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'shopnow';

// Create connection
$connection = new mysqli($host, $username, $password, $database);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Set charset to UTF-8
$connection->set_charset("utf8mb4");

// Initialize variables
$errors = [];
$success = false;
$firstName = $lastName = $email = $phone = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if database connection exists
    if (!isset($connection) || $connection === null) {
        $errors['database'] = "Cannot connect to database. Please try again later.";
    } else {
        // Get form data
        $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
        $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        $newsletter = isset($_POST['newsletter']) ? 1 : 0;
        $terms = isset($_POST['terms']) ? true : false;
        
        // Validation
        if (empty($firstName)) {
            $errors['firstName'] = "First name is required";
        }
        
        if (empty($lastName)) {
            $errors['lastName'] = "Last name is required";
        }
        
        if (empty($email)) {
            $errors['email'] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Please enter a valid email address";
        } else {
            // Check if email already exists
            $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
            $stmt = $connection->prepare($checkEmailQuery);
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();
                
                if ($stmt->num_rows > 0) {
                    $errors['email'] = "This email is already registered";
                }
                $stmt->close();
            } else {
                $errors['database'] = "Database query error: " . $connection->error;
            }
        }
        
        if (!empty($phone) && !preg_match('/^[0-9+\-\s]+$/', $phone)) {
            $errors['phone'] = "Please enter a valid phone number";
        }
        
        if (empty($password)) {
            $errors['password'] = "Password is required";
        } elseif (strlen($password) < 8) {
            $errors['password'] = "Password must be at least 8 characters long";
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors['password'] = "Password must contain at least one uppercase letter";
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors['password'] = "Password must contain at least one lowercase letter";
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors['password'] = "Password must contain at least one number";
        }
        
        if (empty($confirmPassword)) {
            $errors['confirmPassword'] = "Please confirm your password";
        } elseif ($password !== $confirmPassword) {
            $errors['confirmPassword'] = "Passwords do not match";
        }
        
        if (!$terms) {
            $errors['terms'] = "You must agree to the terms and conditions";
        }
        
        // If no errors, insert into database
        if (empty($errors)) {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Check if the table has newsletter column
            $tableCheck = $connection->query("SHOW COLUMNS FROM users LIKE 'newsletter_subscription'");
            $hasNewsletterColumn = ($tableCheck && $tableCheck->num_rows > 0);
            
            if ($hasNewsletterColumn) {
                // Insert with newsletter column
                $query = "INSERT INTO users (first_name, last_name, email, phone, password, newsletter_subscription, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
                $stmt = $connection->prepare($query);
                if ($stmt) {
                    $stmt->bind_param("sssssi", $firstName, $lastName, $email, $phone, $hashedPassword, $newsletter);
                }
            } else {
                // Insert without newsletter column (add timestamps)
                $query = "INSERT INTO users (first_name, last_name, email, phone, password, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
                $stmt = $connection->prepare($query);
                if ($stmt) {
                    $stmt->bind_param("sssss", $firstName, $lastName, $email, $phone, $hashedPassword);
                }
            }
            
            if (isset($stmt) && $stmt->execute()) {
                $userId = $stmt->insert_id;
                
                // Store user data in session
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $firstName . ' ' . $lastName;
                $_SESSION['user_email'] = $email;
                $_SESSION['is_verified'] = false;
                $_SESSION['logged_in'] = true;
                
                $success = true;
                
                // Redirect to home page
                header("Location: index.php");
                exit();
            } else {
                if (isset($stmt)) {
                    $errors['database'] = "Registration failed. Error: " . $stmt->error;
                    error_log("Database Error: " . $stmt->error);
                } else {
                    $errors['database'] = "Registration failed. Could not prepare statement.";
                }
            }
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ShopEase</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/signup.css">
</head>
<body>

<?php include "include/nav.php"; ?>

<div class="container">
    <div class="auth-container d-flex flex-wrap">
        <!-- Left Column - Benefits & Carousel -->
        <div class="col-lg-6 auth-left">
            <div class="auth-header text-start">
                <h2>Join ShopEase Today!</h2>
                <p>Create your account to unlock exclusive benefits and start shopping smarter.</p>
            </div>
            
            <!-- Benefits List -->
            <div class="benefits-list">
                <h4>Why Join ShopEase?</h4>
                <ul>
                    <li>Exclusive member-only deals and discounts</li>
                    <li>Fast and secure checkout process</li>
                    <li>Track your orders in real-time</li>
                    <li>Personalized product recommendations</li>
                    <li>Earn loyalty points on every purchase</li>
                    <li>Priority customer support</li>
                    <li>Early access to sales and new arrivals</li>
                </ul>
            </div>
            
            <!-- Signup Page Carousel -->
            <div class="signup-carousel-container">
                <div id="signupCarousel" class="carousel slide signup-carousel" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#signupCarousel" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#signupCarousel" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#signupCarousel" data-bs-slide-to="2"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80" class="d-block w-100" alt="Secure Shopping">
                            <div class="carousel-caption">
                                <h5>Secure Shopping</h5>
                                <p>Your data is protected with advanced security</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="https://images.unsplash.com/photo-1556742044-3c52d6e88c62?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80" class="d-block w-100" alt="Fast Delivery">
                            <div class="carousel-caption">
                                <h5>Fast Delivery</h5>
                                <p>Get your orders delivered quickly</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="https://images.unsplash.com/photo-1556742031-c6961e8560b0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80" class="d-block w-100" alt="24/7 Support">
                            <div class="carousel-caption">
                                <h5>24/7 Support</h5>
                                <p>We're always here to help you</p>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#signupCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#signupCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
            
            <div class="mt-4">
                <p>Already have an account? <a href="login.php" class="text-white fw-bold"> Sign in here</a></p>
            </div>
        </div>
        
        <!-- Right Column - Signup Form -->
        <div class="col-lg-6 auth-right">
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>Create Account</h2>
                <p class="text-muted">Fill in your details to get started</p>
            </div>
            
            <!-- Database Connection Error -->
            <?php if (isset($errors['database']) && !isset($_POST['firstName'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Database Error:</strong> <?php echo $errors['database']; ?>
                    <br><small>Please check your database configuration or contact support.</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Success/Error Messages -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Account created successfully! You will be redirected shortly.
                </div>
            <?php elseif (isset($errors['database']) && isset($_POST['firstName'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $errors['database']; ?>
                </div>
            <?php endif; ?>
            
            <form id="signupForm" method="POST" action="" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName" class="form-label">First Name *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control <?php echo isset($errors['firstName']) ? 'is-invalid' : ''; ?>" 
                                   id="firstName" name="firstName" 
                                   value="<?php echo htmlspecialchars($firstName); ?>" 
                                   placeholder="Enter first name" required>
                            <?php if (isset($errors['firstName'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['firstName']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="lastName" class="form-label">Last Name *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control <?php echo isset($errors['lastName']) ? 'is-invalid' : ''; ?>" 
                                   id="lastName" name="lastName" 
                                   value="<?php echo htmlspecialchars($lastName); ?>" 
                                   placeholder="Enter last name" required>
                            <?php if (isset($errors['lastName'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['lastName']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                               id="email" name="email" 
                               value="<?php echo htmlspecialchars($email); ?>" 
                               placeholder="Enter your email" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['email']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                               id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($phone); ?>" 
                               placeholder="Enter your phone number">
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['phone']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                               id="password" name="password" placeholder="Create a password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['password']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                    <div class="password-requirements" id="passwordRequirements">
                        <div class="requirement unmet" id="reqLength">At least 8 characters</div>
                        <div class="requirement unmet" id="reqUppercase">At least one uppercase letter</div>
                        <div class="requirement unmet" id="reqLowercase">At least one lowercase letter</div>
                        <div class="requirement unmet" id="reqNumber">At least one number</div>
                        <div class="requirement unmet" id="reqSpecial">At least one special character</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control <?php echo isset($errors['confirmPassword']) ? 'is-invalid' : ''; ?>" 
                               id="confirmPassword" name="confirmPassword" 
                               placeholder="Confirm your password" required>
                        <?php if (isset($errors['confirmPassword'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['confirmPassword']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input <?php echo isset($errors['terms']) ? 'is-invalid' : ''; ?>" 
                               type="checkbox" id="terms" name="terms" <?php echo isset($_POST['terms']) ? 'checked' : ''; ?> required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="terms.php" class="text-primary">Terms of Service</a> and <a href="privacy.php" class="text-primary">Privacy Policy</a>
                        </label>
                        <?php if (isset($errors['terms'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['terms']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" checked>
                        <label class="form-check-label" for="newsletter">
                            Subscribe to our newsletter for exclusive deals and updates
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2 mb-4" <?php echo (isset($errors['database']) && !isset($_POST['firstName'])) ? 'disabled' : ''; ?>>
                    Create Account
                </button>
                
                <div class="divider">
                    <span>Or sign up with</span>
                </div>
                
                <div class="row g-2 mb-4">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-google">
                            <i class="fab fa-google me-2"></i> Google
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-facebook">
                            <i class="fab fa-facebook-f me-2"></i> Facebook
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "include/footer.php"; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Password strength checker
    const passwordInput = document.getElementById('password');
    const passwordStrengthBar = document.getElementById('passwordStrengthBar');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    
    // Password requirements elements
    const reqLength = document.getElementById('reqLength');
    const reqUppercase = document.getElementById('reqUppercase');
    const reqLowercase = document.getElementById('reqLowercase');
    const reqNumber = document.getElementById('reqNumber');
    const reqSpecial = document.getElementById('reqSpecial');
    
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    // Check password strength
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Check requirements
        const hasLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
        
        // Update requirement indicators
        updateRequirement(reqLength, hasLength);
        updateRequirement(reqUppercase, hasUppercase);
        updateRequirement(reqLowercase, hasLowercase);
        updateRequirement(reqNumber, hasNumber);
        updateRequirement(reqSpecial, hasSpecial);
        
        // Calculate strength score
        let strength = 0;
        if (hasLength) strength += 20;
        if (hasUppercase) strength += 20;
        if (hasLowercase) strength += 20;
        if (hasNumber) strength += 20;
        if (hasSpecial) strength += 20;
        
        // Update strength bar
        passwordStrengthBar.style.width = strength + '%';
        
        // Update bar color based on strength
        if (strength < 40) {
            passwordStrengthBar.style.backgroundColor = '#dc3545'; // Red
        } else if (strength < 80) {
            passwordStrengthBar.style.backgroundColor = '#ffc107'; // Yellow
        } else {
            passwordStrengthBar.style.backgroundColor = '#28a745'; // Green
        }
        
        // Check password confirmation
        checkPasswordMatch();
    });
    
    // Check password match
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword === '') {
            confirmPasswordInput.classList.remove('is-invalid', 'is-valid');
            return;
        }
        
        if (password !== confirmPassword) {
            confirmPasswordInput.classList.add('is-invalid');
            confirmPasswordInput.classList.remove('is-valid');
        } else {
            confirmPasswordInput.classList.remove('is-invalid');
            confirmPasswordInput.classList.add('is-valid');
        }
    }
    
    function updateRequirement(element, isMet) {
        if (isMet) {
            element.classList.remove('unmet');
            element.classList.add('met');
        } else {
            element.classList.remove('met');
            element.classList.add('unmet');
        }
    }
    
    // Form validation before submission
    document.getElementById('signupForm').addEventListener('submit', function(e) {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const terms = document.getElementById('terms').checked;
        
        // Check password strength
        const hasLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        
        if (!hasLength || !hasUppercase || !hasLowercase || !hasNumber) {
            e.preventDefault();
            alert('Password must be at least 8 characters long and contain uppercase, lowercase, and numbers.');
            return;
        }
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match.');
            return;
        }
        
        if (!terms) {
            e.preventDefault();
            alert('You must agree to the terms and conditions.');
            return;
        }
    });
    
    // Social login buttons
    document.querySelector('.btn-google').addEventListener('click', function() {
        alert('Google sign up would be implemented here in a real application.');
    });
    
    document.querySelector('.btn-facebook').addEventListener('click', function() {
        alert('Facebook sign up would be implemented here in a real application.');
    });
    
    // Initialize carousel
    document.addEventListener('DOMContentLoaded', function() {
        const signupCarousel = new bootstrap.Carousel(document.getElementById('signupCarousel'), {
            interval: 3000,
            ride: 'carousel'
        });
    });
</script>
</body>
</html>
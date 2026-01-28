<?php 
// Check if session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables
$errors = [];
$success = false;
$email = '';

// Include database connection with error handling
$conn = null;
$connection = null; // Keep for backward compatibility
try {
    include "admin/db_connection.php";
    
    // Check if $conn was created (not $connection)
    if (!isset($conn)) {
        throw new Exception("Database connection not established");
    }
    
    // Also set $connection for compatibility
    $connection = $conn;
} catch (Exception $e) {
    $errors['database'] = "Database connection error: " . $e->getMessage();
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn !== null) {
    // Get form data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['rememberMe']) ? true : false;
    
    // Validation
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    }
    
    if (empty($password)) {
        $errors['password'] = "Password is required";
    }
    
    // If no validation errors, check credentials
    if (empty($errors) && $conn !== null) {
        // Prepare SQL query to get user by email
        $query = "SELECT id, first_name, last_name, email, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($query); // Use $conn instead of $connection
        
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Password is correct, login successful
                    
                    // Store user data in session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['logged_in'] = true;
                    
                    // Set cookie for "Remember me" if checked
                    if ($rememberMe) {
                        $cookie_name = "remember_user";
                        $cookie_value = $user['id'] . ":" . hash('sha256', $user['password']);
                        setcookie($cookie_name, $cookie_value, time() + (30 * 24 * 60 * 60), "/"); // 30 days
                    }
                    
                    $success = true;
                    
                    // Use JavaScript redirect instead of header to avoid errors
                    echo '<script>
                        setTimeout(function() {
                            window.location.href = "index.php";
                        }, 2000);
                    </script>';
                } else {
                    $errors['password'] = "Invalid password";
                }
            } else {
                $errors['email'] = "No account found with this email";
            }
            
            $stmt->close();
        } else {
            $errors['database'] = "Database error. Please try again. Error: " . $conn->error;
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn === null) {
    $errors['database'] = "Cannot connect to database. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ShopEase</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
<?php include "include/nav.php"; ?>

    <!-- Login Section -->
    <div class="container">
        <div class="auth-container d-flex flex-wrap">
            <!-- Left Column - Benefits & Carousel -->
            <div class="col-lg-6 auth-left">
                <div class="auth-header text-start">
                    <h2>Welcome Back to ShopEase!</h2>
                    <p>Log in to access your account and continue your shopping journey.</p>
                </div>
                
                <!-- Benefits List -->
                <div class="benefits-list">
                    <h4>Member Benefits</h4>
                    <ul>
                        <li>Access your order history</li>
                        <li>Track current orders in real-time</li>
                        <li>Save items to your wishlist</li>
                        <li>Personalized product recommendations</li>
                        <li>Exclusive member-only discounts</li>
                        <li>Quick checkout with saved information</li>
                        <li>Manage your account preferences</li>
                    </ul>
                </div>
                
                <!-- Login Page Carousel -->
                <div class="login-carousel-container">
                    <div id="loginCarousel" class="carousel slide login-carousel" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#loginCarousel" data-bs-slide-to="0" class="active"></button>
                            <button type="button" data-bs-target="#loginCarousel" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#loginCarousel" data-bs-slide-to="2"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" class="d-block w-100" alt="Secure Shopping">
                                <div class="carousel-caption">
                                    <h5>Secure Account</h5>
                                    <p>Your account is protected with advanced security</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img src="https://images.unsplash.com/photo-1556742044-3c52d6e88c62?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" class="d-block w-100" alt="Fast Checkout">
                                <div class="carousel-caption">
                                    <h5>Fast Checkout</h5>
                                    <p>Quick and secure payment process</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img src="https://images.unsplash.com/photo-1556742031-c6961e8560b0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" class="d-block w-100" alt="24/7 Support">
                                <div class="carousel-caption">
                                    <h5>24/7 Support</h5>
                                    <p>We're always here to help you</p>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#loginCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#loginCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
                
                <div class="mt-4">
                    <p>New to ShopEase? <a href="sign up.php" class="text-white fw-bold">Create an account</a></p>
                </div>
            </div>
            
            <!-- Right Column - Login Form -->
            <div class="col-lg-6 auth-right">
                <div class="auth-header">
                    <div class="auth-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <h2>Login to Your Account</h2>
                    <p class="text-muted">Enter your credentials to continue</p>
                </div>
                
                <!-- Display Database Connection Error -->
                <?php if (isset($errors['database'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Database Error:</strong> <?php echo $errors['database']; ?>
                        <br><small>Please check your database configuration or contact support.</small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Display Success Message -->
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Login Successful!</strong> Welcome back <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?>! 
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Display Form Error Messages -->
                <?php if (!empty($errors)): ?>
                    <?php if (isset($errors['email']) || isset($errors['password'])): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php 
                            if (isset($errors['email'])) echo $errors['email'] . '<br>';
                            if (isset($errors['password'])) echo $errors['password'];
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <form id="loginForm" method="POST" action="">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   id="loginEmail" name="email" 
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
                        <label for="loginPassword" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                   id="loginPassword" name="password" 
                                   placeholder="Enter your password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleLoginPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['password']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                            <label class="form-check-label" for="rememberMe">
                                Remember me
                            </label>
                            <a href="#" class="float-end text-decoration-none" id="forgotPassword">Forgot Password?</a>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mb-4">Login to Account</button>
                    
                    <div class="divider">
                        <span>Or login with</span>
                    </div>
                    
                    <div class="row g-2 mb-4">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-google" id="googleLogin">
                                <i class="fab fa-google me-2"></i> Google
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-facebook" id="facebookLogin">
                                <i class="fas fa-facebook-f me-2"></i> Facebook
                            </button>
                        </div>
                    </div>
                    
                    <div class="auth-footer">
                        <p>Don't have an account? <a href="sign up.php">Sign up here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include "include/footer.php"; ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        document.getElementById('toggleLoginPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('loginPassword');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
        
        // Check if there's saved user data from signup
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-fill email if available from session or localStorage
            <?php if (!empty($email)): ?>
                document.getElementById('loginEmail').value = '<?php echo htmlspecialchars($email); ?>';
            <?php endif; ?>
            
            // Initialize carousel
            const loginCarousel = new bootstrap.Carousel(document.getElementById('loginCarousel'), {
                interval: 3000,
                ride: 'carousel'
            });
        });
        
        // Forgot password functionality
        document.getElementById('forgotPassword').addEventListener('click', function(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            
            if (email) {
                // In a real application, this would send a password reset email
                alert(`Password reset instructions will be sent to ${email} (This is a demo).`);
                
                // Simulate API call
                const formData = new FormData();
                formData.append('email', email);
                formData.append('action', 'forgot_password');
                
                fetch('password_reset.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Password reset email sent! Check your inbox.', 'success');
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } else {
                alert('Please enter your email address first.');
                document.getElementById('loginEmail').focus();
            }
        });
        
        // Social login buttons
        document.getElementById('googleLogin').addEventListener('click', function() {
            alert('Google login would be implemented here in a real application.');
            
            // Simulate Google login
            // window.location.href = 'google_auth.php';
        });
        
        document.getElementById('facebookLogin').addEventListener('click', function() {
            alert('Facebook login would be implemented here in a real application.');
            
            // Simulate Facebook login
            // window.location.href = 'facebook_auth.php';
        });
        
        // Form validation before submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in both email and password.');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }
        });
        
        // Helper function to show alerts
        function showAlert(message, type) {
            // Remove any existing alerts
            const existingAlert = document.querySelector('.alert:not(.alert-dismissible)');
            if (existingAlert) {
                existingAlert.remove();
            }
            
            // Create alert element
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insert after the auth-header
            const authHeader = document.querySelector('.auth-right .auth-header');
            authHeader.parentNode.insertBefore(alertDiv, authHeader.nextSibling);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Auto-dismiss success alert after redirection delay
        <?php if ($success): ?>
            setTimeout(function() {
                const successAlert = document.querySelector('.alert-success');
                if (successAlert) {
                    const bsAlert = new bootstrap.Alert(successAlert);
                    bsAlert.close();
                }
            }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>
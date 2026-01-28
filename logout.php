<?php
// logout.php

// Include database connection
require_once "admin/db_connection.php";

// Start session
session_start();

// Store user info before destroying session
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Optional: Log logout activity to database
if ($user_id) {
    try {
        $logout_time = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'];
        
        $log_query = "INSERT INTO user_logs (user_id, activity, ip_address, timestamp) 
                      VALUES (?, 'logout', ?, ?)";
        
        if ($stmt = $conn->prepare($log_query)) {
            $stmt->bind_param("iss", $user_id, $ip_address, $logout_time);
            $stmt->execute();
            $stmt->close();
        }
    } catch (Exception $e) {
        // Silently fail if logging doesn't work
        error_log("Logout logging failed: " . $e->getMessage());
    }
}

// Clear all session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Clear remember me cookie if exists
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
    
    // Optional: Clear remember token from database
    if ($user_id && isset($_COOKIE['remember_user'])) {
        $token = $_COOKIE['remember_user'];
        $clear_token = "UPDATE users SET remember_token = NULL WHERE id = ?";
        if ($stmt = $conn->prepare($clear_token)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Clear any other auth-related cookies
setcookie('PHPSESSID', '', time() - 3600, '/');

// Close database connection
$conn->close();

// Set logout success flag
$logout_success = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - ShopEase</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* logout.css styles integrated */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .logout-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: fadeIn 0.5s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logout-icon {
            font-size: 80px;
            color: #667eea;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
            border-radius: 15px;
            margin: 25px 0;
            font-size: 18px;
            color: #444;
            border-left: 5px solid #667eea;
        }

        .user-name {
            color: #667eea;
            font-weight: 600;
            text-transform: capitalize;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            margin: 30px auto;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .redirect-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 25px 0;
            color: #666;
            font-size: 15px;
        }

        .countdown {
            font-weight: bold;
            color: #e74c3c;
            font-size: 18px;
            display: inline-block;
            min-width: 25px;
        }

        .btn-home {
            display: inline-block;
            padding: 10px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-outline-primary {
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 123, 255, 0.3);
        }

        @media (max-width: 576px) {
            .logout-container {
                padding: 30px 20px;
            }
            
            .logout-icon {
                font-size: 60px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            .message {
                font-size: 16px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Success Logout Screen -->
    <div class="logout-container">
        <div class="logout-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        
        <h2>Logged Out Successfully</h2>
        
        <div class="message">
            Goodbye, <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>!
            <br>
            You have been successfully logged out.
        </div>
        
        <div class="spinner"></div>
        
        <div class="redirect-info">
            <p>
                <i class="fas fa-info-circle me-2"></i>
                You will be redirected to the homepage in <span class="countdown">3</span> seconds...
            </p>
            <p class="mb-0 small text-muted">
                To access your account again, you'll need to login.
            </p>
        </div>
        
        <div class="mt-3">
            <a href="index.php" class="btn-home me-2">
                <i class="fas fa-home me-2"></i> Go to Homepage
            </a>
            <a href="login.php" class="btn btn-outline-primary">
                <i class="fas fa-sign-in-alt me-2"></i> Login Again
            </a>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Countdown timer
        let countdown = 3;
        const countdownElement = document.querySelector('.countdown');
        
        // Update countdown every second
        const countdownInterval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                // Redirect to index.php
                window.location.href = 'index.php';
            }
        }, 1000);
        
        // Force reload index.php without cache when redirecting
        setTimeout(function() {
            window.location.replace('index.php');
        }, 3000);
        
        // Prevent back button after logout
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
        
        // Clear any cached session data
        if (window.localStorage) {
            localStorage.removeItem('userData');
            localStorage.removeItem('isLoggedIn');
            localStorage.removeItem('currentUser');
            localStorage.removeItem('cartItems');
            localStorage.removeItem('wishlist');
        }
        
        // Clear sessionStorage
        if (window.sessionStorage) {
            sessionStorage.clear();
        }
        
        // Clear cookies programmatically
        function clearAllCookies() {
            const cookies = document.cookie.split(";");
            for (let i = 0; i < cookies.length; i++) {
                const cookie = cookies[i];
                const eqPos = cookie.indexOf("=");
                const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
                document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
                document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=" + window.location.hostname;
            }
        }
        
        // Call clear cookies function
        clearAllCookies();
    </script>
</body>
</html>
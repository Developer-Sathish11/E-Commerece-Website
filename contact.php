<?php 
session_start();
include "admin/db_connection.php"; 

// Handle form submission
$message_sent = false;
$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If no errors, process the message
    if (empty($errors)) {
        // Prepare SQL statement to insert contact message
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        
        if ($stmt->execute()) {
            $success_message = "Thank you for contacting us! We'll get back to you soon.";
            $message_sent = true;
            
            // Clear form fields
            $name = $email = $phone = $subject = $message = '';
        } else {
            $error_message = "Sorry, there was an error sending your message. Please try again later.";
        }
        $stmt->close();
    } else {
        $error_message = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ShopEase</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --light-bg: #f8f9fa;
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff;
        }
        
        .contact-section {
            padding: 80px 0 60px;
            background-color: var(--light-bg);
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 15px;
            text-align: center;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }
        
        .section-subtitle {
            text-align: center;
            color: var(--secondary-color);
            margin-bottom: 50px;
            font-size: 1.1rem;
        }
        
        .contact-container {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 50px;
        }
        
        .contact-info {
            background: linear-gradient(135deg, var(--primary-color), #0a58ca);
            color: white;
            padding: 40px;
            height: 100%;
        }
        
        .contact-info h3 {
            font-size: 1.8rem;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .contact-info p {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        
        .contact-icon {
            background: rgba(255,255,255,0.2);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .contact-icon i {
            font-size: 1.2rem;
        }
        
        .contact-details h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .contact-details p {
            margin: 0;
            opacity: 0.8;
            font-size: 0.95rem;
        }
        
        .social-links {
            margin-top: 40px;
        }
        
        .social-links h5 {
            font-size: 1.1rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .social-icons {
            display: flex;
            gap: 15px;
        }
        
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-icons a:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-3px);
        }
        
        .contact-form {
            padding: 40px;
        }
        
        .contact-form h3 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .form-control, .form-select {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #0a58ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
        
        .btn-primary i {
            margin-right: 8px;
        }
        
        .map-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-top: 30px;
        }
        
        .map-container iframe {
            width: 100%;
            height: 400px;
            border: none;
        }
        
        .faq-section {
            padding: 60px 0;
        }
        
        .faq-item {
            margin-bottom: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .faq-question {
            background: #f8f9fa;
            padding: 20px;
            font-weight: 600;
            color: #333;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }
        
        .faq-question:hover {
            background: #e9ecef;
        }
        
        .faq-question i {
            transition: transform 0.3s;
        }
        
        .faq-answer {
            padding: 20px;
            display: none;
            background: white;
        }
        
        .faq-item.active .faq-answer {
            display: block;
        }
        
        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }
        
        @media (max-width: 768px) {
            .contact-section {
                padding: 40px 0;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .contact-info, .contact-form {
                padding: 30px 20px;
            }
            
            .contact-container {
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
<?php include "include/nav.php"; ?>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <h1 class="section-title">Contact Us</h1>
        <p class="section-subtitle">Have questions? We're here to help. Get in touch with our team.</p>
        
        <!-- Display messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message && !$message_sent): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="row contact-container">
            <div class="col-lg-5">
                <div class="contact-info">
                    <h3>Get in Touch</h3>
                    <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Our Location</h4>
                            <p>123 Shopping Street, Dharmapuri District<br>Tamilnadu, INDIA</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Phone Number</h4>
                            <p>+910123456789<br>+8300354452</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Email Address</h4>
                            <p>shopnow123@gmail.com<br>tnsathish083@gmail.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Working Hours</h4>
                            <p>Monday - Friday: 9AM - 8PM<br>Saturday - Sunday: 10AM - 6PM</p>
                        </div>
                    </div>
                    
                    <div class="social-links">
                        <h5>Follow Us</h5>
                        <div class="social-icons">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-pinterest"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7">
                <div class="contact-form">
                    <h3>Send Us a Message</h3>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="contactForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>" 
                                       required placeholder="Enter your full name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                                       required placeholder="Enter your email">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($phone ?? ''); ?>" 
                                       placeholder="Enter your phone number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="subject" class="form-label">Subject *</label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Select a subject</option>
                                    <option value="General Inquiry" <?php echo (($subject ?? '') == 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                                    <option value="Product Support" <?php echo (($subject ?? '') == 'Product Support') ? 'selected' : ''; ?>>Product Support</option>
                                    <option value="Order Issues" <?php echo (($subject ?? '') == 'Order Issues') ? 'selected' : ''; ?>>Order Issues</option>
                                    <option value="Shipping & Delivery" <?php echo (($subject ?? '') == 'Shipping & Delivery') ? 'selected' : ''; ?>>Shipping & Delivery</option>
                                    <option value="Returns & Refunds" <?php echo (($subject ?? '') == 'Returns & Refunds') ? 'selected' : ''; ?>>Returns & Refunds</option>
                                    <option value="Wholesale Inquiry" <?php echo (($subject ?? '') == 'Wholesale Inquiry') ? 'selected' : ''; ?>>Wholesale Inquiry</option>
                                    <option value="Other" <?php echo (($subject ?? '') == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="6" 
                                      required placeholder="Enter your message here..."><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Map Section -->
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.177858804427!2d-73.9878446845943!3d40.70555197933207!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a315cdf4c9b%3A0x8b934de5cae6f67a!2sWall%20St%2C%20New%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1624567890123!5m2!1sen!2s" 
                    allowfullscreen="" loading="lazy"></iframe>
        </div>
        
        <!-- FAQ Section -->
        <div class="faq-section">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Find quick answers to common questions</p>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>What are your shipping options and delivery times?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We offer standard shipping (5-7 business days), express shipping (2-3 business days), and overnight shipping for urgent orders. Shipping costs vary based on destination and package weight. You can view all shipping options at checkout.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>What is your return policy?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We offer a 30-day return policy for most items. Products must be in original condition with all tags attached. Some items (like personalized products or intimate apparel) may not be eligible for return. Please check the product page for specific return information.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Do you ship internationally?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, we ship to over 50 countries worldwide. International shipping typically takes 10-21 business days depending on the destination. Additional customs fees and import taxes may apply, which are the responsibility of the customer.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How can I track my order?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Once your order ships, you'll receive a confirmation email with a tracking number and link. You can also track your order by logging into your account on our website and visiting the "Order History" section.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>What payment methods do you accept?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We accept all major credit cards (Visa, MasterCard, American Express, Discover), PayPal, Apple Pay, and Google Pay. All transactions are encrypted and secure.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "include/footer.php"; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // FAQ Toggle Functionality
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const faqItem = question.parentElement;
            faqItem.classList.toggle('active');
        });
    });
    
    // Form validation
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value.trim();
        
        if (!name || !email || !subject || !message) {
            e.preventDefault();
            alert('Please fill in all required fields marked with *.');
            return false;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address.');
            return false;
        }
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
</body>
</html>
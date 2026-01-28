<?php include"admin/db_connection.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ShopEase</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="styles/about.css">
</head>
<body>  
<?php include"include/nav.php"; ?> 
    <!-- Hero Section -->
    <section class="hero-about">
        <div class="container">
            <h1 class="display-4 fw-bold">Our Story</h1>
            <p class="lead">ShopEase was founded with a simple mission: to make online shopping easier, more enjoyable, and accessible to everyone.</p>
            <a href="#our-mission" class="btn btn-light btn-lg mt-3">Explore Our Journey</a>
        </div>
    </section>

    <!-- Mission & Values -->
    <section id="our-mission" class="container my-5">
        <h2 class="section-title">Our Mission & Values</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="mission-box">
                    <div class="icon-box">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h4>Our Mission</h4>
                    <p>To provide customers with a seamless shopping experience, offering high-quality products at competitive prices while delivering exceptional customer service.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="mission-box">
                    <div class="icon-box">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h4>Our Vision</h4>
                    <p>To become the most trusted and customer-centric online shopping destination globally, revolutionizing how people shop online.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="mission-box">
                    <div class="icon-box">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4>Our Values</h4>
                    <p>Customer satisfaction, integrity, innovation, and community engagement are at the core of everything we do at ShopEase.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="container my-5">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="stats-box">
                    <div class="stats-number">50K+</div>
                    <p>Happy Customers</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-box">
                    <div class="stats-number">10K+</div>
                    <p>Products Available</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-box">
                    <div class="stats-number">120+</div>
                    <p>Countries Served</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-box">
                    <div class="stats-number">5</div>
                    <p>Years of Excellence</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story Timeline -->
    <section class="container my-5">
        <h2 class="section-title">Our Journey</h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-content">
                    <h5>2018 - The Beginning</h5>
                    <p>ShopEase was founded by three college friends who wanted to simplify online shopping. Started as a small e-commerce store with just 50 products.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <h5>2019 - First Milestone</h5>
                    <p>Reached 10,000 customers and expanded our product catalog to over 1,000 items. Launched our mobile app for iOS and Android.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <h5>2020 - Pandemic Response</h5>
                    <p>Adapted to the changing world by implementing contactless delivery and expanding our essential goods category. Partnered with local businesses.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <h5>2021 - International Expansion</h5>
                    <p>Started shipping to 50+ countries and launched regional websites for Europe and Asia. Introduced our premium loyalty program.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <h5>2022 - Innovation Year</h5>
                    <p>Launched AR try-on features and AI-powered product recommendations. Reached 1 million active users worldwide.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <h5>2023 - Today</h5>
                    <p>Continuing to innovate with sustainable packaging initiatives and expanding our product range to include eco-friendly alternatives.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="container my-5">
        <h2 class="section-title">Meet Our Team</h2>
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="team-img" alt="CEO">
                    <h5>Michael Chen</h5>
                    <p class="text-muted">CEO & Founder</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="team-img" alt="CTO">
                    <h5>Sarah Johnson</h5>
                    <p class="text-muted">Chief Technology Officer</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="team-img" alt="Head of Marketing">
                    <h5>David Rodriguez</h5>
                    <p class="text-muted">Head of Marketing</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="team-img" alt="Customer Service Director">
                    <h5>Jessica Williams</h5>
                    <p class="text-muted">Customer Service Director</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sustainability Section -->
    <section class="container my-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="section-title">Our Commitment to Sustainability</h2>
                <p>At ShopEase, we believe in responsible business practices. That's why we've implemented several initiatives to reduce our environmental impact:</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-leaf text-success me-2"></i> 100% recyclable packaging</li>
                    <li class="mb-2"><i class="fas fa-truck text-primary me-2"></i> Carbon-neutral shipping options</li>
                    <li class="mb-2"><i class="fas fa-sun text-warning me-2"></i> Solar-powered warehouses</li>
                    <li class="mb-2"><i class="fas fa-hand-holding-heart text-danger me-2"></i> Partnerships with eco-friendly brands</li>
                </ul>
                <a href="#" class="btn btn-primary mt-3">Learn About Our Green Initiatives</a>
            </div>
            <div class="col-md-6">
                <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" class="img-fluid rounded" alt="Sustainable Packaging">
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="container my-5 text-center">
        <div class="p-5 rounded" style="background-color: rgba(52, 152, 219, 0.1);">
            <h2 class="mb-4">Join Our Growing Community</h2>
            <p class="lead mb-4">Become part of the ShopEase family today and experience shopping reimagined.</p>
            <a href="index.php#products" class="btn btn-primary btn-lg me-3">Start Shopping</a>
            <a href="#" class="btn btn-outline-primary btn-lg">Join Our Newsletter</a>
        </div>
    </section>

    <?php include"include/footer.php"; ?>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 70,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Highlight active nav link on scroll
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if(scrollY >= (sectionTop - 100)) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if(link.getAttribute('href') === `#${current}` || 
                   (current === '' && link.getAttribute('href') === 'about.php')) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
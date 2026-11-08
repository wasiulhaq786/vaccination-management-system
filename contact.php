<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/config.php';

$page_title = 'Contact Us';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo asset('css/style.css'); ?>" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: #0a2540;">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?php echo url(''); ?>">
                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; flex-shrink: 0;">
                    <i class="fas fa-shield-heart fs-5 text-primary"></i>
                </div>
                <span style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.3rem; letter-spacing: 0.5px; color: #ffffff;">
                    Vaccine<span class="text-info">Care</span>
                </span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item"><a class="nav-link" href="<?php echo url('about.php'); ?>">About</a></li>
                    <li class="nav-item"><a class="nav-link active" href="<?php echo url('contact.php'); ?>">Contact</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="<?php echo url('auth/login.php'); ?>">Parent Login</a></li>
                    <li class="nav-item"><a class="btn btn-teal btn-sm" href="<?php echo url('auth/register.php'); ?>">Register</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="<?php echo url('hospital/login.php'); ?>">Hospital</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm" href="<?php echo url('admin/login.php'); ?>">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <h1 class="mb-3">Get in Touch with Us</h1>
                    <p class="lead mb-4 opacity-90">Have questions regarding vaccination schedules, hospital centers, or technical support? Reach out to our team below.</p>
                </div>
                <div class="col-lg-5 text-center">
                    <i class="fas fa-headset" style="font-size: 10rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Content Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center g-4">
                <!-- Left Column: Contact Info & Map -->
                <div class="col-lg-5">
                    <div class="card border border-primary-subtle shadow-sm p-4 h-100 d-flex flex-column justify-content-between rounded-4">
                        <div>
                            <h4 class="fw-bold text-dark mb-4">Contact Information</h4>
                            
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Our Location</h6>
                                    <p class="text-muted small mb-0">Health Department, City Center, Pakistan</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px;">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Phone Number</h6>
                                    <p class="text-muted small mb-0">+92 300 1234567</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-4">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px;">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Email Address</h6>
                                    <p class="text-muted small mb-0">support@vaccination-ms.com</p>
                                </div>
                            </div>
                        </div>

                        <!-- Embedded Google Map -->
                        <div class="rounded-3 overflow-hidden shadow-sm mt-3" style="height: 180px; width: 100%;">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3618.664069811195!2d67.0011!3d24.8607!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjTCsDUxJzM4LjUiTiA2N8KwMDAnMDQuMCJF!5e0!3m2!1sen!2spk!4v1620000000000!5m2!1sen!2spk" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Contact Form -->
                <div class="col-lg-5">
                    <div class="card border border-primary-subtle shadow-sm p-4 h-100 rounded-4">
                        <h4 class="fw-bold text-dark mb-4">Send Us a Message</h4>
                        <form action="process-contact.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label small fw-bold text-muted">Your Name</label>
                                <input type="text" class="form-control py-2" id="name" name="name" placeholder="Enter your name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold text-muted">Your Email</label>
                                <input type="email" class="form-control py-2" id="email" name="email" placeholder="name@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label small fw-bold text-muted">Your Message</label>
                                <textarea class="form-control py-2" id="message" name="message" rows="4" placeholder="How can we help you?" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                <i class="fas fa-paper-plane me-2"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4" style="background: #0a2540; color: rgba(255,255,255,0.8);">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
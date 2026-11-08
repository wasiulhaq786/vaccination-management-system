<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/config.php';

$page_title = 'About Us';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - About Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo asset('css/style.css'); ?>" rel="stylesheet">
</head>
<body>
    <!-- Navbar (Exact same as Index) -->
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
                    <li class="nav-item"><a class="nav-link active" href="<?php echo url('about.php'); ?>">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo url('contact.php'); ?>">Contact</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="<?php echo url('auth/login.php'); ?>">Parent Login</a></li>
                    <li class="nav-item"><a class="btn btn-teal btn-sm" href="<?php echo url('auth/register.php'); ?>">Register</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="<?php echo url('hospital/login.php'); ?>">Hospital</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm" href="<?php echo url('admin/login.php'); ?>">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section (Exact same theme style as Index) -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <h1 class="mb-3">About VaccineCare</h1>
                    <p class="lead mb-4 opacity-90">A comprehensive digital platform designed to streamline child immunization management, connecting parents, healthcare institutions, and administrators safely and efficiently.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo url(''); ?>" class="btn btn-light btn-lg">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <i class="fas fa-info-circle" style="font-size: 10rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Content Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-3 text-primary">Our Mission & Vision</h2>
                    <p class="text-muted">
                        VaccineCare was built with a single goal in mind: to eliminate gaps in child healthcare tracking. We connect parents, hospitals, and medical administrators to ensure that every single child receives timely vaccinations according to standard medical schedules.
                    </p>
                    <p class="text-muted">
                        Through our smart scheduling, automated reminders, and transparent hospital coordination, we keep complete digital medical histories safe, secure, and easily accessible.
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="p-4 bg-light rounded shadow-sm border">
                        <h4 class="fw-bold mb-3 text-dark"><i class="fas fa-check-circle text-success me-2"></i>Key Platform Benefits</h4>
                        <ul class="list-unstyled text-muted mb-0">
                            <li class="mb-2"><i class="fas fa-angle-right text-primary me-2"></i> Real-time tracking of children's vaccination status.</li>
                            <li class="mb-2"><i class="fas fa-angle-right text-primary me-2"></i> Automated appointment bookings with registered hospitals.</li>
                            <li class="mb-2"><i class="fas fa-angle-right text-primary me-2"></i> Secure role-based panels for Parents, Hospitals, and Admins.</li>
                            <li class="mb-0"><i class="fas fa-angle-right text-primary me-2"></i> Comprehensive medical history archiving and reporting.</li>
                        </ul>
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
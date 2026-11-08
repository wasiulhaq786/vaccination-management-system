<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/config.php';

// Redirect logged-in users to their dashboard
if (is_logged_in('admin')) {
    redirect('admin/dashboard.php');
} elseif (is_logged_in('parent')) {
    redirect('parent/dashboard.php');
} elseif (is_logged_in('hospital')) {
    redirect('hospital/dashboard.php');
}

$page_title = 'Home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Protect Your Child</title>
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
                    <li class="nav-item"><a class="nav-link" href="<?php echo url('contact.php'); ?>">Contact</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="<?php echo url('auth/login.php'); ?>">Parent Login</a></li>
                    <li class="nav-item"><a class="btn btn-teal btn-sm" href="<?php echo url('auth/register.php'); ?>">Register</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="<?php echo url('hospital/login.php'); ?>">Hospital</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm" href="<?php echo url('admin/login.php'); ?>">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="mb-3">Protect Every Child with Timely Vaccination</h1>
                    <p class="lead mb-4 opacity-90">A complete online platform connecting parents, hospitals and administrators to manage child vaccination schedules, appointments and records efficiently.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo url('auth/register.php'); ?>" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Register as Parent
                        </a>
                        <a href="<?php echo url('hospital/register.php'); ?>" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-hospital me-2"></i>Hospital Registration
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-shield-virus" style="font-size: 12rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Why Choose Our System?</h2>
                <p class="text-muted">Streamlined vaccination management for everyone</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-user-friends"></i>
                        <h5>For Parents</h5>
                        <p class="text-muted mb-0">Register children, book appointments, track vaccination status and receive timely reminders.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-hospital"></i>
                        <h5>For Hospitals</h5>
                        <p class="text-muted mb-0">Manage bookings, update vaccination status and keep accurate digital records.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-user-shield"></i>
                        <h5>For Administrators</h5>
                        <p class="text-muted mb-0">Oversee children, vaccines, hospitals, requests and generate comprehensive reports.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules -->
    <section class="py-5 bg-white" id="modules">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">System Modules</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><i class="fas fa-user-shield me-2"></i>Admin</h5>
                            <ul class="list-unstyled mb-0 small">
                                <li><i class="fas fa-check text-success me-2"></i>Child details & history</li>
                                <li><i class="fas fa-check text-success me-2"></i>Vaccination dates & reports</li>
                                <li><i class="fas fa-check text-success me-2"></i>Vaccine management</li>
                                <li><i class="fas fa-check text-success me-2"></i>Parent requests approval</li>
                                <li><i class="fas fa-check text-success me-2"></i>Hospital & booking management</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-teal"><i class="fas fa-users me-2"></i>Parent</h5>
                            <ul class="list-unstyled mb-0 small">
                                <li><i class="fas fa-check text-success me-2"></i>Register & manage children</li>
                                <li><i class="fas fa-check text-success me-2"></i>Search vaccines</li>
                                <li><i class="fas fa-check text-success me-2"></i>Book appointments</li>
                                <li><i class="fas fa-check text-success me-2"></i>Track vaccination status</li>
                                <li><i class="fas fa-check text-success me-2"></i>View history & profile</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-success"><i class="fas fa-hospital me-2"></i>Hospital</h5>
                            <ul class="list-unstyled mb-0 small">
                                <li><i class="fas fa-check text-success me-2"></i>Register & login</li>
                                <li><i class="fas fa-check text-success me-2"></i>View own bookings</li>
                                <li><i class="fas fa-check text-success me-2"></i>Update vaccination status</li>
                                <li><i class="fas fa-check text-success me-2"></i>Dashboard statistics</li>
                                <li><i class="fas fa-check text-success me-2"></i>Profile management</li>
                            </ul>
                        </div>
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
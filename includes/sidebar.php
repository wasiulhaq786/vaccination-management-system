<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo url(''); ?>" class="sidebar-brand">
            <i class="fas fa-syringe me-2"></i>
            <span>VMS</span>
        </a>
        <button class="btn btn-link d-lg-none text-white" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <nav class="sidebar-nav">
        <?php $role = $_SESSION['role'] ?? ''; ?>
        
        <?php if ($role === 'admin'): ?>
            <a href="<?php echo url('admin/dashboard.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
            <a href="<?php echo url('admin/children.php'); ?>" class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['children.php','child-details.php']) ? 'active' : ''; ?>">
                <i class="fas fa-child"></i> <span>Child Details</span>
            </a>
            <a href="<?php echo url('admin/vaccination-dates.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'vaccination-dates.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i> <span>Vaccination Dates</span>
            </a>
            <a href="<?php echo url('admin/vaccination-reports.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'vaccination-reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-medical"></i> <span>Vaccination Reports</span>
            </a>
            <a href="<?php echo url('admin/vaccines.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'vaccines.php' ? 'active' : ''; ?>">
                <i class="fas fa-vial"></i> <span>Vaccine Management</span>
            </a>
            <a href="<?php echo url('admin/requests.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'active' : ''; ?>">
                <i class="fas fa-inbox"></i> <span>Parent Requests</span>
            </a>
            <a href="<?php echo url('admin/hospitals.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'hospitals.php' ? 'active' : ''; ?>">
                <i class="fas fa-hospital"></i> <span>Hospital Management</span>
            </a>
            <a href="<?php echo url('admin/bookings.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i> <span>Booking Management</span>
            </a>

        <?php elseif ($role === 'parent'): ?>
            <a href="<?php echo url('parent/dashboard.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
            <a href="<?php echo url('parent/children.php'); ?>" class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['children.php','add-child.php','edit-child.php']) ? 'active' : ''; ?>">
                <i class="fas fa-child"></i> <span>My Children</span>
            </a>
            <a href="<?php echo url('parent/vaccinations.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'vaccinations.php' ? 'active' : ''; ?>">
                <i class="fas fa-syringe"></i> <span>Vaccination Details</span>
            </a>
            <a href="<?php echo url('parent/book-appointment.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'book-appointment.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-plus"></i> <span>Book Appointment</span>
            </a>
            <a href="<?php echo url('parent/vaccine-search.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'vaccine-search.php' ? 'active' : ''; ?>">
                <i class="fas fa-search"></i> <span>Search Vaccines</span>
            </a>
            <a href="<?php echo url('parent/vaccination-status.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'vaccination-status.php' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-check"></i> <span>Vaccination Status</span>
            </a>
            <a href="<?php echo url('parent/profile.php'); ?>" class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['profile.php','edit-profile.php','change-password.php']) ? 'active' : ''; ?>">
                <i class="fas fa-user"></i> <span>My Profile</span>
            </a>

        <?php elseif ($role === 'hospital'): ?>
            <a href="<?php echo url('hospital/dashboard.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
            <a href="<?php echo url('hospital/bookings.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i> <span>Bookings</span>
            </a>
            <a href="<?php echo url('hospital/vaccination-status.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'vaccination-status.php' ? 'active' : ''; ?>">
                <i class="fas fa-notes-medical"></i> <span>Update Status</span>
            </a>
            <a href="<?php echo url('hospital/profile.php'); ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-hospital-user"></i> <span>Profile</span>
            </a>
        <?php endif; ?>
    </nav>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<nav class="navbar navbar-expand-lg top-navbar">
    <div class="container-fluid">
        <button class="btn btn-link sidebar-toggle d-lg-none" type="button" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <span class="navbar-brand mb-0 h1 d-none d-md-block">
            <?php echo e($page_title ?? 'Dashboard'); ?>
        </span>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-muted d-none d-sm-inline">
                <i class="fas fa-user-circle me-1"></i>
                <?php echo e($_SESSION['user_name'] ?? 'User'); ?>
                <small class="badge bg-primary ms-1"><?php echo ucfirst(e($_SESSION['role'] ?? '')); ?></small>
            </span>
            <?php
            $logout_url = 'auth/logout.php';
            if (($_SESSION['role'] ?? '') === 'admin') $logout_url = 'admin/logout.php';
            if (($_SESSION['role'] ?? '') === 'hospital') $logout_url = 'hospital/logout.php';
            ?>
            <a href="<?php echo url($logout_url); ?>" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt"></i> <span class="d-none d-md-inline">Logout</span>
            </a>
        </div>
    </div>
</nav>

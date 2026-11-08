<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/csrf.php';

if (is_logged_in()) {
    $role = current_role();
    if ($role === 'admin') redirect('admin/dashboard.php');
    if ($role === 'hospital') redirect('hospital/dashboard.php');
    redirect('parent/dashboard.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Email and password are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } else {
            $stmt = mysqli_prepare($conn, "SELECT id, full_name, email, password, status FROM parents WHERE email = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] !== 'active') {
                    $error = 'Your account is inactive. Contact support.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['role'] = 'parent';
                    $_SESSION['created'] = time();
                    flash('success', 'Welcome back, ' . $user['full_name'] . '!');
                    redirect('parent/dashboard.php');
                }
            } else {
                $error = 'Invalid email or password.';
            }
        }
    }
}

$page_title = 'Parent Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?> | <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo asset('css/style.css'); ?>" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-user-circle"></i>
            <h4 class="mb-0">Parent Login</h4>
            <p class="mb-0 opacity-75 small">Access your vaccination portal</p>
        </div>
        <div class="auth-body">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?php echo e($error); ?></div>
            <?php endif; ?>
            <?php if ($msg = flash('success')): ?>
                <div class="alert alert-success py-2"><?php echo e($msg); ?></div>
            <?php endif; ?>
            <form method="POST" action="" id="loginForm" novalidate>
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e($email); ?>" required placeholder="your@email.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="<?php echo url('auth/forgot-password.php'); ?>" class="small">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <p class="text-center mt-3 mb-0 small">
                Don't have an account? <a href="<?php echo url('auth/register.php'); ?>">Register</a>
            </p>
            <p class="text-center mt-2 mb-0">
                <a href="<?php echo url(''); ?>" class="small text-muted"><i class="fas fa-arrow-left me-1"></i>Back to Home</a>
            </p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

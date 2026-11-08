<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/csrf.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = false;

if (empty($token)) {
    $error = 'Invalid reset link.';
} else {
    $stmt = mysqli_prepare($conn, "SELECT id FROM parents WHERE reset_token = ? AND reset_expires > NOW() AND status = 'active'");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$user) {
        $error = 'Reset link is invalid or has expired.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    if (!verify_csrf()) {
        $error = 'Invalid security token.';
    } else {
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE parents SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $hash, $user['id']);
            if (mysqli_stmt_execute($stmt)) {
                $success = true;
            } else {
                $error = 'Failed to update password.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

$page_title = 'Reset Password';
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
            <i class="fas fa-lock"></i>
            <h4 class="mb-0">Reset Password</h4>
        </div>
        <div class="auth-body">
            <?php if ($success): ?>
                <div class="alert alert-success">Password updated successfully. <a href="<?php echo url('auth/login.php'); ?>">Login now</a></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?php echo e($error); ?></div>
                <p class="text-center"><a href="<?php echo url('auth/forgot-password.php'); ?>">Request new link</a></p>
            <?php else: ?>
            <form method="POST" action="">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary w-100">Update Password</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>

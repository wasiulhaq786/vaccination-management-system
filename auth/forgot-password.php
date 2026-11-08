<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/csrf.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token.';
    } else {
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email.';
        } else {
            $stmt = mysqli_prepare($conn, "SELECT id, full_name FROM parents WHERE email = ? AND status = 'active'");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                $stmt = mysqli_prepare($conn, "UPDATE parents SET reset_token = ?, reset_expires = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "ssi", $token, $expires, $user['id']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // In a real system you would email the link. For local demo we show the token.
                $reset_link = url('auth/reset-password.php?token=' . $token);
                $message = 'Password reset link has been generated (demo mode). Use this link: <br><a href="' . e($reset_link) . '">' . e($reset_link) . '</a>';
            } else {
                // Do not reveal if email exists
                $message = 'If the email exists in our system, a reset link has been sent.';
            }
        }
    }
}

$page_title = 'Forgot Password';
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
            <i class="fas fa-key"></i>
            <h4 class="mb-0">Forgot Password</h4>
            <p class="mb-0 opacity-75 small">Enter your email to reset password</p>
        </div>
        <div class="auth-body">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?php echo e($error); ?></div>
            <?php endif; ?>
            <?php if ($message): ?>
                <div class="alert alert-success py-2"><?php echo $message; ?></div>
            <?php else: ?>
            <form method="POST" action="">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="your@email.com">
                </div>
                <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
            </form>
            <?php endif; ?>
            <p class="text-center mt-3 mb-0">
                <a href="<?php echo url('auth/login.php'); ?>" class="small"><i class="fas fa-arrow-left me-1"></i>Back to Login</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>

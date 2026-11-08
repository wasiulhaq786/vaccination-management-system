<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/csrf.php';

if (is_logged_in('hospital')) {
    redirect('hospital/dashboard.php');
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
            $error = 'Email and password required.';
        } else {
            $stmt = mysqli_prepare($conn, "SELECT id, hospital_name, email, password, status FROM hospitals WHERE email = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] !== 'active') {
                    $error = 'Your hospital account is not active yet. Please wait for admin approval.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['hospital_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['role'] = 'hospital';
                    $_SESSION['created'] = time();
                    flash('success', 'Welcome, ' . $user['hospital_name'] . '!');
                    redirect('hospital/dashboard.php');
                }
            } else {
                $error = 'Invalid credentials.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Login | <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo asset('css/style.css'); ?>" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header" style="background: linear-gradient(135deg, #198754, #20c997);">
            <i class="fas fa-hospital"></i>
            <h4 class="mb-0">Hospital Login</h4>
        </div>
        <div class="auth-body">
            <?php if ($error): ?><div class="alert alert-danger py-2"><?php echo e($error); ?></div><?php endif; ?>
            <?php if ($msg = flash('success')): ?><div class="alert alert-success py-2"><?php echo e($msg); ?></div><?php endif; ?>
            <form method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e($email); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Login</button>
            </form>
            <p class="text-center mt-3 mb-0 small">Not registered? <a href="<?php echo url('hospital/register.php'); ?>">Register</a></p>
            <p class="text-center mt-2"><a href="<?php echo url(''); ?>" class="small text-muted">Back to Home</a></p>
        </div>
    </div>
</div>
</body>
</html>

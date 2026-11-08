<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/csrf.php';

if (is_logged_in('hospital')) {
    redirect('hospital/dashboard.php');
}

$errors = [];
$data = ['hospital_name' => '', 'email' => '', 'phone' => '', 'address' => '', 'city' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Invalid security token.';
    } else {
        $data['hospital_name'] = trim($_POST['hospital_name'] ?? '');
        $data['email'] = trim($_POST['email'] ?? '');
        $data['phone'] = trim($_POST['phone'] ?? '');
        $data['address'] = trim($_POST['address'] ?? '');
        $data['city'] = trim($_POST['city'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($data['hospital_name']) < 3) $errors[] = 'Hospital name is required.';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
        if (empty($data['phone'])) $errors[] = 'Phone is required.';
        if (empty($data['address']) || empty($data['city'])) $errors[] = 'Address and city are required.';
        if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
        if ($password !== $confirm) $errors[] = 'Passwords do not match.';

        if (empty($errors)) {
            $stmt = mysqli_prepare($conn, "SELECT id FROM hospitals WHERE email = ?");
            mysqli_stmt_bind_param($stmt, "s", $data['email']);
            mysqli_stmt_execute($stmt);
            if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0) {
                $errors[] = 'Email already registered.';
            }
            mysqli_stmt_close($stmt);
        }

        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO hospitals (hospital_name, email, phone, address, city, password, status) VALUES (?,?,?,?,?,?,'pending')");
            mysqli_stmt_bind_param($stmt, "ssssss", $data['hospital_name'], $data['email'], $data['phone'], $data['address'], $data['city'], $hash);
            if (mysqli_stmt_execute($stmt)) {
                flash('success', 'Registration successful! Waiting for admin approval.');
                redirect('hospital/login.php');
            } else {
                $errors[] = 'Registration failed.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Registration | <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo asset('css/style.css'); ?>" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card" style="max-width: 520px;">
        <div class="auth-header" style="background: linear-gradient(135deg, #198754, #20c997);">
            <i class="fas fa-hospital"></i>
            <h4 class="mb-0">Hospital Registration</h4>
        </div>
        <div class="auth-body">
            <?php if ($errors): ?>
                <div class="alert alert-danger py-2"><ul class="mb-0 ps-3"><?php foreach ($errors as $e) echo '<li>' . e($e) . '</li>'; ?></ul></div>
            <?php endif; ?>
            <form method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Hospital Name *</label>
                    <input type="text" name="hospital_name" class="form-control" value="<?php echo e($data['hospital_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e($data['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone *</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo e($data['phone']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address *</label>
                    <input type="text" name="address" class="form-control" value="<?php echo e($data['address']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">City *</label>
                    <input type="text" name="city" class="form-control" value="<?php echo e($data['city']); ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">Register</button>
            </form>
            <p class="text-center mt-3 mb-0 small">Already registered? <a href="<?php echo url('hospital/login.php'); ?>">Login</a></p>
            <p class="text-center mt-2"><a href="<?php echo url(''); ?>" class="small text-muted">Back to Home</a></p>
        </div>
    </div>
</div>
</body>
</html>

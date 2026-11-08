<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/csrf.php';

if (is_logged_in()) {
    redirect('parent/dashboard.php');
}

$errors = [];
$data = ['full_name' => '', 'email' => '', 'phone' => '', 'cnic' => '', 'address' => '', 'city' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Invalid security token.';
    } else {
        $data['full_name'] = trim($_POST['full_name'] ?? '');
        $data['email'] = trim($_POST['email'] ?? '');
        $data['phone'] = trim($_POST['phone'] ?? '');
        $data['cnic'] = trim($_POST['cnic'] ?? '');
        $data['address'] = trim($_POST['address'] ?? '');
        $data['city'] = trim($_POST['city'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($data['full_name']) || strlen($data['full_name']) < 3) {
            $errors[] = 'Full name must be at least 3 characters.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }
        if (!preg_match('/^03[0-9]{9}$/', $data['phone'])) {
            $errors[] = 'Phone must be a valid Pakistani number (03XXXXXXXXX).';
        }
        if (!empty($data['cnic']) && !preg_match('/^[0-9]{5}-[0-9]{7}-[0-9]$/', $data['cnic'])) {
            $errors[] = 'CNIC format must be XXXXX-XXXXXXX-X.';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        // Check unique email
        if (empty($errors)) {
            $stmt = mysqli_prepare($conn, "SELECT id FROM parents WHERE email = ?");
            mysqli_stmt_bind_param($stmt, "s", $data['email']);
            mysqli_stmt_execute($stmt);
            if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0) {
                $errors[] = 'Email already registered.';
            }
            mysqli_stmt_close($stmt);
        }

        if (empty($errors) && !empty($data['cnic'])) {
            $stmt = mysqli_prepare($conn, "SELECT id FROM parents WHERE cnic = ?");
            mysqli_stmt_bind_param($stmt, "s", $data['cnic']);
            mysqli_stmt_execute($stmt);
            if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0) {
                $errors[] = 'CNIC already registered.';
            }
            mysqli_stmt_close($stmt);
        }

        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO parents (full_name, email, phone, cnic, password, address, city) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $cnic_val = $data['cnic'] ?: null;
            mysqli_stmt_bind_param($stmt, "sssssss", $data['full_name'], $data['email'], $data['phone'], $cnic_val, $hash, $data['address'], $data['city']);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                flash('success', 'Registration successful! Please login.');
                redirect('auth/login.php');
            } else {
                $errors[] = 'Registration failed. Please try again.';
                mysqli_stmt_close($stmt);
            }
        }
    }
}

$page_title = 'Parent Registration';
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
    <div class="auth-card" style="max-width: 520px;">
        <div class="auth-header">
            <i class="fas fa-user-plus"></i>
            <h4 class="mb-0">Parent Registration</h4>
            <p class="mb-0 opacity-75 small">Create your account to manage vaccinations</p>
        </div>
        <div class="auth-body">
            <?php if ($errors): ?>
                <div class="alert alert-danger py-2">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $e): ?><li><?php echo e($e); ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="POST" action="" id="registerForm" novalidate>
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo e($data['full_name']); ?>" required minlength="3">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e($data['email']); ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo e($data['phone']); ?>" placeholder="03XXXXXXXXX" required pattern="^03[0-9]{9}$">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CNIC</label>
                        <input type="text" name="cnic" class="form-control" value="<?php echo e($data['cnic']); ?>" placeholder="XXXXX-XXXXXXX-X" pattern="^[0-9]{5}-[0-9]{7}-[0-9]$">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="<?php echo e($data['address']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="<?php echo e($data['city']); ?>">
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
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <p class="text-center mt-3 mb-0 small">
                Already have an account? <a href="<?php echo url('auth/login.php'); ?>">Login</a>
            </p>
            <p class="text-center mt-2 mb-0">
                <a href="<?php echo url(''); ?>" class="small text-muted"><i class="fas fa-arrow-left me-1"></i>Back to Home</a>
            </p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const phone = this.phone.value.trim();
    const cnic = this.cnic.value.trim();
    const pass = this.password.value;
    const conf = this.confirm_password.value;
    if (!/^03[0-9]{9}$/.test(phone)) {
        e.preventDefault();
        alert('Phone must be a valid Pakistani number (03XXXXXXXXX)');
        return;
    }
    if (cnic && !/^[0-9]{5}-[0-9]{7}-[0-9]$/.test(cnic)) {
        e.preventDefault();
        alert('CNIC format must be XXXXX-XXXXXXX-X');
        return;
    }
    if (pass !== conf) {
        e.preventDefault();
        alert('Passwords do not match');
    }
});
</script>
</body>
</html>

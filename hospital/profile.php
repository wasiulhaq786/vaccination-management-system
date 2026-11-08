<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_hospital();

$page_title = 'Hospital Profile';
$hospital_id = current_user_id();
$errors = [];

$stmt = mysqli_prepare($conn, "SELECT * FROM hospitals WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$hospital = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $name = trim($_POST['hospital_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($name) < 3) $errors[] = 'Name is required.';
    if (empty($phone)) $errors[] = 'Phone is required.';

    if (empty($errors)) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE hospitals SET hospital_name=?, phone=?, address=?, city=?, password=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssssi", $name, $phone, $address, $city, $hash, $hospital_id);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE hospitals SET hospital_name=?, phone=?, address=?, city=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssssi", $name, $phone, $address, $city, $hospital_id);
        }
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['user_name'] = $name;
            flash('success', 'Profile updated.');
            redirect('hospital/profile.php');
        } else {
            $errors[] = 'Update failed.';
        }
        mysqli_stmt_close($stmt);
    }
    $hospital = array_merge($hospital, ['hospital_name' => $name, 'phone' => $phone, 'address' => $address, 'city' => $city]);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Profile</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo url('hospital/dashboard.php'); ?>">Dashboard</a></li><li class="breadcrumb-item active">Profile</li></ol></nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0 ps-3"><?php foreach ($errors as $e) echo '<li>' . e($e) . '</li>'; ?></ul></div><?php endif; ?>
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Hospital Name</label>
                        <input type="text" name="hospital_name" class="form-control" value="<?php echo e($hospital['hospital_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email (cannot change)</label>
                        <input type="email" class="form-control" value="<?php echo e($hospital['email']); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo e($hospital['phone']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="<?php echo e($hospital['address']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?php echo e($hospital['city']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (leave blank to keep)</label>
                        <input type="password" name="password" class="form-control" minlength="6">
                    </div>
                    <button type="submit" class="btn btn-success">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

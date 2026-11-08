<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_parent();

$page_title = 'Edit Profile';
$parent_id = current_user_id();
$errors = [];

$stmt = mysqli_prepare($conn, "SELECT * FROM parents WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $parent_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Invalid token.';
    } else {
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $cnic = trim($_POST['cnic'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');

        if (strlen($full_name) < 3) $errors[] = 'Name is required.';
        if (!preg_match('/^03[0-9]{9}$/', $phone)) $errors[] = 'Valid Pakistani phone required.';
        if ($cnic && !preg_match('/^[0-9]{5}-[0-9]{7}-[0-9]$/', $cnic)) $errors[] = 'Invalid CNIC format.';

        if (empty($errors)) {
            $stmt = mysqli_prepare($conn, "UPDATE parents SET full_name=?, phone=?, cnic=?, address=?, city=? WHERE id=?");
            $cnic_val = $cnic ?: null;
            mysqli_stmt_bind_param($stmt, "sssssi", $full_name, $phone, $cnic_val, $address, $city, $parent_id);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['user_name'] = $full_name;
                flash('success', 'Profile updated.');
                redirect('parent/profile.php');
            } else {
                $errors[] = 'Update failed.';
            }
            mysqli_stmt_close($stmt);
        }
        $user = array_merge($user, compact('full_name', 'phone', 'cnic', 'address', 'city'));
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Edit Profile</h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <?php if ($errors): ?>
                    <div class="alert alert-danger"><ul class="mb-0 ps-3"><?php foreach ($errors as $e) echo '<li>' . e($e) . '</li>'; ?></ul></div>
                <?php endif; ?>
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo e($user['full_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email (cannot change)</label>
                        <input type="email" class="form-control" value="<?php echo e($user['email']); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo e($user['phone']); ?>" required pattern="^03[0-9]{9}$">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CNIC</label>
                        <input type="text" name="cnic" class="form-control" value="<?php echo e($user['cnic'] ?? ''); ?>" pattern="^[0-9]{5}-[0-9]{7}-[0-9]$">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="<?php echo e($user['address'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?php echo e($user['city'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="<?php echo url('parent/profile.php'); ?>" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

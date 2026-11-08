<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_parent();

$page_title = 'Edit Child';
$parent_id = current_user_id();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0 || !parent_owns_child($conn, $id, $parent_id)) {
    flash('error', 'Child not found or access denied.');
    redirect('parent/children.php');
}

$stmt = mysqli_prepare($conn, "SELECT * FROM children WHERE id = ? AND parent_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $parent_id);
mysqli_stmt_execute($stmt);
$child = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Invalid security token.';
    } else {
        $full_name = trim($_POST['full_name'] ?? '');
        $date_of_birth = $_POST['date_of_birth'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $blood_group = trim($_POST['blood_group'] ?? '');
        $birth_certificate_no = trim($_POST['birth_certificate_no'] ?? '');

        if (strlen($full_name) < 2) $errors[] = 'Name is required.';
        if (empty($date_of_birth)) $errors[] = 'Date of birth is required.';
        if (!in_array($gender, ['male', 'female', 'other'])) $errors[] = 'Gender is required.';

        if (empty($errors)) {
            $stmt = mysqli_prepare($conn, "UPDATE children SET full_name=?, date_of_birth=?, gender=?, blood_group=?, birth_certificate_no=? WHERE id=? AND parent_id=?");
            mysqli_stmt_bind_param($stmt, "sssssii", $full_name, $date_of_birth, $gender, $blood_group, $birth_certificate_no, $id, $parent_id);
            if (mysqli_stmt_execute($stmt)) {
                flash('success', 'Child updated successfully.');
                redirect('parent/children.php');
            } else {
                $errors[] = 'Update failed.';
            }
            mysqli_stmt_close($stmt);
        }
        $child = array_merge($child, compact('full_name', 'date_of_birth', 'gender', 'blood_group', 'birth_certificate_no'));
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Edit Child</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('parent/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo url('parent/children.php'); ?>">Children</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
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
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo e($child['full_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth *</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?php echo e($child['date_of_birth']); ?>" required max="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender *</label>
                        <select name="gender" class="form-select" required>
                            <option value="male" <?php echo $child['gender']==='male'?'selected':''; ?>>Male</option>
                            <option value="female" <?php echo $child['gender']==='female'?'selected':''; ?>>Female</option>
                            <option value="other" <?php echo $child['gender']==='other'?'selected':''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-select">
                            <option value="">Select</option>
                            <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                                <option value="<?php echo $bg; ?>" <?php echo ($child['blood_group']??'')===$bg?'selected':''; ?>><?php echo $bg; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Birth Certificate No.</label>
                        <input type="text" name="birth_certificate_no" class="form-control" value="<?php echo e($child['birth_certificate_no'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="<?php echo url('parent/children.php'); ?>" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

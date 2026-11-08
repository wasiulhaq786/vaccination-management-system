<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_parent();

$page_title = 'Add Child';
$parent_id = current_user_id();
$errors = [];
$data = ['full_name' => '', 'date_of_birth' => '', 'gender' => '', 'blood_group' => '', 'birth_certificate_no' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Invalid security token.';
    } else {
        $data['full_name'] = trim($_POST['full_name'] ?? '');
        $data['date_of_birth'] = $_POST['date_of_birth'] ?? '';
        $data['gender'] = $_POST['gender'] ?? '';
        $data['blood_group'] = trim($_POST['blood_group'] ?? '');
        $data['birth_certificate_no'] = trim($_POST['birth_certificate_no'] ?? '');

        if (strlen($data['full_name']) < 2) $errors[] = 'Child name is required.';
        if (empty($data['date_of_birth']) || strtotime($data['date_of_birth']) > time()) $errors[] = 'Valid date of birth is required.';
        if (!in_array($data['gender'], ['male', 'female', 'other'])) $errors[] = 'Gender is required.';

        if (empty($errors)) {
            $stmt = mysqli_prepare($conn, "INSERT INTO children (parent_id, full_name, date_of_birth, gender, blood_group, birth_certificate_no) VALUES (?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, "isssss", $parent_id, $data['full_name'], $data['date_of_birth'], $data['gender'], $data['blood_group'], $data['birth_certificate_no']);
            if (mysqli_stmt_execute($stmt)) {
                flash('success', 'Child added successfully.');
                redirect('parent/children.php');
            } else {
                $errors[] = 'Failed to add child.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Add Child</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('parent/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo url('parent/children.php'); ?>">Children</a></li>
                <li class="breadcrumb-item active">Add</li>
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
                        <input type="text" name="full_name" class="form-control" value="<?php echo e($data['full_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth *</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?php echo e($data['date_of_birth']); ?>" required max="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender *</label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select</option>
                            <option value="male" <?php echo $data['gender']==='male'?'selected':''; ?>>Male</option>
                            <option value="female" <?php echo $data['gender']==='female'?'selected':''; ?>>Female</option>
                            <option value="other" <?php echo $data['gender']==='other'?'selected':''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-select">
                            <option value="">Select</option>
                            <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                                <option value="<?php echo $bg; ?>" <?php echo $data['blood_group']===$bg?'selected':''; ?>><?php echo $bg; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Birth Certificate No.</label>
                        <input type="text" name="birth_certificate_no" class="form-control" value="<?php echo e($data['birth_certificate_no']); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Child</button>
                    <a href="<?php echo url('parent/children.php'); ?>" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

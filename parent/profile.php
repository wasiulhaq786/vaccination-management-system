<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_parent();

$page_title = 'My Profile';
$parent_id = current_user_id();

$stmt = mysqli_prepare($conn, "SELECT * FROM parents WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $parent_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>My Profile</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('parent/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?php echo url('parent/edit-profile.php'); ?>" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Edit Profile</a>
        <a href="<?php echo url('parent/change-password.php'); ?>" class="btn btn-outline-secondary"><i class="fas fa-key me-1"></i>Change Password</a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <p><strong>Full Name:</strong> <?php echo e($user['full_name']); ?></p>
                <p><strong>Email:</strong> <?php echo e($user['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo e($user['phone']); ?></p>
                <p><strong>CNIC:</strong> <?php echo e($user['cnic'] ?: 'N/A'); ?></p>
                <p><strong>Address:</strong> <?php echo e($user['address'] ?: 'N/A'); ?></p>
                <p><strong>City:</strong> <?php echo e($user['city'] ?: 'N/A'); ?></p>
                <p><strong>Member Since:</strong> <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

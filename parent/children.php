<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_parent();

$page_title = 'My Children';
$parent_id = current_user_id();

$children = [];
$stmt = mysqli_prepare($conn, "SELECT * FROM children WHERE parent_id = ? ORDER BY full_name");
mysqli_stmt_bind_param($stmt, "i", $parent_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($r = mysqli_fetch_assoc($res)) $children[] = $r;
mysqli_stmt_close($stmt);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>My Children</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('parent/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Children</li>
            </ol>
        </nav>
    </div>
    <a href="<?php echo url('parent/add-child.php'); ?>" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Child</a>
</div>

<div class="row g-3">
    <?php if (empty($children)): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body empty-state">
                    <i class="fas fa-child"></i>
                    <p>No children registered yet.</p>
                    <a href="<?php echo url('parent/add-child.php'); ?>" class="btn btn-primary">Add Your First Child</a>
                </div>
            </div>
        </div>
    <?php else: foreach ($children as $c): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?php echo e($c['full_name']); ?></h5>
                    <p class="mb-1"><i class="fas fa-birthday-cake me-2 text-muted"></i><?php echo date('d M Y', strtotime($c['date_of_birth'])); ?></p>
                    <p class="mb-1"><i class="fas fa-venus-mars me-2 text-muted"></i><?php echo ucfirst(e($c['gender'])); ?></p>
                    <p class="mb-3"><i class="fas fa-tint me-2 text-muted"></i><?php echo e($c['blood_group'] ?: 'N/A'); ?></p>
                    <div class="d-flex gap-2">
                        <a href="<?php echo url('parent/edit-child.php?id=' . $c['id']); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Edit</a>
                        <a href="<?php echo url('parent/vaccination-status.php?child_id=' . $c['id']); ?>" class="btn btn-sm btn-outline-teal"><i class="fas fa-syringe"></i> Status</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

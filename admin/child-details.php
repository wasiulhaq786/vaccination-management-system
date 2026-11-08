<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    flash('error', 'Invalid child ID.');
    redirect('admin/children.php');
}

$stmt = mysqli_prepare($conn, "SELECT c.*, p.full_name AS parent_name, p.email AS parent_email, p.phone AS parent_phone, p.address AS parent_address, p.city AS parent_city, p.cnic AS parent_cnic
    FROM children c JOIN parents p ON c.parent_id = p.id WHERE c.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$child = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$child) {
    flash('error', 'Child not found.');
    redirect('admin/children.php');
}

// Vaccination records
$records = [];
$stmt = mysqli_prepare($conn, "SELECT vr.*, v.name AS vaccine_name, h.hospital_name
    FROM vaccination_records vr
    JOIN vaccines v ON vr.vaccine_id = v.id
    JOIN hospitals h ON vr.hospital_id = h.id
    WHERE vr.child_id = ? ORDER BY vr.vaccination_date DESC");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) $records[] = $row;
mysqli_stmt_close($stmt);

// Appointments
$appointments = [];
$stmt = mysqli_prepare($conn, "SELECT a.*, v.name AS vaccine_name, h.hospital_name
    FROM appointments a
    JOIN vaccines v ON a.vaccine_id = v.id
    JOIN hospitals h ON a.hospital_id = h.id
    WHERE a.child_id = ? ORDER BY a.appointment_date DESC");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) $appointments[] = $row;
mysqli_stmt_close($stmt);

$page_title = 'Child Details - ' . $child['full_name'];
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><?php echo e($child['full_name']); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('admin/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo url('admin/children.php'); ?>">Children</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </nav>
    </div>
    <a href="<?php echo url('admin/children.php'); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Child Information</div>
            <div class="card-body">
                <p><strong>Name:</strong> <?php echo e($child['full_name']); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo date('d M Y', strtotime($child['date_of_birth'])); ?></p>
                <p><strong>Gender:</strong> <?php echo ucfirst(e($child['gender'])); ?></p>
                <p><strong>Blood Group:</strong> <?php echo e($child['blood_group'] ?: 'N/A'); ?></p>
                <p><strong>Status:</strong> <span class="badge badge-status badge-<?php echo e($child['status']); ?>"><?php echo ucfirst(e($child['status'])); ?></span></p>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">Parent Information</div>
            <div class="card-body">
                <p><strong>Name:</strong> <?php echo e($child['parent_name']); ?></p>
                <p><strong>Email:</strong> <?php echo e($child['parent_email']); ?></p>
                <p><strong>Phone:</strong> <?php echo e($child['parent_phone']); ?></p>
                <p><strong>CNIC:</strong> <?php echo e($child['parent_cnic'] ?: 'N/A'); ?></p>
                <p><strong>Address:</strong> <?php echo e($child['parent_address'] ?: 'N/A'); ?>, <?php echo e($child['parent_city'] ?: ''); ?></p>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">Vaccination History</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr><th>Vaccine</th><th>Date</th><th>Dose</th><th>Hospital</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr><td colspan="5" class="text-center text-muted">No vaccination records</td></tr>
                        <?php else: foreach ($records as $r): ?>
                            <tr>
                                <td><?php echo e($r['vaccine_name']); ?></td>
                                <td><?php echo date('d M Y', strtotime($r['vaccination_date'])); ?></td>
                                <td><?php echo (int)$r['dose_number']; ?></td>
                                <td><?php echo e($r['hospital_name']); ?></td>
                                <td><span class="badge badge-status badge-completed"><?php echo ucfirst(e($r['status'])); ?></span></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Appointments</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr><th>Vaccine</th><th>Hospital</th><th>Date</th><th>Time</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments)): ?>
                            <tr><td colspan="5" class="text-center text-muted">No appointments</td></tr>
                        <?php else: foreach ($appointments as $a): ?>
                            <tr>
                                <td><?php echo e($a['vaccine_name']); ?></td>
                                <td><?php echo e($a['hospital_name']); ?></td>
                                <td><?php echo date('d M Y', strtotime($a['appointment_date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($a['appointment_time'])); ?></td>
                                <td><span class="badge badge-status badge-<?php echo e($a['status']); ?>"><?php echo ucfirst(e($a['status'])); ?></span></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

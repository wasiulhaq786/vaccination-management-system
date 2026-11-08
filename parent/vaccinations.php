<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_parent();

$page_title = 'Vaccination Details';
$parent_id = current_user_id();

$records = [];
$stmt = mysqli_prepare($conn, "SELECT vr.*, c.full_name AS child_name, v.name AS vaccine_name, h.hospital_name
    FROM vaccination_records vr
    JOIN children c ON vr.child_id = c.id
    JOIN vaccines v ON vr.vaccine_id = v.id
    JOIN hospitals h ON vr.hospital_id = h.id
    WHERE c.parent_id = ?
    ORDER BY vr.vaccination_date DESC");
mysqli_stmt_bind_param($stmt, "i", $parent_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($r = mysqli_fetch_assoc($res)) $records[] = $r;
mysqli_stmt_close($stmt);

$appointments = [];
$stmt = mysqli_prepare($conn, "SELECT a.*, c.full_name AS child_name, v.name AS vaccine_name, h.hospital_name
    FROM appointments a
    JOIN children c ON a.child_id = c.id
    JOIN vaccines v ON a.vaccine_id = v.id
    JOIN hospitals h ON a.hospital_id = h.id
    WHERE a.parent_id = ? AND a.status IN ('pending','approved')
    ORDER BY a.appointment_date ASC");
mysqli_stmt_bind_param($stmt, "i", $parent_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($r = mysqli_fetch_assoc($res)) $appointments[] = $r;
mysqli_stmt_close($stmt);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Vaccination Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('parent/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Vaccinations</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Completed Vaccinations</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Child</th><th>Vaccine</th><th>Date</th><th>Dose</th><th>Hospital</th></tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">No completed vaccinations yet</td></tr>
                <?php else: foreach ($records as $r): ?>
                    <tr>
                        <td><?php echo e($r['child_name']); ?></td>
                        <td><?php echo e($r['vaccine_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($r['vaccination_date'])); ?></td>
                        <td><?php echo (int)$r['dose_number']; ?></td>
                        <td><?php echo e($r['hospital_name']); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Upcoming / Pending Appointments</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Child</th><th>Vaccine</th><th>Hospital</th><th>Date</th><th>Time</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php if (empty($appointments)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">No upcoming appointments</td></tr>
                <?php else: foreach ($appointments as $a): ?>
                    <tr>
                        <td><?php echo e($a['child_name']); ?></td>
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

<?php include __DIR__ . '/../includes/footer.php'; ?>

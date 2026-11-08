<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_hospital();

$page_title = 'Hospital Dashboard';
$hospital_id = current_user_id();

$stats = [];
$q = [
    'total' => "SELECT COUNT(*) AS c FROM appointments WHERE hospital_id = $hospital_id",
    'today' => "SELECT COUNT(*) AS c FROM appointments WHERE hospital_id = $hospital_id AND appointment_date = CURDATE()",
    'pending' => "SELECT COUNT(*) AS c FROM appointments WHERE hospital_id = $hospital_id AND status IN ('pending','approved')",
    'completed' => "SELECT COUNT(*) AS c FROM appointments WHERE hospital_id = $hospital_id AND status = 'completed'"
];
foreach ($q as $k => $sql) {
    $res = mysqli_query($conn, $sql);
    $stats[$k] = ($r = mysqli_fetch_assoc($res)) ? (int)$r['c'] : 0;
}

$recent = [];
$stmt = mysqli_prepare($conn, "SELECT a.*, c.full_name AS child_name, p.full_name AS parent_name, v.name AS vaccine_name
    FROM appointments a
    JOIN children c ON a.child_id = c.id
    JOIN parents p ON a.parent_id = p.id
    JOIN vaccines v ON a.vaccine_id = v.id
    WHERE a.hospital_id = ?
    ORDER BY a.appointment_date DESC LIMIT 8");
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($r = mysqli_fetch_assoc($res)) $recent[] = $r;
mysqli_stmt_close($stmt);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active">Hospital Dashboard</li></ol></nav>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="d-flex align-items-center gap-3"><div class="stat-icon bg-primary"><i class="fas fa-calendar-check"></i></div><div><p class="stat-value"><?php echo $stats['total']; ?></p><p class="stat-label">Total Bookings</p></div></div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="d-flex align-items-center gap-3"><div class="stat-icon bg-teal"><i class="fas fa-calendar-day"></i></div><div><p class="stat-value"><?php echo $stats['today']; ?></p><p class="stat-label">Today's Appointments</p></div></div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="d-flex align-items-center gap-3"><div class="stat-icon bg-warning"><i class="fas fa-clock"></i></div><div><p class="stat-value"><?php echo $stats['pending']; ?></p><p class="stat-label">Pending</p></div></div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="d-flex align-items-center gap-3"><div class="stat-icon bg-success"><i class="fas fa-check-circle"></i></div><div><p class="stat-value"><?php echo $stats['completed']; ?></p><p class="stat-label">Completed</p></div></div></div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between"><span>Recent Bookings</span><a href="<?php echo url('hospital/bookings.php'); ?>" class="btn btn-sm btn-outline-primary">View All</a></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Child</th><th>Parent</th><th>Vaccine</th><th>Date</th><th>Status</th></tr></thead>
            <tbody>
                <?php if (empty($recent)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No bookings yet</td></tr>
                <?php else: foreach ($recent as $r): ?>
                    <tr>
                        <td><?php echo e($r['child_name']); ?></td>
                        <td><?php echo e($r['parent_name']); ?></td>
                        <td><?php echo e($r['vaccine_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($r['appointment_date'])); ?></td>
                        <td><span class="badge badge-status badge-<?php echo e($r['status']); ?>"><?php echo ucfirst(e($r['status'])); ?></span></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

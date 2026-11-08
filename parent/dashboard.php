<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_parent();

$page_title = 'Parent Dashboard';
$parent_id = current_user_id();

$stats = [];
$q = [
    'children' => "SELECT COUNT(*) AS c FROM children WHERE parent_id = $parent_id AND status='active'",
    'upcoming' => "SELECT COUNT(*) AS c FROM appointments WHERE parent_id = $parent_id AND appointment_date >= CURDATE() AND status IN ('pending','approved')",
    'completed' => "SELECT COUNT(*) AS c FROM vaccination_records vr JOIN children c ON vr.child_id = c.id WHERE c.parent_id = $parent_id",
    'pending' => "SELECT COUNT(*) AS c FROM appointments WHERE parent_id = $parent_id AND status = 'pending'"
];
foreach ($q as $k => $sql) {
    $res = mysqli_query($conn, $sql);
    $stats[$k] = ($r = mysqli_fetch_assoc($res)) ? (int)$r['c'] : 0;
}

$recent = [];
$stmt = mysqli_prepare($conn, "SELECT a.*, c.full_name AS child_name, v.name AS vaccine_name, h.hospital_name
    FROM appointments a
    JOIN children c ON a.child_id = c.id
    JOIN vaccines v ON a.vaccine_id = v.id
    JOIN hospitals h ON a.hospital_id = h.id
    WHERE a.parent_id = ?
    ORDER BY a.appointment_date DESC LIMIT 6");
mysqli_stmt_bind_param($stmt, "i", $parent_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($r = mysqli_fetch_assoc($res)) $recent[] = $r;
mysqli_stmt_close($stmt);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Welcome, <?php echo e($_SESSION['user_name']); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary"><i class="fas fa-child"></i></div>
                <div>
                    <p class="stat-value"><?php echo $stats['children']; ?></p>
                    <p class="stat-label">My Children</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-teal"><i class="fas fa-calendar-alt"></i></div>
                <div>
                    <p class="stat-value"><?php echo $stats['upcoming']; ?></p>
                    <p class="stat-label">Upcoming</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success"><i class="fas fa-check-circle"></i></div>
                <div>
                    <p class="stat-value"><?php echo $stats['completed']; ?></p>
                    <p class="stat-label">Completed</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning"><i class="fas fa-clock"></i></div>
                <div>
                    <p class="stat-value"><?php echo $stats['pending']; ?></p>
                    <p class="stat-label">Pending</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <a href="<?php echo url('parent/add-child.php'); ?>" class="btn btn-primary w-100 py-3"><i class="fas fa-plus me-2"></i>Add Child</a>
    </div>
    <div class="col-md-6">
        <a href="<?php echo url('parent/book-appointment.php'); ?>" class="btn btn-teal w-100 py-3"><i class="fas fa-calendar-plus me-2"></i>Book Appointment</a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Recent Appointments</span>
        <a href="<?php echo url('parent/vaccination-status.php'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Child</th><th>Vaccine</th><th>Hospital</th><th>Date</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php if (empty($recent)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No appointments yet. <a href="<?php echo url('parent/book-appointment.php'); ?>">Book one</a></td></tr>
                <?php else: foreach ($recent as $r): ?>
                    <tr>
                        <td><?php echo e($r['child_name']); ?></td>
                        <td><?php echo e($r['vaccine_name']); ?></td>
                        <td><?php echo e($r['hospital_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($r['appointment_date'])); ?></td>
                        <td><span class="badge badge-status badge-<?php echo e($r['status']); ?>"><?php echo ucfirst(e($r['status'])); ?></span></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

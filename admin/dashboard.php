<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$page_title = 'Admin Dashboard';

// Stats
$stats = [];
$queries = [
    'children' => "SELECT COUNT(*) AS c FROM children WHERE status='active'",
    'parents' => "SELECT COUNT(*) AS c FROM parents WHERE status='active'",
    'hospitals' => "SELECT COUNT(*) AS c FROM hospitals WHERE status='active'",
    'vaccines' => "SELECT COUNT(*) AS c FROM vaccines WHERE status='active'",
    'pending_appointments' => "SELECT COUNT(*) AS c FROM appointments WHERE status='pending'",
    'today_vaccinations' => "SELECT COUNT(*) AS c FROM appointments WHERE appointment_date = CURDATE() AND status IN ('approved','completed')",
    'pending_requests' => "SELECT COUNT(*) AS c FROM vaccination_requests WHERE status='pending'",
    'completed' => "SELECT COUNT(*) AS c FROM vaccination_records"
];
foreach ($queries as $key => $sql) {
    $res = mysqli_query($conn, $sql);
    $stats[$key] = ($row = mysqli_fetch_assoc($res)) ? (int)$row['c'] : 0;
}

// Recent appointments
$recent = [];
$res = mysqli_query($conn, "SELECT a.*, c.full_name AS child_name, p.full_name AS parent_name, v.name AS vaccine_name, h.hospital_name
    FROM appointments a
    JOIN children c ON a.child_id = c.id
    JOIN parents p ON a.parent_id = p.id
    JOIN vaccines v ON a.vaccine_id = v.id
    JOIN hospitals h ON a.hospital_id = h.id
    ORDER BY a.created_at DESC LIMIT 8");
while ($row = mysqli_fetch_assoc($res)) {
    $recent[] = $row;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Admin Dashboard</li>
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
                    <p class="stat-label">Children</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-teal"><i class="fas fa-users"></i></div>
                <div>
                    <p class="stat-value"><?php echo $stats['parents']; ?></p>
                    <p class="stat-label">Parents</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success"><i class="fas fa-hospital"></i></div>
                <div>
                    <p class="stat-value"><?php echo $stats['hospitals']; ?></p>
                    <p class="stat-label">Hospitals</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-info"><i class="fas fa-vial"></i></div>
                <div>
                    <p class="stat-value"><?php echo $stats['vaccines']; ?></p>
                    <p class="stat-label">Vaccines</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning"><i class="fas fa-clock"></i></div>
                <div>
                    <p class="stat-value"><?php echo $stats['pending_appointments']; ?></p>
                    <p class="stat-label">Pending Bookings</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary"><i class="fas fa-calendar-day"></i></div>
                <div>
                    <p class="stat-value"><?php echo $stats['today_vaccinations']; ?></p>
                    <p class="stat-label">Today's Appointments</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger"><i class="fas fa-inbox"></i></div>
                <div>
                    <p class="stat-value"><?php echo $stats['pending_requests']; ?></p>
                    <p class="stat-label">Pending Requests</p>
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
                    <p class="stat-label">Completed Vaccinations</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-calendar-check me-2"></i>Recent Appointments</span>
        <a href="<?php echo url('admin/bookings.php'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Child</th>
                    <th>Parent</th>
                    <th>Vaccine</th>
                    <th>Hospital</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No appointments yet</td></tr>
                <?php else: foreach ($recent as $r): ?>
                    <tr>
                        <td><?php echo e($r['child_name']); ?></td>
                        <td><?php echo e($r['parent_name']); ?></td>
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

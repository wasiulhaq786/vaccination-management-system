<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$page_title = 'Vaccination Dates';

$filter = $_GET['filter'] ?? 'today';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$where = "1=1";
$params = [];
$types = '';

if ($filter === 'today') {
    $where = "a.appointment_date = CURDATE()";
} elseif ($filter === 'upcoming') {
    $where = "a.appointment_date > CURDATE() AND a.status IN ('pending','approved')";
} elseif ($filter === 'due') {
    $where = "a.appointment_date <= CURDATE() AND a.status IN ('pending','approved')";
} elseif ($filter === 'custom' && $date_from && $date_to) {
    $where = "a.appointment_date BETWEEN ? AND ?";
    $params = [$date_from, $date_to];
    $types = 'ss';
}

$sql = "SELECT a.*, c.full_name AS child_name, p.full_name AS parent_name, v.name AS vaccine_name, h.hospital_name
        FROM appointments a
        JOIN children c ON a.child_id = c.id
        JOIN parents p ON a.parent_id = p.id
        JOIN vaccines v ON a.vaccine_id = v.id
        JOIN hospitals h ON a.hospital_id = h.id
        WHERE $where
        ORDER BY a.appointment_date ASC, a.appointment_time ASC";

$stmt = mysqli_prepare($conn, $sql);
if ($types) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
mysqli_stmt_close($stmt);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Vaccination Dates</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('admin/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Vaccination Dates</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Quick Filter</label>
                <select name="filter" class="form-select" onchange="this.form.submit()">
                    <option value="today" <?php echo $filter==='today'?'selected':''; ?>>Today</option>
                    <option value="upcoming" <?php echo $filter==='upcoming'?'selected':''; ?>>Upcoming</option>
                    <option value="due" <?php echo $filter==='due'?'selected':''; ?>>Due</option>
                    <option value="custom" <?php echo $filter==='custom'?'selected':''; ?>>Custom Range</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo e($date_from); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo e($date_to); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Appointments (<?php echo count($rows); ?>)</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Child</th>
                    <th>Parent</th>
                    <th>Vaccine</th>
                    <th>Hospital</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No records found</td></tr>
                <?php else: foreach ($rows as $r): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($r['appointment_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($r['appointment_time'])); ?></td>
                        <td><?php echo e($r['child_name']); ?></td>
                        <td><?php echo e($r['parent_name']); ?></td>
                        <td><?php echo e($r['vaccine_name']); ?></td>
                        <td><?php echo e($r['hospital_name']); ?></td>
                        <td><span class="badge badge-status badge-<?php echo e($r['status']); ?>"><?php echo ucfirst(e($r['status'])); ?></span></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

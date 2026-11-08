<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_hospital();

$page_title = 'Hospital Bookings';
$hospital_id = current_user_id();

$status_filter = $_GET['status'] ?? '';
$where = "a.hospital_id = ?";
$params = [$hospital_id];
$types = "i";
if ($status_filter) {
    $where .= " AND a.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$sql = "SELECT a.*, c.full_name AS child_name, p.full_name AS parent_name, p.phone AS parent_phone, v.name AS vaccine_name
        FROM appointments a
        JOIN children c ON a.child_id = c.id
        JOIN parents p ON a.parent_id = p.id
        JOIN vaccines v ON a.vaccine_id = v.id
        WHERE $where
        ORDER BY a.appointment_date DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$bookings = [];
while ($r = mysqli_fetch_assoc($result)) $bookings[] = $r;
mysqli_stmt_close($stmt);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Bookings</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo url('hospital/dashboard.php'); ?>">Dashboard</a></li><li class="breadcrumb-item active">Bookings</li></ol></nav>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <?php foreach (['pending','approved','completed','rejected','cancelled'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $status_filter===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Date / Time</th><th>Child</th><th>Parent</th><th>Vaccine</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No bookings found</td></tr>
                <?php else: foreach ($bookings as $b): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($b['appointment_date'])); ?><br><small><?php echo date('h:i A', strtotime($b['appointment_time'])); ?></small></td>
                        <td><?php echo e($b['child_name']); ?></td>
                        <td><?php echo e($b['parent_name']); ?><br><small><?php echo e($b['parent_phone']); ?></small></td>
                        <td><?php echo e($b['vaccine_name']); ?></td>
                        <td><span class="badge badge-status badge-<?php echo e($b['status']); ?>"><?php echo ucfirst(e($b['status'])); ?></span></td>
                        <td>
                            <?php if (in_array($b['status'], ['pending','approved'])): ?>
                                <a href="<?php echo url('hospital/vaccination-status.php?id=' . $b['id']); ?>" class="btn btn-sm btn-success"><i class="fas fa-syringe"></i> Update</a>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

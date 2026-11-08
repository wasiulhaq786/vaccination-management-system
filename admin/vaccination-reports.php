<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$page_title = 'Vaccination Reports';

$search = trim($_GET['search'] ?? '');
$child_id = (int)($_GET['child_id'] ?? 0);
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$where = ["1=1"];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = "(c.full_name LIKE ? OR p.full_name LIKE ? OR v.name LIKE ?)";
    $s = '%' . $search . '%';
    $params[] = $s; $params[] = $s; $params[] = $s;
    $types .= 'sss';
}
if ($child_id > 0) {
    $where[] = "vr.child_id = ?";
    $params[] = $child_id;
    $types .= 'i';
}
if ($date_from && $date_to) {
    $where[] = "vr.vaccination_date BETWEEN ? AND ?";
    $params[] = $date_from; $params[] = $date_to;
    $types .= 'ss';
}

$sql = "SELECT vr.*, c.full_name AS child_name, c.date_of_birth, p.full_name AS parent_name, p.phone AS parent_phone,
               v.name AS vaccine_name, h.hospital_name
        FROM vaccination_records vr
        JOIN children c ON vr.child_id = c.id
        JOIN parents p ON c.parent_id = p.id
        JOIN vaccines v ON vr.vaccine_id = v.id
        JOIN hospitals h ON vr.hospital_id = h.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY vr.vaccination_date DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($types) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$records = [];
while ($r = mysqli_fetch_assoc($result)) $records[] = $r;
mysqli_stmt_close($stmt);

// Children for filter
$children_list = [];
$res = mysqli_query($conn, "SELECT id, full_name FROM children ORDER BY full_name");
while ($r = mysqli_fetch_assoc($res)) $children_list[] = $r;

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Vaccination Reports</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('admin/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Reports</li>
            </ol>
        </nav>
    </div>
    <button onclick="window.print()" class="btn btn-outline-primary no-print"><i class="fas fa-print me-1"></i>Print</button>
</div>

<div class="card mb-4 no-print">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" value="<?php echo e($search); ?>" placeholder="Child, parent or vaccine">
            </div>
            <div class="col-md-2">
                <label class="form-label">Child</label>
                <select name="child_id" class="form-select">
                    <option value="">All</option>
                    <?php foreach ($children_list as $ch): ?>
                        <option value="<?php echo $ch['id']; ?>" <?php echo $child_id==$ch['id']?'selected':''; ?>><?php echo e($ch['full_name']); ?></option>
                    <?php endforeach; ?>
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
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Complete Vaccination History (<?php echo count($records); ?>)</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Child</th>
                    <th>DOB</th>
                    <th>Parent</th>
                    <th>Vaccine</th>
                    <th>Dose</th>
                    <th>Hospital</th>
                    <th>Administered By</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No records found</td></tr>
                <?php else: foreach ($records as $r): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($r['vaccination_date'])); ?></td>
                        <td><?php echo e($r['child_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($r['date_of_birth'])); ?></td>
                        <td><?php echo e($r['parent_name']); ?><br><small><?php echo e($r['parent_phone']); ?></small></td>
                        <td><?php echo e($r['vaccine_name']); ?></td>
                        <td><?php echo (int)$r['dose_number']; ?></td>
                        <td><?php echo e($r['hospital_name']); ?></td>
                        <td><?php echo e($r['administered_by'] ?: 'N/A'); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

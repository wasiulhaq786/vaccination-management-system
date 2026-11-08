<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$page_title = 'Child Details';

$search = trim($_GET['search'] ?? '');
$gender = $_GET['gender'] ?? '';
$status = $_GET['status'] ?? '';

$where = ["1=1"];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = "(c.full_name LIKE ? OR p.full_name LIKE ? OR p.email LIKE ?)";
    $s = '%' . $search . '%';
    $params[] = $s; $params[] = $s; $params[] = $s;
    $types .= 'sss';
}
if ($gender !== '') {
    $where[] = "c.gender = ?";
    $params[] = $gender;
    $types .= 's';
}
if ($status !== '') {
    $where[] = "c.status = ?";
    $params[] = $status;
    $types .= 's';
}

$sql = "SELECT c.*, p.full_name AS parent_name, p.email AS parent_email, p.phone AS parent_phone
        FROM children c
        JOIN parents p ON c.parent_id = p.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY c.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($types) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$children = [];
while ($row = mysqli_fetch_assoc($result)) {
    $children[] = $row;
}
mysqli_stmt_close($stmt);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Child Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('admin/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Children</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" value="<?php echo e($search); ?>" placeholder="Child or parent name/email">
            </div>
            <div class="col-md-2">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">All</option>
                    <option value="male" <?php echo $gender==='male'?'selected':''; ?>>Male</option>
                    <option value="female" <?php echo $gender==='female'?'selected':''; ?>>Female</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" <?php echo $status==='active'?'selected':''; ?>>Active</option>
                    <option value="inactive" <?php echo $status==='inactive'?'selected':''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Filter</button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo url('admin/children.php'); ?>" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">All Children (<?php echo count($children); ?>)</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Child Name</th>
                    <th>DOB</th>
                    <th>Gender</th>
                    <th>Parent</th>
                    <th>Parent Contact</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($children)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No children found</td></tr>
                <?php else: foreach ($children as $c): ?>
                    <tr>
                        <td><?php echo (int)$c['id']; ?></td>
                        <td><?php echo e($c['full_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($c['date_of_birth'])); ?></td>
                        <td><?php echo ucfirst(e($c['gender'])); ?></td>
                        <td><?php echo e($c['parent_name']); ?></td>
                        <td><?php echo e($c['parent_phone']); ?><br><small class="text-muted"><?php echo e($c['parent_email']); ?></small></td>
                        <td><span class="badge badge-status badge-<?php echo e($c['status']); ?>"><?php echo ucfirst(e($c['status'])); ?></span></td>
                        <td>
                            <a href="<?php echo url('admin/child-details.php?id=' . $c['id']); ?>" class="btn btn-sm btn-outline-primary" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

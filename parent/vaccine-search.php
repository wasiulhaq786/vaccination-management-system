<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_parent();

$page_title = 'Search Vaccines';
$search = trim($_GET['q'] ?? '');

$where = "status = 'active'";
$params = [];
$types = '';
if ($search) {
    $where .= " AND (name LIKE ? OR description LIKE ? OR recommended_age LIKE ?)";
    $s = '%' . $search . '%';
    $params = [$s, $s, $s];
    $types = 'sss';
}

$sql = "SELECT * FROM vaccines WHERE $where ORDER BY name";
$stmt = mysqli_prepare($conn, $sql);
if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vaccines = [];
while ($r = mysqli_fetch_assoc($result)) $vaccines[] = $r;
mysqli_stmt_close($stmt);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Search Vaccines</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('parent/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Vaccines</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-9">
                <input type="text" name="q" class="form-control" value="<?php echo e($search); ?>" placeholder="Search by name, description or age...">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Search</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <?php if (empty($vaccines)): ?>
        <div class="col-12">
            <div class="card"><div class="card-body empty-state"><i class="fas fa-search"></i><p>No vaccines found</p></div></div>
        </div>
    <?php else: foreach ($vaccines as $v): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0"><?php echo e($v['name']); ?></h5>
                        <span class="badge badge-status badge-<?php echo e($v['availability']); ?>"><?php echo ucfirst(e($v['availability'])); ?></span>
                    </div>
                    <p class="text-muted small"><?php echo e($v['description'] ?: 'No description'); ?></p>
                    <p class="mb-1"><strong>Recommended:</strong> <?php echo e($v['recommended_age'] ?: 'N/A'); ?></p>
                    <p class="mb-0"><strong>Doses:</strong> <?php echo (int)$v['doses_required']; ?></p>
                </div>
                <div class="card-footer bg-transparent">
                    <?php if ($v['availability'] === 'available'): ?>
                        <a href="<?php echo url('parent/book-appointment.php'); ?>" class="btn btn-sm btn-primary">Book Appointment</a>
                    <?php else: ?>
                        <button class="btn btn-sm btn-secondary" disabled>Unavailable</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

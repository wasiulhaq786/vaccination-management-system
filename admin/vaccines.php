<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$page_title = 'Vaccine Management';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $recommended_age = trim($_POST['recommended_age'] ?? '');
        $doses_required = (int)($_POST['doses_required'] ?? 1);
        $interval_days = (int)($_POST['interval_days'] ?? 0);
        $availability = $_POST['availability'] ?? 'available';
        $status = $_POST['status'] ?? 'active';
        $id = (int)($_POST['id'] ?? 0);

        if (empty($name)) {
            flash('error', 'Vaccine name is required.');
        } else {
            if ($action === 'add') {
                $stmt = mysqli_prepare($conn, "INSERT INTO vaccines (name, description, recommended_age, doses_required, interval_days, availability, status) VALUES (?,?,?,?,?,?,?)");
                mysqli_stmt_bind_param($stmt, "sssiiss", $name, $description, $recommended_age, $doses_required, $interval_days, $availability, $status);
                if (mysqli_stmt_execute($stmt)) {
                    flash('success', 'Vaccine added successfully.');
                } else {
                    flash('error', 'Failed to add vaccine.');
                }
                mysqli_stmt_close($stmt);
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE vaccines SET name=?, description=?, recommended_age=?, doses_required=?, interval_days=?, availability=?, status=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "sssiissi", $name, $description, $recommended_age, $doses_required, $interval_days, $availability, $status, $id);
                if (mysqli_stmt_execute($stmt)) {
                    flash('success', 'Vaccine updated successfully.');
                } else {
                    flash('error', 'Failed to update vaccine.');
                }
                mysqli_stmt_close($stmt);
            }
        }
        redirect('admin/vaccines.php');
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, "DELETE FROM vaccines WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            flash('success', 'Vaccine deleted.');
        } else {
            flash('error', 'Cannot delete. Vaccine may be in use.');
        }
        mysqli_stmt_close($stmt);
        redirect('admin/vaccines.php');
    }
}

$search = trim($_GET['search'] ?? '');
$where = "1=1";
$params = [];
$types = '';
if ($search) {
    $where = "name LIKE ? OR description LIKE ?";
    $s = '%' . $search . '%';
    $params = [$s, $s];
    $types = 'ss';
}

$sql = "SELECT * FROM vaccines WHERE $where ORDER BY name";
$stmt = mysqli_prepare($conn, $sql);
if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vaccines = [];
while ($r = mysqli_fetch_assoc($result)) $vaccines[] = $r;
mysqli_stmt_close($stmt);

$edit = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM vaccines WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $eid);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Vaccine Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('admin/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Vaccines</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vaccineModal" onclick="resetForm()">
        <i class="fas fa-plus me-1"></i>Add Vaccine
    </button>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" value="<?php echo e($search); ?>" placeholder="Search vaccines...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo url('admin/vaccines.php'); ?>" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Recommended Age</th>
                    <th>Doses</th>
                    <th>Availability</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vaccines)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No vaccines found</td></tr>
                <?php else: foreach ($vaccines as $v): ?>
                    <tr>
                        <td>
                            <strong><?php echo e($v['name']); ?></strong>
                            <?php if ($v['description']): ?><br><small class="text-muted"><?php echo e(substr($v['description'], 0, 60)); ?>...</small><?php endif; ?>
                        </td>
                        <td><?php echo e($v['recommended_age']); ?></td>
                        <td><?php echo (int)$v['doses_required']; ?></td>
                        <td><span class="badge badge-status badge-<?php echo e($v['availability']); ?>"><?php echo ucfirst(e($v['availability'])); ?></span></td>
                        <td><span class="badge badge-status badge-<?php echo e($v['status']); ?>"><?php echo ucfirst(e($v['status'])); ?></span></td>
                        <td>
                            <a href="?edit=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#vaccineModal" onclick='fillForm(<?php echo json_encode($v); ?>)'><i class="fas fa-edit"></i></a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this vaccine?')">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $v['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="vaccineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="vaccineForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="formId" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Vaccine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" id="vName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="vDesc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recommended Age</label>
                        <input type="text" name="recommended_age" id="vAge" class="form-control" placeholder="e.g. At birth, 6 weeks">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Doses Required</label>
                            <input type="number" name="doses_required" id="vDoses" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Interval (days)</label>
                            <input type="number" name="interval_days" id="vInterval" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Availability</label>
                            <select name="availability" id="vAvail" class="form-select">
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="vStatus" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('formAction').value = 'add';
    document.getElementById('formId').value = '';
    document.getElementById('modalTitle').textContent = 'Add Vaccine';
    document.getElementById('vaccineForm').reset();
}
function fillForm(v) {
    document.getElementById('formAction').value = 'edit';
    document.getElementById('formId').value = v.id;
    document.getElementById('modalTitle').textContent = 'Edit Vaccine';
    document.getElementById('vName').value = v.name;
    document.getElementById('vDesc').value = v.description || '';
    document.getElementById('vAge').value = v.recommended_age || '';
    document.getElementById('vDoses').value = v.doses_required;
    document.getElementById('vInterval').value = v.interval_days;
    document.getElementById('vAvail').value = v.availability;
    document.getElementById('vStatus').value = v.status;
}
<?php if ($edit): ?>
document.addEventListener('DOMContentLoaded', function() {
    fillForm(<?php echo json_encode($edit); ?>);
    new bootstrap.Modal(document.getElementById('vaccineModal')).show();
});
<?php endif; ?>
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

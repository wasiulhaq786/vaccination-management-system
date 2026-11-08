<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$page_title = 'Hospital Management';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $name = trim($_POST['hospital_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $status = $_POST['status'] ?? 'pending';
        $id = (int)($_POST['id'] ?? 0);
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($phone)) {
            flash('error', 'Name, email and phone are required.');
        } else {
            if ($action === 'add') {
                if (strlen($password) < 6) {
                    flash('error', 'Password must be at least 6 characters.');
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = mysqli_prepare($conn, "INSERT INTO hospitals (hospital_name, email, phone, address, city, password, status) VALUES (?,?,?,?,?,?,?)");
                    mysqli_stmt_bind_param($stmt, "sssssss", $name, $email, $phone, $address, $city, $hash, $status);
                    if (mysqli_stmt_execute($stmt)) {
                        flash('success', 'Hospital added successfully.');
                    } else {
                        flash('error', 'Failed to add. Email may already exist.');
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                if ($password) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = mysqli_prepare($conn, "UPDATE hospitals SET hospital_name=?, email=?, phone=?, address=?, city=?, password=?, status=? WHERE id=?");
                    mysqli_stmt_bind_param($stmt, "sssssssi", $name, $email, $phone, $address, $city, $hash, $status, $id);
                } else {
                    $stmt = mysqli_prepare($conn, "UPDATE hospitals SET hospital_name=?, email=?, phone=?, address=?, city=?, status=? WHERE id=?");
                    mysqli_stmt_bind_param($stmt, "ssssssi", $name, $email, $phone, $address, $city, $status, $id);
                }
                if (mysqli_stmt_execute($stmt)) {
                    flash('success', 'Hospital updated successfully.');
                } else {
                    flash('error', 'Failed to update.');
                }
                mysqli_stmt_close($stmt);
            }
        }
        redirect('admin/hospitals.php');
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, "DELETE FROM hospitals WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            flash('success', 'Hospital deleted.');
        } else {
            flash('error', 'Cannot delete. Hospital may have related records.');
        }
        mysqli_stmt_close($stmt);
        redirect('admin/hospitals.php');
    }

    if ($action === 'status') {
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'active';
        $stmt = mysqli_prepare($conn, "UPDATE hospitals SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        flash('success', 'Status updated.');
        redirect('admin/hospitals.php');
    }
}

$hospitals = [];
$res = mysqli_query($conn, "SELECT * FROM hospitals ORDER BY hospital_name");
while ($r = mysqli_fetch_assoc($res)) $hospitals[] = $r;

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Hospital Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('admin/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Hospitals</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#hospitalModal" onclick="resetHForm()">
        <i class="fas fa-plus me-1"></i>Add Hospital
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($hospitals)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No hospitals found</td></tr>
                <?php else: foreach ($hospitals as $h): ?>
                    <tr>
                        <td><strong><?php echo e($h['hospital_name']); ?></strong><br><small class="text-muted"><?php echo e($h['address']); ?></small></td>
                        <td><?php echo e($h['email']); ?></td>
                        <td><?php echo e($h['phone']); ?></td>
                        <td><?php echo e($h['city']); ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="status">
                                <input type="hidden" name="id" value="<?php echo $h['id']; ?>">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto;">
                                    <option value="active" <?php echo $h['status']==='active'?'selected':''; ?>>Active</option>
                                    <option value="inactive" <?php echo $h['status']==='inactive'?'selected':''; ?>>Inactive</option>
                                    <option value="pending" <?php echo $h['status']==='pending'?'selected':''; ?>>Pending</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick='fillHForm(<?php echo json_encode($h); ?>)' data-bs-toggle="modal" data-bs-target="#hospitalModal"><i class="fas fa-edit"></i></button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this hospital?')">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $h['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="hospitalModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content" id="hForm">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" id="hAction" value="add">
            <input type="hidden" name="id" id="hId" value="">
            <div class="modal-header">
                <h5 class="modal-title" id="hTitle">Add Hospital</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Hospital Name *</label>
                    <input type="text" name="hospital_name" id="hName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" id="hEmail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone *</label>
                    <input type="text" name="phone" id="hPhone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" id="hAddress" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" id="hCity" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password <span id="passHint">(required for new)</span></label>
                    <input type="password" name="password" id="hPass" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" id="hStatus" class="form-select">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function resetHForm() {
    document.getElementById('hAction').value = 'add';
    document.getElementById('hId').value = '';
    document.getElementById('hTitle').textContent = 'Add Hospital';
    document.getElementById('passHint').textContent = '(required for new)';
    document.getElementById('hForm').reset();
}
function fillHForm(h) {
    document.getElementById('hAction').value = 'edit';
    document.getElementById('hId').value = h.id;
    document.getElementById('hTitle').textContent = 'Edit Hospital';
    document.getElementById('passHint').textContent = '(leave blank to keep)';
    document.getElementById('hName').value = h.hospital_name;
    document.getElementById('hEmail').value = h.email;
    document.getElementById('hPhone').value = h.phone;
    document.getElementById('hAddress').value = h.address || '';
    document.getElementById('hCity').value = h.city || '';
    document.getElementById('hStatus').value = h.status;
    document.getElementById('hPass').value = '';
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

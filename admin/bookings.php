<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$page_title = 'Booking Management';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $notes = trim($_POST['admin_notes'] ?? '');
    if ($id > 0 && in_array($status, ['pending','approved','rejected','completed','cancelled'])) {
        $stmt = mysqli_prepare($conn, "UPDATE appointments SET status = ?, admin_notes = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $status, $notes, $id);
        if (mysqli_stmt_execute($stmt)) {
            flash('success', 'Booking status updated.');
        } else {
            flash('error', 'Update failed.');
        }
        mysqli_stmt_close($stmt);
    }
    redirect('admin/bookings.php');
}

$status_filter = $_GET['status'] ?? '';
$where = "1=1";
$params = [];
$types = '';
if ($status_filter) {
    $where = "a.status = ?";
    $params[] = $status_filter;
    $types = 's';
}

$sql = "SELECT a.*, c.full_name AS child_name, p.full_name AS parent_name, p.phone AS parent_phone,
               v.name AS vaccine_name, h.hospital_name
        FROM appointments a
        JOIN children c ON a.child_id = c.id
        JOIN parents p ON a.parent_id = p.id
        JOIN vaccines v ON a.vaccine_id = v.id
        JOIN hospitals h ON a.hospital_id = h.id
        WHERE $where
        ORDER BY a.appointment_date DESC, a.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$bookings = [];
while ($r = mysqli_fetch_assoc($result)) $bookings[] = $r;
mysqli_stmt_close($stmt);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Booking Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('admin/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Bookings</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <?php foreach (['pending','approved','rejected','completed','cancelled'] as $s): ?>
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
                <tr>
                    <th>Date / Time</th>
                    <th>Child</th>
                    <th>Parent</th>
                    <th>Vaccine</th>
                    <th>Hospital</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No bookings found</td></tr>
                <?php else: foreach ($bookings as $b): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($b['appointment_date'])); ?><br><small><?php echo date('h:i A', strtotime($b['appointment_time'])); ?></small></td>
                        <td><?php echo e($b['child_name']); ?></td>
                        <td><?php echo e($b['parent_name']); ?><br><small><?php echo e($b['parent_phone']); ?></small></td>
                        <td><?php echo e($b['vaccine_name']); ?></td>
                        <td><?php echo e($b['hospital_name']); ?></td>
                        <td><span class="badge badge-status badge-<?php echo e($b['status']); ?>"><?php echo ucfirst(e($b['status'])); ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#statusModal"
                                onclick="setBooking(<?php echo $b['id']; ?>, '<?php echo e($b['status']); ?>', '<?php echo e(addslashes($b['admin_notes'] ?? '')); ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" id="bId">
            <div class="modal-header">
                <h5 class="modal-title">Update Booking Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" id="bStatus" class="form-select">
                        <?php foreach (['pending','approved','rejected','completed','cancelled'] as $s): ?>
                            <option value="<?php echo $s; ?>"><?php echo ucfirst($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Admin Notes</label>
                    <textarea name="admin_notes" id="bNotes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function setBooking(id, status, notes) {
    document.getElementById('bId').value = id;
    document.getElementById('bStatus').value = status;
    document.getElementById('bNotes').value = notes;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

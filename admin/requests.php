<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$page_title = 'Parent Requests';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $response = trim($_POST['admin_response'] ?? '');

    if (in_array($action, ['approved', 'rejected']) && $id > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE vaccination_requests SET status = ?, admin_response = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $action, $response, $id);
        if (mysqli_stmt_execute($stmt)) {
            flash('success', 'Request ' . $action . ' successfully.');
        } else {
            flash('error', 'Failed to update request.');
        }
        mysqli_stmt_close($stmt);
    }
    redirect('admin/requests.php');
}

$status_filter = $_GET['status'] ?? '';
$where = "1=1";
$params = [];
$types = '';
if ($status_filter) {
    $where = "vr.status = ?";
    $params[] = $status_filter;
    $types = 's';
}

$sql = "SELECT vr.*, c.full_name AS child_name, p.full_name AS parent_name, p.phone AS parent_phone,
               v.name AS vaccine_name, h.hospital_name
        FROM vaccination_requests vr
        JOIN children c ON vr.child_id = c.id
        JOIN parents p ON vr.parent_id = p.id
        JOIN vaccines v ON vr.vaccine_id = v.id
        LEFT JOIN hospitals h ON vr.hospital_id = h.id
        WHERE $where
        ORDER BY vr.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$requests = [];
while ($r = mysqli_fetch_assoc($result)) $requests[] = $r;
mysqli_stmt_close($stmt);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Parent Requests</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('admin/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Requests</li>
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
                    <option value="pending" <?php echo $status_filter==='pending'?'selected':''; ?>>Pending</option>
                    <option value="approved" <?php echo $status_filter==='approved'?'selected':''; ?>>Approved</option>
                    <option value="rejected" <?php echo $status_filter==='rejected'?'selected':''; ?>>Rejected</option>
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
                    <th>Date</th>
                    <th>Parent</th>
                    <th>Child</th>
                    <th>Vaccine</th>
                    <th>Preferred Date</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No requests found</td></tr>
                <?php else: foreach ($requests as $r): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
                        <td><?php echo e($r['parent_name']); ?><br><small><?php echo e($r['parent_phone']); ?></small></td>
                        <td><?php echo e($r['child_name']); ?></td>
                        <td><?php echo e($r['vaccine_name']); ?></td>
                        <td><?php echo $r['preferred_date'] ? date('d M Y', strtotime($r['preferred_date'])) : 'N/A'; ?></td>
                        <td><?php echo e(substr($r['message'] ?? '', 0, 40)); ?></td>
                        <td><span class="badge badge-status badge-<?php echo e($r['status']); ?>"><?php echo ucfirst(e($r['status'])); ?></span></td>
                        <td>
                            <?php if ($r['status'] === 'pending'): ?>
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#actionModal" onclick="setAction(<?php echo $r['id']; ?>, 'approved')"><i class="fas fa-check"></i></button>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#actionModal" onclick="setAction(<?php echo $r['id']; ?>, 'rejected')"><i class="fas fa-times"></i></button>
                            <?php else: ?>
                                <small class="text-muted"><?php echo e($r['admin_response'] ?? ''); ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="actionModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" id="reqId">
            <input type="hidden" name="action" id="reqAction">
            <div class="modal-header">
                <h5 class="modal-title">Update Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Admin Response / Notes</label>
                    <textarea name="admin_response" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
function setAction(id, action) {
    document.getElementById('reqId').value = id;
    document.getElementById('reqAction').value = action;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

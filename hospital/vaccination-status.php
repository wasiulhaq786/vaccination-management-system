<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_hospital();

$page_title = 'Update Vaccination Status';
$hospital_id = current_user_id();
$id = (int)($_GET['id'] ?? 0);
$errors = [];

if ($id > 0) {
    if (!hospital_owns_appointment($conn, $id, $hospital_id)) {
        flash('error', 'Appointment not found or access denied.');
        redirect('hospital/bookings.php');
    }
    $stmt = mysqli_prepare($conn, "SELECT a.*, c.full_name AS child_name, p.full_name AS parent_name, v.name AS vaccine_name, v.id AS vaccine_id
        FROM appointments a
        JOIN children c ON a.child_id = c.id
        JOIN parents p ON a.parent_id = p.id
        JOIN vaccines v ON a.vaccine_id = v.id
        WHERE a.id = ? AND a.hospital_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $hospital_id);
    mysqli_stmt_execute($stmt);
    $appointment = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $appt_id = (int)($_POST['appointment_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $administered_by = trim($_POST['administered_by'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $vaccination_date = $_POST['vaccination_date'] ?? date('Y-m-d');
    $dose_number = (int)($_POST['dose_number'] ?? 1);

    if (!hospital_owns_appointment($conn, $appt_id, $hospital_id)) {
        flash('error', 'Access denied.');
        redirect('hospital/bookings.php');
    }

    if ($status === 'completed') {
        // Transaction: update appointment + create vaccination record
        mysqli_begin_transaction($conn);
        try {
            $stmt = mysqli_prepare($conn, "UPDATE appointments SET status = 'completed', notes = CONCAT(IFNULL(notes,''), ?) WHERE id = ? AND hospital_id = ?");
            $note_append = $notes ? "\nHospital note: $notes" : '';
            mysqli_stmt_bind_param($stmt, "sii", $note_append, $appt_id, $hospital_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Get appointment details for record
            $stmt = mysqli_prepare($conn, "SELECT child_id, vaccine_id FROM appointments WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $appt_id);
            mysqli_stmt_execute($stmt);
            $appt = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare($conn, "INSERT INTO vaccination_records (child_id, vaccine_id, hospital_id, appointment_id, dose_number, vaccination_date, administered_by, notes, status) VALUES (?,?,?,?,?,?,?,?,'completed')");
            mysqli_stmt_bind_param($stmt, "iiiiisss", $appt['child_id'], $appt['vaccine_id'], $hospital_id, $appt_id, $dose_number, $vaccination_date, $administered_by, $notes);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            mysqli_commit($conn);
            flash('success', 'Vaccination marked as completed. Record created and parent can now see the updated status.');
            redirect('hospital/bookings.php');
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $errors[] = 'Failed to update. Please try again.';
        }
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE appointments SET status = ? WHERE id = ? AND hospital_id = ?");
        mysqli_stmt_bind_param($stmt, "sii", $status, $appt_id, $hospital_id);
        if (mysqli_stmt_execute($stmt)) {
            flash('success', 'Status updated.');
            redirect('hospital/bookings.php');
        } else {
            $errors[] = 'Update failed.';
        }
        mysqli_stmt_close($stmt);
    }
}

// List pending for quick access if no id
$pending = [];
if ($id <= 0) {
    $stmt = mysqli_prepare($conn, "SELECT a.*, c.full_name AS child_name, v.name AS vaccine_name
        FROM appointments a
        JOIN children c ON a.child_id = c.id
        JOIN vaccines v ON a.vaccine_id = v.id
        WHERE a.hospital_id = ? AND a.status IN ('pending','approved')
        ORDER BY a.appointment_date ASC");
    mysqli_stmt_bind_param($stmt, "i", $hospital_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($res)) $pending[] = $r;
    mysqli_stmt_close($stmt);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Update Vaccination Status</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo url('hospital/dashboard.php'); ?>">Dashboard</a></li><li class="breadcrumb-item active">Update Status</li></ol></nav>
    </div>
</div>

<?php if ($id > 0 && isset($appointment)): ?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Appointment Details</div>
            <div class="card-body">
                <?php if ($errors): ?><div class="alert alert-danger"><?php echo e(implode(', ', $errors)); ?></div><?php endif; ?>
                <p><strong>Child:</strong> <?php echo e($appointment['child_name']); ?></p>
                <p><strong>Parent:</strong> <?php echo e($appointment['parent_name']); ?></p>
                <p><strong>Vaccine:</strong> <?php echo e($appointment['vaccine_name']); ?></p>
                <p><strong>Scheduled:</strong> <?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?> at <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></p>
                <hr>
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select" id="statusSelect" required>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div id="completedFields">
                        <div class="mb-3">
                            <label class="form-label">Vaccination Date</label>
                            <input type="date" name="vaccination_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dose Number</label>
                            <input type="number" name="dose_number" class="form-control" value="1" min="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Administered By</label>
                            <input type="text" name="administered_by" class="form-control" placeholder="Doctor / Nurse name">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Update Status</button>
                    <a href="<?php echo url('hospital/bookings.php'); ?>" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">Pending / Approved Appointments – Select to Update</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Child</th><th>Vaccine</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
                <?php if (empty($pending)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">No pending appointments</td></tr>
                <?php else: foreach ($pending as $p): ?>
                    <tr>
                        <td><?php echo e($p['child_name']); ?></td>
                        <td><?php echo e($p['vaccine_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($p['appointment_date'])); ?></td>
                        <td><a href="?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-syringe"></i> Update</a></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

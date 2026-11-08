<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../config/db.php';
require_parent();

$page_title = 'Book Appointment';
$parent_id = current_user_id();
$errors = [];

// Load children of this parent
$children = [];
$stmt = mysqli_prepare($conn, "SELECT id, full_name FROM children WHERE parent_id = ? AND status='active'");
mysqli_stmt_bind_param($stmt, "i", $parent_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($r = mysqli_fetch_assoc($res)) $children[] = $r;
mysqli_stmt_close($stmt);

// Vaccines
$vaccines = [];
$res = mysqli_query($conn, "SELECT id, name, availability FROM vaccines WHERE status='active' ORDER BY name");
while ($r = mysqli_fetch_assoc($res)) $vaccines[] = $r;

// Hospitals
$hospitals = [];
$res = mysqli_query($conn, "SELECT id, hospital_name, city FROM hospitals WHERE status='active' ORDER BY hospital_name");
while ($r = mysqli_fetch_assoc($res)) $hospitals[] = $r;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Invalid security token.';
    } else {
        $child_id = (int)($_POST['child_id'] ?? 0);
        $vaccine_id = (int)($_POST['vaccine_id'] ?? 0);
        $hospital_id = (int)($_POST['hospital_id'] ?? 0);
        $appointment_date = $_POST['appointment_date'] ?? '';
        $appointment_time = $_POST['appointment_time'] ?? '';
        $notes = trim($_POST['notes'] ?? '');

        if (!parent_owns_child($conn, $child_id, $parent_id)) {
            $errors[] = 'Invalid child selected.';
        }
        if ($vaccine_id <= 0) $errors[] = 'Please select a vaccine.';
        if ($hospital_id <= 0) $errors[] = 'Please select a hospital.';
        if (empty($appointment_date) || strtotime($appointment_date) < strtotime('today')) {
            $errors[] = 'Appointment date must be today or in the future.';
        }
        if (empty($appointment_time)) $errors[] = 'Please select a time.';

        // Check vaccine availability
        if (empty($errors)) {
            $stmt = mysqli_prepare($conn, "SELECT availability FROM vaccines WHERE id = ? AND status='active'");
            mysqli_stmt_bind_param($stmt, "i", $vaccine_id);
            mysqli_stmt_execute($stmt);
            $v = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
            if (!$v || $v['availability'] !== 'available') {
                $errors[] = 'Selected vaccine is not available.';
            }
        }

        if (empty($errors)) {
            $stmt = mysqli_prepare($conn, "INSERT INTO appointments (parent_id, child_id, hospital_id, vaccine_id, appointment_date, appointment_time, notes, status) VALUES (?,?,?,?,?,?,?,'pending')");
            mysqli_stmt_bind_param($stmt, "iiiisss", $parent_id, $child_id, $hospital_id, $vaccine_id, $appointment_date, $appointment_time, $notes);
            if (mysqli_stmt_execute($stmt)) {
                flash('success', 'Appointment booked successfully! Waiting for approval.');
                redirect('parent/vaccination-status.php');
            } else {
                $errors[] = 'Failed to book appointment. Please try again.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Book Appointment</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('parent/dashboard.php'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Book Appointment</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <?php if ($errors): ?>
                    <div class="alert alert-danger"><ul class="mb-0 ps-3"><?php foreach ($errors as $e) echo '<li>' . e($e) . '</li>'; ?></ul></div>
                <?php endif; ?>
                <?php if (empty($children)): ?>
                    <div class="alert alert-warning">Please <a href="<?php echo url('parent/add-child.php'); ?>">add a child</a> first.</div>
                <?php else: ?>
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Select Child *</label>
                        <select name="child_id" class="form-select" required>
                            <option value="">Choose child</option>
                            <?php foreach ($children as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo e($c['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Vaccine *</label>
                        <select name="vaccine_id" class="form-select" required>
                            <option value="">Choose vaccine</option>
                            <?php foreach ($vaccines as $v): ?>
                                <option value="<?php echo $v['id']; ?>" <?php echo $v['availability']!=='available'?'disabled':''; ?>>
                                    <?php echo e($v['name']); ?> <?php echo $v['availability']!=='available'?'(Unavailable)':''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Hospital *</label>
                        <select name="hospital_id" class="form-select" required>
                            <option value="">Choose hospital</option>
                            <?php foreach ($hospitals as $h): ?>
                                <option value="<?php echo $h['id']; ?>"><?php echo e($h['hospital_name']); ?> (<?php echo e($h['city']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date *</label>
                            <input type="date" name="appointment_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Time *</label>
                            <input type="time" name="appointment_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Appointment</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

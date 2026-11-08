<?php
/**
 * Database Installer for Vaccination Management System
 * Run once: http://localhost/vaccination-management-system/install.php
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'vaccination_db';

$messages = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = @mysqli_connect($host, $user, $pass);
    if (!$conn) {
        $messages[] = 'Cannot connect to MySQL. Make sure MySQL is running in XAMPP.';
    } else {
        $sql_file = __DIR__ . '/database/vaccination_db.sql';
        if (!file_exists($sql_file)) {
            $messages[] = 'SQL file not found.';
        } else {
            $sql = file_get_contents($sql_file);
            // Split and execute statements
            mysqli_multi_query($conn, $sql);
            do {
                if ($result = mysqli_store_result($conn)) {
                    mysqli_free_result($result);
                }
            } while (mysqli_next_result($conn));

            if (mysqli_errno($conn)) {
                $messages[] = 'Error: ' . mysqli_error($conn);
            } else {
                $success = true;
                $messages[] = 'Database installed successfully!';
                $messages[] = 'You can now delete or rename install.php for security.';
            }
        }
        mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - Vaccination Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0a2540, #0d6efd); min-height: 100vh; display: flex; align-items: center; font-family: system-ui, sans-serif; }
        .card { border-radius: 1rem; box-shadow: 0 1rem 3rem rgba(0,0,0,0.2); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-4">
                    <h3 class="mb-3 text-center"><i class="fas fa-database"></i> Database Installer</h3>
                    <p class="text-muted text-center">Vaccination Management System</p>
                    <?php if ($messages): ?>
                        <?php foreach ($messages as $m): ?>
                            <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>"><?php echo htmlspecialchars($m); ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="text-center">
                            <a href="index.php" class="btn btn-primary btn-lg">Go to Website</a>
                        </div>
                        <hr>
                        <p class="small text-muted"><strong>Default Logins:</strong><br>
                        Admin: admin@vaccination.com / Admin@123<br>
                        Parent: ahmed@parent.com / Parent@123<br>
                        Hospital: citygeneral@hospital.com / Hospital@123</p>
                    <?php else: ?>
                        <p>This will create the <code>vaccination_db</code> database and all tables with seed data.</p>
                        <p class="small text-muted">Requirements: XAMPP with MySQL running. Default credentials (root / no password) are used.</p>
                        <form method="POST">
                            <button type="submit" class="btn btn-primary w-100 btn-lg">Install Database</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

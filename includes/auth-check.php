<?php
/**
 * Authorization helpers
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/csrf.php';

function require_admin() {
    require_login('admin');
}

function require_parent() {
    require_login('parent');
}

function require_hospital() {
    require_login('hospital');
}

function parent_owns_child($conn, $child_id, $parent_id) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM children WHERE id = ? AND parent_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $child_id, $parent_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_num_rows($result) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

function hospital_owns_appointment($conn, $appointment_id, $hospital_id) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM appointments WHERE id = ? AND hospital_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $appointment_id, $hospital_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_num_rows($result) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

<?php
/**
 * Application Configuration
 * Vaccination Management System
 */

// Prevent direct access
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// Base URL (adjust if needed for subdirectory)
define('BASE_URL', '/vaccination-management-system-new');
define('SITE_NAME', 'VaccineCare');
define('SITE_EMAIL', 'noreply@vaccination.com');

// Database credentials (XAMPP defaults)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vaccination_db');

// Session settings
define('SESSION_NAME', 'VMS_SESSION');
define('SESSION_LIFETIME', 3600 * 4); // 4 hours

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');

// Timezone
date_default_timezone_set('Asia/Karachi');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper function for URLs
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset($path) {
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

function redirect($path) {
    header('Location: ' . url($path));
    exit;
}

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function old($key, $default = '') {
    return isset($_SESSION['old'][$key]) ? e($_SESSION['old'][$key]) : e($default);
}

function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
    } else {
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    }
}

function is_logged_in($role = null) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        return false;
    }
    if ($role !== null) {
        return $_SESSION['role'] === $role;
    }
    return true;
}

function require_login($role = null) {
    if (!is_logged_in($role)) {
        flash('error', 'Please login to continue.');
        if ($role === 'admin') {
            redirect('admin/login.php');
        } elseif ($role === 'hospital') {
            redirect('hospital/login.php');
        } else {
            redirect('auth/login.php');
        }
    }
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function current_role() {
    return $_SESSION['role'] ?? null;
}

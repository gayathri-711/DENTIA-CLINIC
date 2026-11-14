<?php
/**
 * Doctor Dental Clinic Dentia - Authentication & Security Layer
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize string for HTML output
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Clean user input
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    $data = trim($data ?? '');
    $data = stripslashes($data);
    return $data;
}

/**
 * Generate CSRF Token
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output hidden CSRF input field
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// ----------------------------------------------------
// Patient Authentication Helpers
// ----------------------------------------------------

function is_patient_logged_in() {
    return !empty($_SESSION['patient_id']);
}

function get_logged_in_patient() {
    if (!is_patient_logged_in()) {
        return null;
    }
    return [
        'id'    => $_SESSION['patient_id'],
        'name'  => $_SESSION['patient_name'] ?? 'Patient',
        'email' => $_SESSION['patient_email'] ?? '',
        'phone' => $_SESSION['patient_phone'] ?? ''
    ];
}

function require_patient_login($redirect_url = 'login.php') {
    if (!is_patient_logged_in()) {
        $_SESSION['flash_error'] = "Please log in to access your patient dashboard.";
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'patient-dashboard.php';
        header("Location: " . $redirect_url);
        exit;
    }
}

// ----------------------------------------------------
// Admin Authentication Helpers
// ----------------------------------------------------

function is_admin_logged_in() {
    return !empty($_SESSION['admin_id']);
}

function get_logged_in_admin() {
    if (!is_admin_logged_in()) {
        return null;
    }
    return [
        'id'    => $_SESSION['admin_id'],
        'name'  => $_SESSION['admin_name'] ?? 'Admin',
        'email' => $_SESSION['admin_email'] ?? '',
        'role'  => $_SESSION['admin_role'] ?? 'admin'
    ];
}

function require_admin_login($redirect_url = 'login.php') {
    if (!is_admin_logged_in()) {
        $_SESSION['flash_error'] = "Administrator authentication required.";
        header("Location: " . $redirect_url);
        exit;
    }
}

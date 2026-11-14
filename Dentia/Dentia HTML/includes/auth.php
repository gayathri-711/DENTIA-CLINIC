<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Unified Role-Based Access Control (RBAC) & Authentication Security
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * XSS prevention escaping
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Clean input
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return stripslashes(trim($data ?? ''));
}

/**
 * CSRF Protection
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// ----------------------------------------------------
// Authentication & Session Inspection
// ----------------------------------------------------

function is_logged_in() {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['user_role']);
}

function current_user() {
    if (!is_logged_in()) return null;
    return [
        'id'        => $_SESSION['user_id'],
        'email'     => $_SESSION['user_email'] ?? '',
        'name'      => $_SESSION['user_name'] ?? '',
        'role'      => $_SESSION['user_role'] ?? '',
        'role_id'   => $_SESSION['role_id'] ?? 0,
        'avatar'    => $_SESSION['user_avatar'] ?? 'images/team/1.webp',
        'ref_id'    => $_SESSION['ref_id'] ?? null // patient_id, doctor_id, or staff_id
    ];
}

/**
 * Strict Role Guard
 * Enforces that user is logged in with one of the allowed roles.
 * If unauthorized, redirects to appropriate portal or login with flash error.
 */
function require_role($allowed_roles = []) {
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }
    
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = "Please sign in to access the hospital management portal.";
        header("Location: /login.php");
        exit;
    }
    
    $userRole = $_SESSION['user_role'];
    if (!in_array($userRole, $allowed_roles) && !in_array('superadmin', $allowed_roles) && $userRole !== 'superadmin') {
        $_SESSION['flash_error'] = "Access denied: Your account role ({$userRole}) is not authorized for this module.";
        redirect_to_dashboard($userRole);
        exit;
    }
}

/**
 * Redirects user to their designated role dashboard
 */
function redirect_to_dashboard($role) {
    switch ($role) {
        case 'superadmin':
        case 'admin':
            header("Location: /admin/index.php");
            break;
        case 'doctor':
            header("Location: /doctor/index.php");
            break;
        case 'patient':
            header("Location: /patient/dashboard.php");
            break;
        case 'receptionist':
            header("Location: /receptionist/index.php");
            break;
        case 'nurse':
            header("Location: /nurse/index.php");
            break;
        case 'lab_technician':
            header("Location: /laboratory/index.php");
            break;
        case 'pharmacist':
            header("Location: /pharmacy/index.php");
            break;
        case 'billing':
            header("Location: /billing/index.php");
            break;
        default:
            header("Location: /index.php");
            break;
    }
    exit;
}

/**
 * Audit Logging Helper
 */
function log_audit_trail($action, $module, $record_id = null, $details = '') {
    global $pdo;
    if (!$pdo) return;
    try {
        $userId = $_SESSION['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, module, record_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $action, $module, $record_id, $details, $ip]);
    } catch (Exception $e) {}
}

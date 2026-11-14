<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Secure Sign Out & Session Invalidation
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/auth.php';

if (!empty($_SESSION['user_id'])) {
    log_audit_trail('LOGOUT', 'Auth', $_SESSION['user_id'], "User signed out.");
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

session_start();
$_SESSION['flash_info'] = "You have been signed out safely.";
header("Location: /login.php");
exit;

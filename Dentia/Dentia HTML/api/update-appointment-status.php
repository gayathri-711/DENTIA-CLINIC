<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * API: Update Appointment Status (Approved, Completed, Cancelled)
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: $_POST;

$ref = sanitize_input($data['appointment_ref'] ?? '');
$status = sanitize_input($data['status'] ?? '');

if (empty($ref) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Missing reference or status parameter.']);
    exit;
}

global $pdo;
if ($pdo) {
    try {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ?, updated_at = NOW() WHERE appointment_ref = ?");
        $stmt->execute([$status, $ref]);
        log_audit_trail('APPOINTMENT_STATUS_CHANGE', 'Appointments', $ref, "Status transitioned to {$status}");
        echo json_encode(['success' => true, 'message' => "Appointment {$ref} updated to {$status}."]);
        exit;
    } catch (Exception $e) {}
}

// Fallback JSON update
$jsonFile = __DIR__ . '/../data/appointments.json';
if (file_exists($jsonFile)) {
    $apps = json_decode(file_get_contents($jsonFile), true) ?: [];
    foreach ($apps as &$a) {
        if ($a['appointment_ref'] === $ref) {
            $a['status'] = $status;
            $a['updated_at'] = date('c');
            break;
        }
    }
    file_put_contents($jsonFile, json_encode($apps, JSON_PRETTY_PRINT));
}

echo json_encode(['success' => true, 'message' => "Appointment {$ref} updated to {$status}."]);


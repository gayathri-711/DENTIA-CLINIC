<?php
/**
 * API: Process Appointment Booking
 */
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$result = process_appointment_booking($_POST);
echo json_encode($result);
exit;


<?php
/**
 * API: Check Slot Availability & Return Booked Slots for Dentist + Date
 */
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$dentist_id = isset($_GET['dentist_id']) ? intval($_GET['dentist_id']) : 0;
$date = isset($_GET['date']) ? sanitize_input($_GET['date']) : '';

if ($dentist_id <= 0 || empty($date)) {
    echo json_encode([
        'success' => false,
        'message' => 'Dentist ID and appointment date are required.'
    ]);
    exit;
}

$allSlots = get_clinic_time_slots();
$bookedSlots = get_booked_slots_for_date($dentist_id, $date);

echo json_encode([
    'success' => true,
    'dentist_id' => $dentist_id,
    'date' => $date,
    'slots' => $allSlots,
    'booked' => $bookedSlots
]);
exit;


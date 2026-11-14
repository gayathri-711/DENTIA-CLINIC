<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * API: Check Slot Availability & Prevent Duplicate Bookings
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$doctorId = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;
$date = isset($_GET['date']) ? sanitize_input($_GET['date']) : date('Y-m-d');

$standardSlots = get_standard_slots();
$bookedSlots = get_booked_slots($doctorId, $date);

// Filter available slots
$availableSlots = array_values(array_diff($standardSlots, $bookedSlots));

echo json_encode([
    'success' => true,
    'doctor_id' => $doctorId,
    'date' => $date,
    'slots' => $standardSlots,
    'booked_slots' => $bookedSlots,
    'available_slots' => $availableSlots,
    'count_available' => count($availableSlots)
]);

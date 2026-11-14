<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * API: Book Appointment with Server-Side Duplicate Validation
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$deptId = intval($_POST['department_id'] ?? 0);
$doctorId = intval($_POST['doctor_id'] ?? 0);
$appointmentDate = sanitize_input($_POST['appointment_date'] ?? '');
$appointmentTime = sanitize_input($_POST['appointment_time'] ?? '');
$patientName = sanitize_input($_POST['patient_name'] ?? '');
$patientEmail = filter_var($_POST['patient_email'] ?? '', FILTER_VALIDATE_EMAIL);
$patientPhone = sanitize_input($_POST['patient_phone'] ?? '');
$appointmentType = sanitize_input($_POST['appointment_type'] ?? 'General Consultation');
$symptoms = sanitize_input($_POST['symptoms'] ?? '');

// Input validation
if (!$deptId || !$doctorId || empty($appointmentDate) || empty($appointmentTime) || empty($patientName) || !$patientEmail || empty($patientPhone)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields with valid information.']);
    exit;
}

// Ensure appointment date is not in the past
if (strtotime($appointmentDate) < strtotime(date('Y-m-d'))) {
    echo json_encode(['success' => false, 'message' => 'Appointments cannot be scheduled for past dates.']);
    exit;
}

global $pdo;
$appointmentRef = generate_hospital_reference('MED');
$doctorName = 'Attending Specialist';
$deptName = 'Medical Department';

if ($pdo) {
    try {
        // 1. Strict Server-Side Concurrency & Duplicate Check
        $checkStmt = $pdo->prepare("SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'Cancelled' LIMIT 1");
        $checkStmt->execute([$doctorId, $appointmentDate, $appointmentTime]);
        if ($checkStmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'This doctor is already booked for ' . $appointmentTime . ' on ' . $appointmentDate . '. Please select another available time slot.'
            ]);
            exit;
        }

        // 2. Fetch Doctor & Department info
        $docStmt = $pdo->prepare("SELECT d.id, u.full_name as doctor_name, dep.name as dept_name 
                                  FROM doctors d 
                                  JOIN users u ON d.user_id = u.id 
                                  JOIN departments dep ON d.department_id = dep.id 
                                  WHERE d.id = ?");
        $docStmt->execute([$doctorId]);
        $docData = $docStmt->fetch();
        if ($docData) {
            $doctorName = $docData['doctor_name'];
            $deptName = $docData['dept_name'];
        }

        // 3. Associate with registered patient if user is logged in
        $patientId = null;
        if (!empty($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'patient') {
            $pStmt = $pdo->prepare("SELECT id FROM patients WHERE user_id = ?");
            $pStmt->execute([$_SESSION['user_id']]);
            $patientId = $pStmt->fetchColumn() ?: null;
        }

        // 4. Insert into database
        $insertStmt = $pdo->prepare("INSERT INTO appointments 
            (appointment_ref, patient_id, patient_name, patient_email, patient_phone, department_id, doctor_id, appointment_date, appointment_time, appointment_type, symptoms, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $insertStmt->execute([
            $appointmentRef,
            $patientId,
            $patientName,
            $patientEmail,
            $patientPhone,
            $deptId,
            $doctorId,
            $appointmentDate,
            $appointmentTime,
            $appointmentType,
            $symptoms
        ]);

        log_audit_trail('APPOINTMENT_BOOKED', 'Appointments', $appointmentRef, "Booked by {$patientName} for {$doctorName} on {$appointmentDate}");

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
} else {
    // Local JSON datastore fallback
    $jsonFile = __DIR__ . '/../data/appointments.json';
    $appointments = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
    
    // Check duplicate
    foreach ($appointments as $app) {
        if ($app['doctor_id'] == $doctorId && $app['appointment_date'] == $appointmentDate && $app['appointment_time'] == $appointmentTime && $app['status'] !== 'Cancelled') {
            echo json_encode([
                'success' => false,
                'message' => 'This doctor is already booked for ' . $appointmentTime . ' on ' . $appointmentDate . '. Please choose another slot.'
            ]);
            exit;
        }
    }

    $newApp = [
        'id' => count($appointments) + 1,
        'appointment_ref' => $appointmentRef,
        'patient_name' => $patientName,
        'patient_email' => $patientEmail,
        'patient_phone' => $patientPhone,
        'department_id' => $deptId,
        'doctor_id' => $doctorId,
        'appointment_date' => $appointmentDate,
        'appointment_time' => $appointmentTime,
        'appointment_type' => $appointmentType,
        'symptoms' => $symptoms,
        'status' => 'Pending',
        'created_at' => date('c')
    ];
    $appointments[] = $newApp;
    file_put_contents($jsonFile, json_encode($appointments, JSON_PRETTY_PRINT));
}

echo json_encode([
    'success' => true,
    'appointment_ref' => $appointmentRef,
    'doctor_name' => $doctorName,
    'department_name' => $deptName,
    'appointment_date' => $appointmentDate,
    'appointment_time' => $appointmentTime,
    'status' => 'Pending'
]);

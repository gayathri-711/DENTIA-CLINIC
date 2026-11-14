<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * API: Get Doctors filtered by Department
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$deptId = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;
$doctors = get_doctors($deptId > 0 ? $deptId : null);

echo json_encode([
    'success' => true,
    'department_id' => $deptId,
    'doctors' => $doctors
]);


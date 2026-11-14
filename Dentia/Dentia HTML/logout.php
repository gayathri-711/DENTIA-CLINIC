<?php
/**
 * Doctor Dental Clinic Dentia - Patient Logout
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

unset($_SESSION['patient_id']);
unset($_SESSION['patient_name']);
unset($_SESSION['patient_email']);
unset($_SESSION['patient_phone']);

set_flash_message('info', 'You have been safely signed out. See you next visit!');
header("Location: index.php");
exit;

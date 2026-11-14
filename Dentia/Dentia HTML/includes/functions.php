<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Core Business Logic, Data Access & Hospital Helper Functions
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Flash message helpers
function set_flash($type, $message) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash_' . $type] = $message;
}

function get_flash_messages() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $messages = [];
    foreach (['success', 'error', 'warning', 'info'] as $type) {
        if (!empty($_SESSION['flash_' . $type])) {
            $messages[$type] = $_SESSION['flash_' . $type];
            unset($_SESSION['flash_' . $type]);
        }
    }
    return $messages;
}

function render_flash_messages() {
    $messages = get_flash_messages();
    if (empty($messages)) return '';
    
    $html = '<div class="container my-3">';
    foreach ($messages as $type => $msg) {
        $alertClass = $type === 'error' ? 'danger' : $type;
        $icon = $type === 'success' ? 'circle-check' : ($type === 'error' ? 'triangle-exclamation' : 'circle-info');
        $html .= '<div class="alert alert-' . e($alertClass) . ' alert-dismissible fade show rounded-3 shadow-sm d-flex align-items-center" role="alert">';
        $html .= '<i class="fa fa-' . $icon . ' me-2 fs-5"></i>';
        $html .= '<div>' . e($msg) . '</div>';
        $html .= '<button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>';
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Hospital Metadata Settings
 */
function get_hospital_settings() {
    global $pdo;
    $defaults = [
        'hospital_name'   => 'MEDINOVA Multi-Speciality Hospital',
        'tagline'         => 'Advanced Healthcare, Better Tomorrow.',
        'email'           => 'care@medinova.com',
        'phone'           => '+1 (800) 555-MEDS (6337)',
        'emergency_phone' => '+1 (800) 555-9111',
        'ambulance_phone' => '+1 (800) 555-AMBU',
        'address'         => '750 Health Sciences Blvd, Medical District, New York, NY 10016',
        'operating_hours' => 'Outpatient Clinics: Mon - Sat 08:00 AM - 08:00 PM | Emergency: 24/7 All Days',
        'google_maps'     => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.15830869428!2d-74.119763973046!3d40.69766374874431!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY!5e0!3m2!1sen!2sus!4v1689247190821!5m2!1sen!2sus'
    ];
    
    if (!$pdo) return $defaults;
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM hospital_settings");
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $defaults[$row['setting_key']] = $row['setting_value'];
        }
    } catch (Exception $e) {}
    
    return $defaults;
}

/**
 * Get all 12 Clinical Departments
 */
function get_departments($activeOnly = true) {
    global $pdo;
    if (!$pdo) {
        // Fallback array of 12 departments
        return [
            ['id' => 1, 'name' => 'Cardiology', 'slug' => 'cardiology', 'code' => 'CARD', 'icon' => 'fa-heart-pulse', 'image' => 'images/misc/l1.webp', 'short_desc' => 'Comprehensive cardiac diagnostics, interventional cath labs, coronary angioplasty, and heart failure care.'],
            ['id' => 2, 'name' => 'Neurology', 'slug' => 'neurology', 'code' => 'NEUR', 'icon' => 'fa-brain', 'image' => 'images/misc/l2.webp', 'short_desc' => 'Advanced neurological disorder management, acute stroke intervention, and neurosurgery.'],
            ['id' => 3, 'name' => 'Orthopedics', 'slug' => 'orthopedics', 'code' => 'ORTH', 'icon' => 'fa-bone', 'image' => 'images/misc/l3.webp', 'short_desc' => 'Total joint replacements, robotic arthroscopic surgery, and trauma reconstruction.'],
            ['id' => 4, 'name' => 'Pediatrics', 'slug' => 'pediatrics', 'code' => 'PEDI', 'icon' => 'fa-baby', 'image' => 'images/misc/l4.webp', 'short_desc' => 'Compassionate infant, child, and adolescent healthcare, Level-III NICU, and pediatric surgery.'],
            ['id' => 5, 'name' => 'Gynecology & Obstetrics', 'slug' => 'gynecology', 'code' => 'GYNE', 'icon' => 'fa-venus', 'image' => 'images/misc/l5.webp', 'short_desc' => 'Complete maternity care, fetal medicine, painless labor suites, and laparoscopic surgery.'],
            ['id' => 6, 'name' => 'Dermatology', 'slug' => 'dermatology', 'code' => 'DERM', 'icon' => 'fa-hand-dots', 'image' => 'images/misc/l6.webp', 'short_desc' => 'Clinical skin pathology, psoriasis therapy, skin cancer screening, and laser dermatology.'],
            ['id' => 7, 'name' => 'Ophthalmology', 'slug' => 'ophthalmology', 'code' => 'OPHT', 'icon' => 'fa-eye', 'image' => 'images/misc/s1.webp', 'short_desc' => 'Micro-incision cataract surgery, LASIK laser vision correction, and vitreoretinal care.'],
            ['id' => 8, 'name' => 'Dental Care & Surgery', 'slug' => 'dental-care', 'code' => 'DENT', 'icon' => 'fa-tooth', 'image' => 'images/misc/s2.webp', 'short_desc' => 'Advanced restorative dentistry, computer-guided titanium implants, and maxillofacial surgery.'],
            ['id' => 9, 'name' => 'Oncology', 'slug' => 'oncology', 'code' => 'ONCO', 'icon' => 'fa-dna', 'image' => 'images/misc/s3.webp', 'short_desc' => 'Medical oncology, targeted immunotherapy, surgical oncology, and chemotherapy suites.'],
            ['id' => 10, 'name' => 'General Medicine', 'slug' => 'general-medicine', 'code' => 'GENM', 'icon' => 'fa-stethoscope', 'image' => 'images/misc/s4.webp', 'short_desc' => 'Primary adult healthcare, management of chronic lifestyle diseases, and acute fever clinics.'],
            ['id' => 11, 'name' => 'Radiology & Imaging', 'slug' => 'radiology', 'code' => 'RADI', 'icon' => 'fa-x-ray', 'image' => 'images/misc/l1.webp', 'short_desc' => 'High-speed 128-slice CT, 3.0 Tesla MRI, digital mammography, and interventional radiology.'],
            ['id' => 12, 'name' => 'Emergency Medicine', 'slug' => 'emergency-medicine', 'code' => 'EMER', 'icon' => 'fa-truck-medical', 'image' => 'images/misc/l2.webp', 'short_desc' => 'Level-I Trauma resuscitation bays, 24/7 cardiac emergency unit, and mobile ICU ambulances.']
        ];
    }
    
    try {
        $where = $activeOnly ? "WHERE is_active = 1" : "";
        $stmt = $pdo->query("SELECT * FROM departments {$where} ORDER BY display_order ASC, id ASC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get Department by Slug
 */
function get_department_by_slug($slug) {
    global $pdo;
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM departments WHERE slug = ? LIMIT 1");
            $stmt->execute([$slug]);
            $res = $stmt->fetch();
            if ($res) return $res;
        } catch (Exception $e) {}
    }
    
    $all = get_departments(false);
    foreach ($all as $dept) {
        if ($dept['slug'] === $slug) return $dept;
    }
    return null;
}

/**
 * Get Doctors (Optionally filtered by Department)
 */
function get_doctors($departmentId = null) {
    global $pdo;
    if (!$pdo) {
        return [
            [
                'id' => 1,
                'name' => 'Dr. Marcus Vance',
                'doctor_code' => 'MED-DOC-001',
                'specialization' => 'Senior Interventional Cardiologist',
                'qualification' => 'MD, FACC, FSCAI (Harvard Medical School)',
                'experience_years' => 16,
                'consultation_fee' => 150.00,
                'room_no' => 'Wing A - Room 101',
                'department_id' => 1,
                'department_name' => 'Cardiology',
                'avatar' => 'images/team/1.webp',
                'availability_days' => 'Monday - Friday',
                'availability_hours' => '09:00 - 15:00'
            ],
            [
                'id' => 2,
                'name' => 'Dr. Elena Rostova',
                'doctor_code' => 'MED-DOC-002',
                'specialization' => 'Chief of Neurology & Neuro-Critical Care',
                'qualification' => 'MD, PhD (Johns Hopkins University)',
                'experience_years' => 14,
                'consultation_fee' => 160.00,
                'room_no' => 'Wing B - Room 204',
                'department_id' => 2,
                'department_name' => 'Neurology',
                'avatar' => 'images/team/2.webp',
                'availability_days' => 'Monday - Thursday',
                'availability_hours' => '10:00 - 16:00'
            ],
            [
                'id' => 3,
                'name' => 'Dr. Julian Thorne',
                'doctor_code' => 'MED-DOC-003',
                'specialization' => 'Consultant Orthopedic & Joint Surgeon',
                'qualification' => 'MS (Ortho), FRCS (Royal College of Surgeons)',
                'experience_years' => 18,
                'consultation_fee' => 140.00,
                'room_no' => 'Wing C - Room 305',
                'department_id' => 3,
                'department_name' => 'Orthopedics',
                'avatar' => 'images/team/3.webp',
                'availability_days' => 'Tuesday - Saturday',
                'availability_hours' => '09:00 - 17:00'
            ],
            [
                'id' => 4,
                'name' => 'Dr. Sophia Martinez',
                'doctor_code' => 'MED-DOC-004',
                'specialization' => 'Head Pediatrician & Neonatologist',
                'qualification' => 'MD (Pediatrics), FAAP (Columbia University)',
                'experience_years' => 12,
                'consultation_fee' => 110.00,
                'room_no' => 'Wing D - Room 402',
                'department_id' => 4,
                'department_name' => 'Pediatrics',
                'avatar' => 'images/team/4.webp',
                'availability_days' => 'Monday - Friday',
                'availability_hours' => '08:30 - 16:30'
            ]
        ];
    }
    
    try {
        $sql = "SELECT d.*, u.full_name as name, u.email, u.phone, u.avatar, dep.name as department_name, dep.slug as department_slug
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                JOIN departments dep ON d.department_id = dep.id
                WHERE d.is_available = 1";
        $params = [];
        if ($departmentId) {
            $sql .= " AND d.department_id = ?";
            $params[] = $departmentId;
        }
        $sql .= " ORDER BY d.id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Standard Available Slots Engine
 */
function get_standard_slots() {
    return [
        '09:00 AM', '09:30 AM', '10:00 AM', '10:30 AM', 
        '11:00 AM', '11:30 AM', '01:30 PM', '02:00 PM', 
        '02:30 PM', '03:00 PM', '03:30 PM', '04:00 PM'
    ];
}

/**
 * Query Booked Slots for Doctor & Date
 */
function get_booked_slots($doctorId, $date) {
    global $pdo;
    if (!$pdo) return [];
    try {
        $stmt = $pdo->prepare("SELECT appointment_time FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND status != 'Cancelled'");
        $stmt->execute([$doctorId, $date]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Generate Secure Unique Hospital Reference (e.g. MED-2026-XXXX)
 */
function generate_hospital_reference($prefix = 'MED') {
    $year = date('Y');
    $rand = strtoupper(bin2hex(random_bytes(2)));
    return "{$prefix}-{$year}-{$rand}";
}

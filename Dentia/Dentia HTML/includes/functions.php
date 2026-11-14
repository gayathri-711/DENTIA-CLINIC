<?php
/**
 * Doctor Dental Clinic Dentia - Core Functions & Business Logic
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

/**
 * Flash messaging helpers
 */
function set_flash_message($type, $message) {
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

/**
 * Render flash messages as modern alerts
 */
function render_flash_messages() {
    $messages = get_flash_messages();
    if (empty($messages)) return '';
    
    $html = '<div class="container my-3">';
    foreach ($messages as $type => $msg) {
        $alertClass = $type === 'error' ? 'danger' : $type;
        $icon = $type === 'success' ? 'check-circle' : ($type === 'error' ? 'exclamation-circle' : 'info-circle');
        $html .= '<div class="alert alert-' . e($alertClass) . ' alert-dismissible fade show rounded-2 shadow-sm d-flex align-items-center" role="alert">';
        $html .= '<i class="fa fa-' . $icon . ' me-2 fs-5"></i>';
        $html .= '<div>' . e($msg) . '</div>';
        $html .= '<button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>';
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Clinic Settings
 */
function get_clinic_settings() {
    global $pdo;
    $defaults = [
        'clinic_name'           => 'Doctor Dental Clinic Dentia',
        'tagline'               => 'Modern Dental Care & Compassionate Smiles',
        'email'                 => 'contact@dentiacare.com',
        'phone'                 => '+1 (800) 234-5678',
        'emergency_phone'       => '+1 (800) 911-DENT',
        'address'               => '100 S Main St, Suite 400, New York, NY 10001',
        'opening_hours_weekday' => 'Monday - Friday: 08:00 AM - 08:00 PM',
        'opening_hours_weekend' => 'Saturday: 09:00 AM - 05:00 PM (Emergency 24/7)',
        'google_map_embed'      => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.15830869428!2d-74.119763973046!3d40.69766374874431!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY!5e0!3m2!1sen!2sus!4v1689247190821!5m2!1sen!2sus'
    ];

    if (!$pdo) return $defaults;

    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM clinic_settings");
        $dbSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return array_merge($defaults, $dbSettings);
    } catch (Exception $e) {
        return $defaults;
    }
}

/**
 * Get all standard services
 */
function get_all_services($featured_only = false) {
    global $pdo;
    if (!$pdo) return get_fallback_services();

    try {
        $sql = "SELECT * FROM services" . ($featured_only ? " WHERE is_featured = 1" : "") . " ORDER BY id ASC";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        return !empty($results) ? $results : get_fallback_services();
    } catch (Exception $e) {
        return get_fallback_services();
    }
}

function get_service_by_slug($slug) {
    global $pdo;
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM services WHERE slug = ? LIMIT 1");
            $stmt->execute([$slug]);
            $service = $stmt->fetch();
            if ($service) return $service;
        } catch (Exception $e) {}
    }

    $fallbacks = get_fallback_services();
    foreach ($fallbacks as $s) {
        if ($s['slug'] === $slug) return $s;
    }
    return null;
}

function get_service_by_id($id) {
    global $pdo;
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $service = $stmt->fetch();
            if ($service) return $service;
        } catch (Exception $e) {}
    }

    $fallbacks = get_fallback_services();
    foreach ($fallbacks as $s) {
        if ($s['id'] == $id) return $s;
    }
    return null;
}

/**
 * Fallback static services definition covering all 10 services
 */
function get_fallback_services() {
    return [
        [
            'id' => 1, 'name' => 'General Dentistry', 'slug' => 'general-dentistry',
            'icon' => 'images/icons/tooth-1.png', 'image' => 'images/misc/l1.webp',
            'short_description' => 'Complete oral care for every smile with cleanings, exams, and diagnostics.',
            'full_description' => 'Our general dentistry services focus on proactive prevention, accurate digital diagnostics, and effective treatment for patients of all ages. Regular examinations ensure optimal oral health and prevent dental disease before it starts.',
            'benefits' => "Complete Oral Health Examinations\nDigital Low-Radiation X-Rays\nTooth-Colored Biomimetic Fillings\nFluoride & Sealant Treatments\nPersonalized Oral Hygiene Care Plans",
            'price' => 85.00, 'duration' => '45-60 mins', 'is_featured' => 1
        ],
        [
            'id' => 2, 'name' => 'Teeth Whitening', 'slug' => 'teeth-whitening',
            'icon' => 'images/icons/tooth-2.png', 'image' => 'images/misc/l2.webp',
            'short_description' => 'Achieve a radiant, luminous smile up to 8 shades brighter in a single session.',
            'full_description' => 'Our in-office laser teeth whitening safely dissolves deep stains caused by coffee, tea, wine, and aging. Formulated with enamel-safe protective gels that deliver instant, dazzling, and long-lasting brightness.',
            'benefits' => "Immediate Results in Just 60 Minutes\nSafe for Sensitive Teeth & Enamel\nRemoves Deep Intrinsic & Extrinsic Stains\nCustom Take-Home Maintenance Trays\nUp to 8 Shades Brighter in One Visit",
            'price' => 199.00, 'duration' => '60 mins', 'is_featured' => 1
        ],
        [
            'id' => 3, 'name' => 'Dental Implants', 'slug' => 'dental-implants',
            'icon' => 'images/icons/tooth-3.png', 'image' => 'images/misc/l3.webp',
            'short_description' => 'Permanent, natural-looking replacement for missing teeth with medical titanium.',
            'full_description' => 'Dental implants provide a sturdy, lifelong foundation for replacement teeth that look, feel, and function just like natural teeth. Prevent jawbone loss and restore your full chewing power with 3D-guided implant placement.',
            'benefits' => "Permanent, Lifetime Solution for Missing Teeth\nPreserves Facial Structure & Bone Density\nFully Restores Chewing & Speaking Ability\nMatches Your Natural Tooth Color & Contour\nHigh 98% Documented Clinical Success Rate",
            'price' => 850.00, 'duration' => '90 mins', 'is_featured' => 1
        ],
        [
            'id' => 4, 'name' => 'Root Canal Treatment', 'slug' => 'root-canal-treatment',
            'icon' => 'images/icons/tooth-4.png', 'image' => 'images/misc/l4.webp',
            'short_description' => 'Gentle, pain-free therapy designed to save severely infected or damaged teeth.',
            'full_description' => 'Modern root canal therapy is quick, comfortable, and painless. Using advanced rotary micro-endodontics and precision digital imaging, we clear out infection, disinfect the root canals, and seal the tooth to preserve its natural structure.',
            'benefits' => "Instant Relief from Acute Tooth Pain\nSaves Natural Tooth from Extraction\nMicroscope-Assisted Precision Care\nPainless with Modern Local Anesthesia\nRestored with a Strong Custom Crown",
            'price' => 320.00, 'duration' => '60-75 mins', 'is_featured' => 1
        ],
        [
            'id' => 5, 'name' => 'Orthodontics and Braces', 'slug' => 'orthodontics-and-braces',
            'icon' => 'images/icons/tooth-5.png', 'image' => 'images/misc/l5.webp',
            'short_description' => 'Clear aligners and modern braces for beautifully aligned teeth and balanced bites.',
            'full_description' => 'Straighten misaligned teeth and fix bite issues comfortably. We provide state-of-the-art clear aligners (Invisalign certified), ceramic discreet brackets, and traditional orthodontics for teenagers and adults.',
            'benefits' => "Discreet Clear Aligners & Aesthetic Brackets\nCorrects Overbite, Underbite, & Crowding\nImproves Chewing Function & Jaw Comfort\nDigital 3D Smile Simulation Preview\nTailored Payment & Flexible Financing Plans",
            'price' => 1200.00, 'duration' => '45 mins', 'is_featured' => 1
        ],
        [
            'id' => 6, 'name' => 'Cosmetic Dentistry', 'slug' => 'cosmetic-dentistry',
            'icon' => 'images/icons/tooth-2.png', 'image' => 'images/misc/l6.webp',
            'short_description' => 'Transform your smile with handcrafted porcelain veneers, contouring, and bonding.',
            'full_description' => 'Cosmetic dentistry blends art and science to craft your dream smile. From porcelain veneers that conceal chips and gaps to composite bonding and gum reshaping, we give you confident, camera-ready smiles.',
            'benefits' => "Custom Porcelain Veneers & Lumineers\nComposite Bonding for Gaps & Chipped Teeth\nAesthetic Gum Line Recontouring\nDigital Smile Design Custom Mockups\nNatural Translucency and Long Durability",
            'price' => 450.00, 'duration' => '60 mins', 'is_featured' => 1
        ],
        [
            'id' => 7, 'name' => 'Pediatric Dentistry', 'slug' => 'pediatric-dentistry',
            'icon' => 'images/icons/tooth-6.png', 'image' => 'images/misc/s1.webp',
            'short_description' => 'Child-friendly dental care in a warm, welcoming, and anxiety-free environment.',
            'full_description' => 'We specialize in making dental visits fun, positive, and stress-free for kids. From toddler checkups to preventive sealants and habit counseling, we help your child cultivate lifelong healthy brushing habits.',
            'benefits' => "Gentle, Anxiety-Free Pediatric Dental Team\nProtective Dental Sealants & Fluoride\nHabit Guidance (Thumb Sucking & Teething)\nEarly Orthodontic Growth Monitoring\nFun Prizes and Educational Smile Games",
            'price' => 95.00, 'duration' => '30-45 mins', 'is_featured' => 1
        ],
        [
            'id' => 8, 'name' => 'Tooth Extraction', 'slug' => 'tooth-extraction',
            'icon' => 'images/icons/tooth-7.png', 'image' => 'images/misc/s2.webp',
            'short_description' => 'Safe, painless extraction of wisdom teeth, impacted teeth, or severe damage.',
            'full_description' => 'When a tooth is broken beyond repair or impacted (like problematic wisdom teeth), our oral surgeons carry out gentle, swift extractions with modern sedation options to keep you completely relaxed throughout.',
            'benefits' => "Minimally Invasive Atraumatic Extraction\nWisdom Tooth Removal with Sedation Options\nFast Healing with Platelet-Rich Fibrin Care\nComprehensive Aftercare Support & Guidance\nImmediate Bone Grafting for Future Implants",
            'price' => 150.00, 'duration' => '45-60 mins', 'is_featured' => 1
        ],
        [
            'id' => 9, 'name' => 'Dental Cleaning', 'slug' => 'dental-cleaning',
            'icon' => 'images/icons/tooth-1.png', 'image' => 'images/misc/s3.webp',
            'short_description' => 'Ultrasonic plaque, tartar, and stain removal for fresh breath and radiant enamel.',
            'full_description' => 'Professional dental prophylaxis cleans deep where regular toothbrushes cannot reach. Ultrasonic scalers remove calcified tartar, eliminate odor-causing bacteria, and polish enamel to a smooth finish.',
            'benefits' => "Ultrasonic Scaling for Deep Tartar Removal\nAir-Flow Stain Polishing Technology\nEliminates Bad Breath (Halitosis)\nPrevents Gingivitis & Cavity Formation\nLeaves Teeth Feeling Incredibly Smooth & Clean",
            'price' => 75.00, 'duration' => '40 mins', 'is_featured' => 1
        ],
        [
            'id' => 10, 'name' => 'Gum Treatment', 'slug' => 'gum-treatment',
            'icon' => 'images/icons/tooth-4.png', 'image' => 'images/misc/s4.webp',
            'short_description' => 'Comprehensive periodontal therapy to stop bleeding gums and treat gum disease.',
            'full_description' => 'Healthy gums are the foundation of healthy teeth. Our periodontal treatments treat gingivitis and periodontitis through deep cleaning (scaling and root planing), laser pocket therapy, and antimicrobial rinses.',
            'benefits' => "Halts Gum Bleeding, Swelling, and Soreness\nDeep Scaling & Root Planing (SRP)\nLaser-Assisted Gum Sterilization\nPrevents Bone Loss and Tooth Looseness\nLong-Term Periodontal Maintenance Plans",
            'price' => 210.00, 'duration' => '60 mins', 'is_featured' => 1
        ]
    ];
}

/**
 * Get Dentists
 */
function get_all_dentists($active_only = true) {
    global $pdo;
    if ($pdo) {
        try {
            $sql = "SELECT * FROM dentists" . ($active_only ? " WHERE is_active = 1" : "") . " ORDER BY id ASC";
            $stmt = $pdo->query($sql);
            $dentists = $stmt->fetchAll();
            if (!empty($dentists)) return $dentists;
        } catch (Exception $e) {}
    }

    return [
        [
            'id' => 1, 'name' => 'Dr. Sarah Bennett',
            'qualification' => 'Doctor of Dental Surgery (DDS), New York University College of Dentistry',
            'specialization' => 'Lead Dentist & General Care',
            'experience' => '12+ Years Experience',
            'email' => 'sarah.bennett@dentiacare.com',
            'phone' => '+1 (800) 234-5671',
            'description' => 'Dr. Sarah Bennett leads our clinical practice with a focus on comprehensive preventive dentistry, painless procedures, and restorative prosthetics.',
            'image' => 'images/team/1.webp',
            'availability_days' => 'Monday - Friday',
            'availability_hours' => '09:00 - 17:00',
            'is_active' => 1
        ],
        [
            'id' => 2, 'name' => 'Dr. Maya Lin',
            'qualification' => 'Doctor of Medicine in Dentistry (DMD), University of Pennsylvania',
            'specialization' => 'Cosmetic & Aesthetic Dentistry',
            'experience' => '9+ Years Experience',
            'email' => 'maya.lin@dentiacare.com',
            'phone' => '+1 (800) 234-5672',
            'description' => 'Specializing in smile redesigns, porcelain veneers, and professional laser whitening treatments with artistic precision.',
            'image' => 'images/team/2.webp',
            'availability_days' => 'Monday - Thursday',
            'availability_hours' => '10:00 - 18:00',
            'is_active' => 1
        ],
        [
            'id' => 3, 'name' => 'Dr. James Rodriguez',
            'qualification' => 'Doctor of Dental Surgery (DDS), Columbia University',
            'specialization' => 'Orthodontics & Implants Specialist',
            'experience' => '14+ Years Experience',
            'email' => 'james.rodriguez@dentiacare.com',
            'phone' => '+1 (800) 234-5673',
            'description' => 'Board-certified Orthodontist and Implantologist dedicated to invisible aligners, complex bite corrections, and titanium dental implants.',
            'image' => 'images/team/3.webp',
            'availability_days' => 'Tuesday - Saturday',
            'availability_hours' => '09:00 - 16:00',
            'is_active' => 1
        ],
        [
            'id' => 4, 'name' => 'Dr. Emily Chen',
            'qualification' => 'Doctor of Dental Surgery (DDS), UCLA School of Dentistry',
            'specialization' => 'Pediatric Dentistry & Gum Therapy',
            'experience' => '8+ Years Experience',
            'email' => 'emily.chen@dentiacare.com',
            'phone' => '+1 (800) 234-5674',
            'description' => 'Renowned for gentle and engaging pediatric care, root canal treatments, and preventative periodontal therapy for the whole family.',
            'image' => 'images/team/4.webp',
            'availability_days' => 'Monday - Friday',
            'availability_hours' => '08:30 - 16:30',
            'is_active' => 1
        ]
    ];
}

function get_dentist_by_id($id) {
    $dentists = get_all_dentists(false);
    foreach ($dentists as $d) {
        if ($d['id'] == $id) return $d;
    }
    return null;
}

/**
 * Standard Available Time Slots
 */
function get_clinic_time_slots() {
    return [
        '09:00 AM',
        '10:00 AM',
        '11:00 AM',
        '12:00 PM',
        '02:00 PM',
        '03:00 PM',
        '04:00 PM',
        '05:00 PM',
        '06:00 PM'
    ];
}

/**
 * Check if a specific time slot is already booked for a dentist on a given date
 * PREVENTS DUPLICATE BOOKINGS
 */
function is_slot_booked($dentist_id, $appointment_date, $appointment_time, $exclude_id = null) {
    global $pdo;
    if (!$pdo) return false;

    try {
        $sql = "SELECT id FROM appointments 
                WHERE dentist_id = ? 
                AND appointment_date = ? 
                AND appointment_time = ? 
                AND status != 'Cancelled'";
        $params = [$dentist_id, $appointment_date, $appointment_time];

        if ($exclude_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get all booked time slots for a dentist on a specific date
 */
function get_booked_slots_for_date($dentist_id, $appointment_date) {
    global $pdo;
    if (!$pdo) return [];

    try {
        $stmt = $pdo->prepare("SELECT appointment_time FROM appointments 
                               WHERE dentist_id = ? 
                               AND appointment_date = ? 
                               AND status != 'Cancelled'");
        $stmt->execute([$dentist_id, $appointment_date]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Generate unique appointment reference (e.g. DEN-2026-9284)
 */
function generate_appointment_ref() {
    return 'DEN-' . date('Y') . '-' . mt_rand(1000, 9999);
}

/**
 * Book an Appointment with validation, duplicate booking prevention, and email notification
 */
function process_appointment_booking($data) {
    global $pdo;

    $name    = sanitize_input($data['name'] ?? '');
    $email   = sanitize_input($data['email'] ?? '');
    $phone   = sanitize_input($data['phone'] ?? '');
    $service_id = intval($data['service_id'] ?? 0);
    $dentist_id = intval($data['dentist_id'] ?? 0);
    $date    = sanitize_input($data['date'] ?? '');
    $time    = sanitize_input($data['time'] ?? '');
    $message = sanitize_input($data['message'] ?? '');
    $patient_id = !empty($_SESSION['patient_id']) ? intval($_SESSION['patient_id']) : null;

    // Handle legacy form inputs
    if ($service_id <= 0 && !empty($data['service'])) {
        $svcName = strtolower(trim($data['service']));
        foreach (get_all_services(false) as $svc) {
            if (strtolower($svc['name']) === $svcName || strtolower($svc['slug']) === $svcName) {
                $service_id = $svc['id'];
                break;
            }
        }
        if ($service_id <= 0) $service_id = 1; // Default to General Dentistry
    }
    if ($dentist_id <= 0) {
        $dentist_id = 1; // Default to Lead Dentist Dr. Sarah Bennett
    }

    // Convert date format if passed as mm-dd-yyyy or mm/dd/yyyy
    if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $date, $matches)) {
        $date = sprintf('%04d-%02d-%02d', $matches[3], $matches[1], $matches[2]);
    }

    // Basic Validation
    if (empty($name) || empty($email) || empty($phone)) {
        return ['success' => false, 'message' => 'Please provide your full name, email, and phone number.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Please enter a valid email address.'];
    }
    if ($service_id <= 0) {
        return ['success' => false, 'message' => 'Please select a dental service.'];
    }
    if ($dentist_id <= 0) {
        return ['success' => false, 'message' => 'Please choose your preferred dentist.'];
    }
    if (empty($date) || empty($time)) {
        return ['success' => false, 'message' => 'Please pick an appointment date and time slot.'];
    }

    // Verify date is not in the past
    $appointment_timestamp = strtotime($date);
    if ($appointment_timestamp < strtotime(date('Y-m-d'))) {
        return ['success' => false, 'message' => 'Appointment date cannot be in the past.'];
    }

    // Check duplicate booking: Is this slot already reserved for this dentist?
    if (is_slot_booked($dentist_id, $date, $time)) {
        $dentist = get_dentist_by_id($dentist_id);
        $dentist_name = $dentist ? $dentist['name'] : 'the selected doctor';
        return [
            'success' => false, 
            'message' => "The time slot {$time} on {$date} is already booked for {$dentist_name}. Please select another time or dentist."
        ];
    }

    // If patient is not logged in, check if patient email exists in DB to link
    if (!$patient_id && $pdo) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM patients WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $existingPatient = $stmt->fetch();
            if ($existingPatient) {
                $patient_id = $existingPatient['id'];
            }
        } catch (Exception $e) {}
    }

    $ref = generate_appointment_ref();

    if ($pdo) {
        try {
            $sql = "INSERT INTO appointments 
                    (appointment_ref, patient_id, patient_name, patient_email, patient_phone, service_id, dentist_id, appointment_date, appointment_time, message, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$ref, $patient_id, $name, $email, $phone, $service_id, $dentist_id, $date, $time, $message]);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database error while saving appointment: ' . $e->getMessage()];
        }
    }

    // Dispatch notification
    $service = get_service_by_id($service_id);
    $dentist = get_dentist_by_id($dentist_id);
    $service_name = $service ? $service['name'] : 'Dental Care';
    $dentist_name = $dentist ? $dentist['name'] : 'Doctor Dentia';

    $subject = "Appointment Confirmation - Reference: {$ref}";
    $emailBody = "Dear {$name},\n\nYour dental appointment has been successfully scheduled!\n\n" .
                 "Reference Code: {$ref}\n" .
                 "Service: {$service_name}\n" .
                 "Doctor: {$dentist_name}\n" .
                 "Date: {$date}\n" .
                 "Time: {$time}\n\n" .
                 "Location: 100 S Main St, Suite 400, New York, NY\n" .
                 "Emergency Contact: +1 (800) 911-DENT\n\n" .
                 "Thank you for choosing Doctor Dental Clinic Dentia.";

    send_notification_email($email, $subject, $emailBody);

    return [
        'success' => true,
        'message' => "Your appointment has been successfully booked!",
        'ref'     => $ref,
        'details' => [
            'name'         => $name,
            'email'        => $email,
            'service_name' => $service_name,
            'dentist_name' => $dentist_name,
            'date'         => $date,
            'time'         => $time
        ]
    ];
}

/**
 * Notification mail sender and logger
 */
function send_notification_email($to, $subject, $message) {
    $settings = get_clinic_settings();
    $from = $settings['email'];
    $headers = "From: " . $settings['clinic_name'] . " <{$from}>\r\n" .
               "Reply-To: {$from}\r\n" .
               "X-Mailer: PHP/" . phpversion();

    @mail($to, $subject, $message, $headers);

    // Also record to mail notification log for easy inspection
    $logDir = __DIR__ . '/../data';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    $logEntry = "[" . date('Y-m-d H:i:s') . "] TO: {$to} | SUBJECT: {$subject}\nMESSAGE:\n{$message}\n" . str_repeat('-', 60) . "\n";
    @file_put_contents($logDir . '/mail_notifications.log', $logEntry, FILE_APPEND);
}

/**
 * Testimonials
 */
function get_all_testimonials($approved_only = true) {
    global $pdo;
    if ($pdo) {
        try {
            $sql = "SELECT * FROM testimonials" . ($approved_only ? " WHERE is_approved = 1" : "") . " ORDER BY id DESC";
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            if (!empty($results)) return $results;
        } catch (Exception $e) {}
    }

    return [
        [
            'id' => 1, 'patient_name' => 'Michael Scott', 'patient_role' => 'Business Executive', 'rating' => 5,
            'comment' => 'Dr. Sarah Bennett and the Dentia team are phenomenal. The clinic is spotless, high-tech, and the entire dental implant procedure was completely painless. Highly recommended!',
            'image' => 'images/testimonial/1.webp', 'is_approved' => 1
        ],
        [
            'id' => 2, 'patient_name' => 'Sophie Martinez', 'patient_role' => 'Graphic Designer', 'rating' => 5,
            'comment' => 'I underwent laser teeth whitening before my wedding day. My teeth are noticeably whiter and there was zero sensitivity. The staff is so gentle and supportive!',
            'image' => 'images/testimonial/2.webp', 'is_approved' => 1
        ],
        [
            'id' => 3, 'patient_name' => 'David Harrison', 'patient_role' => 'Software Engineer', 'rating' => 5,
            'comment' => 'Getting Invisalign here with Dr. Rodriguez changed my smile entirely. The digital scanning was so cool, no messy putty molds. Clean, professional, and on schedule.',
            'image' => 'images/testimonial/3.webp', 'is_approved' => 1
        ],
        [
            'id' => 4, 'patient_name' => 'Amanda Clark', 'patient_role' => 'School Teacher', 'rating' => 5,
            'comment' => 'My 7-year-old daughter used to be terrified of the dentist. Dr. Emily Chen made the visit fun and enjoyable for her. We wouldn’t go anywhere else!',
            'image' => 'images/testimonial/4.webp', 'is_approved' => 1
        ]
    ];
}

/**
 * FAQs
 */
function get_all_faqs($category = null) {
    global $pdo;
    if ($pdo) {
        try {
            $sql = "SELECT * FROM faqs" . ($category ? " WHERE category = ?" : "") . " ORDER BY display_order ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($category ? [$category] : []);
            $results = $stmt->fetchAll();
            if (!empty($results)) return $results;
        } catch (Exception $e) {}
    }

    return [
        [
            'question' => 'How often should I visit the dentist for a checkup?',
            'answer' => 'We recommend visiting Dentia every six months for a comprehensive checkup and professional cleaning. Regular checkups allow us to detect potential issues early, preventing costly and invasive treatments later.',
            'category' => 'General'
        ],
        [
            'question' => 'Are dental treatments at Dentia painless?',
            'answer' => 'Yes! We prioritize patient comfort with state-of-the-art painless anesthesia techniques, micro-invasive rotary tools, and compassionate sedation options to make your visit stress-free.',
            'category' => 'General'
        ],
        [
            'question' => 'How long does laser teeth whitening last?',
            'answer' => 'Our in-office whitening results typically last between 1 to 3 years, depending on your dietary habits (such as coffee, tea, or red wine consumption) and daily oral hygiene routine.',
            'category' => 'Cosmetic'
        ],
        [
            'question' => 'What should I do in a dental emergency?',
            'answer' => 'Call our 24/7 Dental Emergency Hotline immediately at +1 (800) 911-DENT. For a knocked-out tooth, place it in milk or saliva and come directly to our clinic within 60 minutes for the highest re-implantation success.',
            'category' => 'Emergency'
        ],
        [
            'question' => 'How can I prevent duplicate bookings?',
            'answer' => 'Our live booking system checks real-time slot availability for each dentist. Once a time slot is reserved, it is locked immediately to prevent any double-booking.',
            'category' => 'Booking'
        ]
    ];
}

/**
 * Blogs
 */
function get_recent_blogs($limit = 3) {
    global $pdo;
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM blogs ORDER BY id DESC LIMIT ?");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();
            if (!empty($results)) return $results;
        } catch (Exception $e) {}
    }

    return [
        [
            'id' => 1,
            'title' => '5 Essential Tips for Healthy Teeth and Gums',
            'slug' => '5-essential-tips-healthy-teeth',
            'excerpt' => 'Discover daily dental habits that keep your teeth strong, plaque-free, and your breath fresh throughout the day.',
            'content' => '<p>Good oral hygiene is the foundation of overall wellness. Here are five simple yet crucial habits to adopt: brush twice daily with fluoride toothpaste, floss before bedtime to remove interdental food debris, rinse with an antimicrobial mouthwash, drink plenty of water, and never skip your bi-annual dental checkup.</p>',
            'image' => 'images/blog/l1.webp',
            'author' => 'Dr. Sarah Bennett',
            'category' => 'Dental Care',
            'published_at' => '2026-10-15'
        ],
        [
            'id' => 2,
            'title' => 'Everything You Need to Know About Dental Implants',
            'slug' => 'everything-about-dental-implants',
            'excerpt' => 'Missing teeth? Learn why modern titanium dental implants are the gold standard for restoring chewing power and aesthetics.',
            'content' => '<p>A dental implant is a titanium post surgically positioned into the jawbone beneath the gum line. It provides permanent support for prosthetic crowns or bridges. Unlike dentures, implants do not slip or click, and they actively prevent facial bone resorption.</p>',
            'image' => 'images/blog/l2.webp',
            'author' => 'Dr. James Rodriguez',
            'category' => 'Implantology',
            'published_at' => '2026-10-28'
        ],
        [
            'id' => 3,
            'title' => 'The Truth About Teeth Whitening: Myths vs Facts',
            'slug' => 'teeth-whitening-myths-vs-facts',
            'excerpt' => 'Separating commercial myths from clinically proven whitening methods that protect your enamel while delivering bright smiles.',
            'content' => '<p>Many over-the-counter DIY kits can damage tooth enamel or cause severe gum irritation. Professional in-office laser treatments utilize pH-balanced hydrogen peroxide gels activated by specialized light frequencies for fast, safe, and even shade enhancement.</p>',
            'image' => 'images/blog/l3.webp',
            'author' => 'Dr. Maya Lin',
            'category' => 'Aesthetic Care',
            'published_at' => '2026-11-02'
        ]
    ];
}

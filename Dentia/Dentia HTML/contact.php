<?php
/**
 * Doctor Dental Clinic Dentia - Contact & Location Page
 * Stores inquiries in MySQL contact_messages and dispatches email notification
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$clinicSettings = get_clinic_settings();
$messageSent = false;
$errorMessage = '';

// Handle Contact Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitize_input($_POST['name'] ?? '');
    $email   = sanitize_input($_POST['email'] ?? '');
    $phone   = sanitize_input($_POST['phone'] ?? '');
    $subject = sanitize_input($_POST['subject'] ?? 'General Inquiry');
    $message = sanitize_input($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $errorMessage = "Please fill in your name, email, and message.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please provide a valid email address.";
    } else {
        global $pdo;
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, is_read) VALUES (?, ?, ?, ?, ?, 0)");
                $stmt->execute([$name, $email, $phone, $subject, $message]);
                $messageSent = true;
            } catch (Exception $e) {
                // If database insert fails, still proceed to send notification
            }
        } else {
            $messageSent = true;
        }

        // Send Email notification to clinic
        $emailBody = "New contact message from Dentia website:\n\n" .
                     "Name: {$name}\n" .
                     "Email: {$email}\n" .
                     "Phone: {$phone}\n" .
                     "Subject: {$subject}\n\n" .
                     "Message:\n{$message}";
        send_notification_email($clinicSettings['email'], "Dentia Contact Inquiry: {$subject}", $emailBody);

        // If AJAX request, return sent
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo 'sent';
            exit;
        }
    }
}

$pageTitle = "Contact Us & Clinic Location";
require_once __DIR__ . '/includes/header.php';
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <!-- Subheader -->
    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Get in Touch</span>
                    <h1 class="fw-bold mb-2">Contact Dentia Dental Clinic</h1>
                    <div class="border-bottom my-3 mx-auto" style="width: 60px; border-width: 3px !important; border-color: var(--dentia-primary) !important;"></div>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold">Contact</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Contact & Location Section -->
    <section class="py-5 bg-light">
        <div class="container py-3">

            <!-- Highlights Row -->
            <div class="row g-4 mb-5">
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px; height:64px; font-size:24px;">
                            <i class="fa fa-map-marker"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Our Location</h5>
                        <p class="text-muted small mb-0"><?= e($clinicSettings['address']) ?></p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px; height:64px; font-size:24px;">
                            <i class="fa fa-phone"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Emergency Phone</h5>
                        <p class="text-muted small mb-1">Standard: <?= e($clinicSettings['phone']) ?></p>
                        <a href="tel:<?= e($clinicSettings['emergency_phone']) ?>" class="text-danger fw-bold text-decoration-none">
                            24/7 Hotline: <?= e($clinicSettings['emergency_phone']) ?>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px; height:64px; font-size:24px;">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Working Hours</h5>
                        <p class="text-muted small mb-1"><?= e($clinicSettings['opening_hours_weekday']) ?></p>
                        <p class="text-muted small mb-0"><?= e($clinicSettings['opening_hours_weekend']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Form & Map Row -->
            <div class="row g-5">
                <!-- Contact Form -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow rounded-4 overflow-hidden bg-white p-4 p-md-5">
                        <h3 class="fw-bold text-dark mb-2">Send Us a Message</h3>
                        <p class="text-muted small mb-4">Have questions regarding a dental procedure or insurance? Send us a message and our patient care team will get back to you promptly.</p>

                        <?php if ($messageSent): ?>
                            <div class="alert alert-success rounded-3 p-4 text-center mb-0">
                                <i class="fa fa-check-circle fs-1 text-success mb-2"></i>
                                <h5 class="fw-bold text-dark">Thank You For Your Message!</h5>
                                <p class="small text-muted mb-3">We have received your inquiry and our clinic reception will reach out to you within 24 hours.</p>
                                <a href="contact.php" class="btn btn-outline-primary rounded-pill btn-sm px-4">Send Another Message</a>
                            </div>
                        <?php else: ?>

                            <?php if (!empty($errorMessage)): ?>
                                <div class="alert alert-danger rounded-3 mb-4"><?= e($errorMessage) ?></div>
                            <?php endif; ?>

                            <form method="POST" action="contact.php" id="contact_form">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Your Name *</label>
                                        <input type="text" name="name" class="form-control rounded-3" placeholder="Jane Smith" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Email Address *</label>
                                        <input type="email" name="email" class="form-control rounded-3" placeholder="jane@example.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Phone Number</label>
                                        <input type="tel" name="phone" class="form-control rounded-3" placeholder="+1 (555) 000-0000">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Subject</label>
                                        <input type="text" name="subject" class="form-control rounded-3" placeholder="Dental Inquiry">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Message *</label>
                                        <textarea name="message" class="form-control rounded-3" rows="4" placeholder="How can our dental team assist you today?" required></textarea>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
                                            <i class="fa fa-paper-plane me-2"></i> Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Google Maps & Visual Clinic Card -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow rounded-4 overflow-hidden bg-white h-100">
                        <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
                            <span class="fw-bold small text-dark"><i class="fa fa-map-o text-primary me-2"></i> Interactive Location Map</span>
                            <span class="badge bg-success-subtle text-success small rounded-pill">Open Today</span>
                        </div>
                        <div class="position-relative" style="min-height: 400px; height: 100%;">
                            <iframe 
                                src="<?= e($clinicSettings['google_map_embed']) ?>" 
                                width="100%" 
                                height="100%" 
                                style="border:0; min-height: 420px;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

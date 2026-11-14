<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Contact & Department Directory Information
 */
$pageTitle = "Contact Hospital Information Desk";
require_once __DIR__ . '/includes/header.php';
$settings = get_hospital_settings();

$messageSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $phone = sanitize_input($_POST['phone'] ?? '');
    $dept = sanitize_input($_POST['department'] ?? 'General Inquiry');
    $subject = sanitize_input($_POST['subject'] ?? 'Website Inquiry');
    $message = sanitize_input($_POST['message'] ?? '');

    if ($name && $email && $message) {
        global $pdo;
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, department, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $phone, $dept, $subject, $message]);
                $messageSent = true;
            } catch (Exception $e) {}
        } else {
            $messageSent = true;
        }
    }
}
?>

<div class="py-5 bg-light border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Hospital Help Desk</span>
                <h1 class="fw-bold text-primary mb-2">Contact MEDINOVA</h1>
                <p class="text-muted mb-0">Our clinical help desk and patient coordinators are available around the clock to assist you.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <a href="booking.php" class="btn btn-med-primary"><i class="fa fa-calendar-check me-2"></i> Book Appointment</a>
            </div>
        </div>
    </div>
</div>

<div class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left Contact Form -->
            <div class="col-lg-7">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border">
                    <h3 class="fw-bold text-primary mb-2">Send Us an Inquiry</h3>
                    <p class="text-muted mb-4">Please submit your clinical or administrative question and our patient liaison team will respond promptly.</p>

                    <?php if ($messageSent): ?>
                        <div class="alert alert-success rounded-3 p-3 mb-4">
                            <i class="fa fa-circle-check me-2"></i> Thank you! Your message has been routed to the hospital communications team.
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="contact.php">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+1 (555) 000-0000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Department Speciality</label>
                                <select name="department" class="form-select">
                                    <option value="General Inquiry">General Information Desk</option>
                                    <option value="Cardiology">Cardiology Center</option>
                                    <option value="Neurology">Neurology Institute</option>
                                    <option value="Orthopedics">Orthopedics & Joint Care</option>
                                    <option value="Pediatrics">Pediatrics Wing</option>
                                    <option value="Billing">Billing & Insurance</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Inquiry Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control" placeholder="e.g. Insurance authorization, report query..." required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Your Message <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="4" placeholder="How can we assist you today?" required></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-med-primary px-5 py-3 fw-bold rounded-pill mt-4">
                            <i class="fa fa-paper-plane me-2"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Contact Cards & Map -->
            <div class="col-lg-5">
                <div class="p-4 bg-light rounded-4 border mb-4">
                    <h5 class="fw-bold text-primary mb-3">Hospital Contact Info</h5>
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="fa fa-map-marker-alt text-danger fs-4 mt-1"></i>
                        <div>
                            <strong>Physical Campus</strong>
                            <p class="text-muted mb-0 small"><?= e($settings['address']) ?></p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="fa fa-phone text-primary fs-4 mt-1"></i>
                        <div>
                            <strong>Appointments & Info</strong>
                            <p class="text-muted mb-0 small"><?= e($settings['phone']) ?></p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="fa fa-envelope text-info fs-4 mt-1"></i>
                        <div>
                            <strong>Email Inquiries</strong>
                            <p class="text-muted mb-0 small"><?= e($settings['email']) ?></p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <i class="fa fa-phone-volume text-danger fs-4 mt-1"></i>
                        <div>
                            <strong>24/7 Emergency Line</strong>
                            <p class="text-danger fw-bold mb-0"><?= e($settings['emergency_phone']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Google Map Embed -->
                <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow-sm border">
                    <iframe src="<?= e($settings['google_maps']) ?>" loading="lazy" style="border:0;" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

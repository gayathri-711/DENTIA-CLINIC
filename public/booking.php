<?php
/**
 * Doctor Dental Clinic Dentia - Functional Appointment Booking System
 * Includes Duplicate Booking Prevention, Real-Time Slot Checks, and Confirmation
 */
$pageTitle = "Book an Appointment";
require_once __DIR__ . '/includes/header.php';

$allServices = get_all_services();
$dentists = get_all_dentists(true);
$preselectedServiceId = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
$preselectedDentistId = isset($_GET['dentist_id']) ? intval($_GET['dentist_id']) : 0;

$bookingResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingResult = process_appointment_booking($_POST);
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        if ($bookingResult['success']) {
            echo 'sent';
            exit;
        } else {
            echo 'failed';
            exit;
        }
    }
}
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <!-- Subheader -->
    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Schedule Your Visit</span>
                    <h1 class="fw-bold mb-2">Book Your Dental Appointment</h1>
                    <div class="border-bottom my-3 mx-auto" style="width: 60px; border-width: 3px !important; border-color: var(--dentia-primary) !important;"></div>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold">Book Appointment</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Form Section -->
    <section class="py-5 bg-light">
        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <div id="booking_form_wrapper">

                        <?php if ($bookingResult && $bookingResult['success']): ?>
                            <!-- Non-AJAX Fallback Confirmation Card -->
                            <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center p-4 p-md-5 my-4">
                                <div class="mb-4">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle shadow-sm" style="width:72px; height:72px; font-size:32px;">
                                        <i class="fa fa-check"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-2">Appointment Successfully Confirmed!</h3>
                                <p class="text-muted mb-4">Your visit has been confirmed and registered in our clinic calendar. An email confirmation has been sent to <strong><?= e($bookingResult['details']['email']) ?></strong>.</p>
                                
                                <div class="card bg-light border-0 rounded-3 p-4 text-start mb-4 mx-auto" style="max-width: 520px;">
                                    <div class="row g-2">
                                        <div class="col-6 text-muted">Booking Reference:</div>
                                        <div class="col-6 fw-bold text-primary"><?= e($bookingResult['ref']) ?></div>
                                        
                                        <div class="col-6 text-muted">Dental Service:</div>
                                        <div class="col-6 fw-bold"><?= e($bookingResult['details']['service_name']) ?></div>
                                        
                                        <div class="col-6 text-muted">Doctor:</div>
                                        <div class="col-6 fw-bold"><?= e($bookingResult['details']['dentist_name']) ?></div>
                                        
                                        <div class="col-6 text-muted">Date & Time:</div>
                                        <div class="col-6 fw-bold text-success"><?= e($bookingResult['details']['date']) ?> at <?= e($bookingResult['details']['time']) ?></div>
                                        
                                        <div class="col-6 text-muted">Clinic Address:</div>
                                        <div class="col-6 fw-bold">100 S Main St, Suite 400, New York, NY</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center gap-3">
                                    <a href="booking.php" class="btn btn-outline-primary rounded-pill px-4">Book Another</a>
                                    <a href="patient-dashboard.php" class="btn btn-primary rounded-pill px-4">View Patient Portal</a>
                                </div>
                            </div>
                        <?php else: ?>

                            <div class="card border-0 shadow rounded-4 overflow-hidden bg-white">
                                <div class="card-header bg-primary text-white p-4">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                        <div>
                                            <h4 class="mb-1 text-white fw-bold"><i class="fa fa-calendar-check-o me-2"></i> Patient Appointment Reservation</h4>
                                            <p class="small text-white-50 mb-0">Fill in the fields below. Live slot availability prevents duplicate scheduling.</p>
                                        </div>
                                        <?php if ($isPatient): ?>
                                            <span class="badge bg-white text-primary px-3 py-2 rounded-pill">
                                                <i class="fa fa-user me-1"></i> Logged in as <?= e($patientData['name']) ?>
                                            </span>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                                                <i class="fa fa-sign-in me-1"></i> Existing Patient? Log In
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card-body p-4 p-md-5">
                                    
                                    <!-- Alert Box -->
                                    <div id="booking_alert_box" class="alert <?= ($bookingResult && !$bookingResult['success']) ? 'alert-danger' : 'd-none' ?> rounded-3 mb-4">
                                        <?= ($bookingResult && !$bookingResult['success']) ? '<i class="fa fa-exclamation-circle me-2"></i> ' . e($bookingResult['message']) : '' ?>
                                    </div>

                                    <form id="dentia_booking_form" method="POST" action="booking.php">
                                        <?= csrf_field() ?>

                                        <div class="row g-4">
                                            
                                            <!-- Section 1: Patient Information -->
                                            <div class="col-12">
                                                <h5 class="fw-bold text-dark border-bottom pb-2">
                                                    <i class="fa fa-user-circle text-primary me-2"></i> 1. Patient Information
                                                </h5>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Full Name *</label>
                                                <input type="text" name="name" class="form-control rounded-3" value="<?= e($patientData['name'] ?? ($_POST['name'] ?? '')) ?>" placeholder="e.g. Sarah Jenkins" required>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Email Address *</label>
                                                <input type="email" name="email" class="form-control rounded-3" value="<?= e($patientData['email'] ?? ($_POST['email'] ?? '')) ?>" placeholder="sarah@example.com" required>
                                                <div class="form-text small" style="font-size:11px;">Confirmation receipt will be emailed here.</div>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Phone Number *</label>
                                                <input type="tel" name="phone" class="form-control rounded-3" value="<?= e($patientData['phone'] ?? ($_POST['phone'] ?? '')) ?>" placeholder="+1 (555) 234-5678" required>
                                            </div>

                                            <!-- Section 2: Dental Procedure & Doctor -->
                                            <div class="col-12 mt-4">
                                                <h5 class="fw-bold text-dark border-bottom pb-2">
                                                    <i class="fa fa-stethoscope text-primary me-2"></i> 2. Dental Procedure & Doctor
                                                </h5>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Dental Service *</label>
                                                <select name="service_id" id="service_select" class="form-select rounded-3" required>
                                                    <option value="">-- Choose Dental Service --</option>
                                                    <?php foreach ($allServices as $s): ?>
                                                        <option value="<?= $s['id'] ?>" <?= ($preselectedServiceId == $s['id'] || (isset($_POST['service_id']) && $_POST['service_id'] == $s['id'])) ? 'selected' : '' ?>>
                                                            <?= e($s['name']) ?> (₹<?= number_format($s['price']) ?> - <?= e($s['duration']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Preferred Dentist *</label>
                                                <select name="dentist_id" id="dentist_select" class="form-select rounded-3" required>
                                                    <option value="">-- Choose Preferred Dentist --</option>
                                                    <?php foreach ($dentists as $d): ?>
                                                        <option value="<?= $d['id'] ?>" <?= ($preselectedDentistId == $d['id'] || (isset($_POST['dentist_id']) && $_POST['dentist_id'] == $d['id'])) ? 'selected' : '' ?>>
                                                            <?= e($d['name']) ?> — <?= e($d['specialization']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Section 3: Date & Live Time Slots -->
                                            <div class="col-12 mt-4">
                                                <h5 class="fw-bold text-dark border-bottom pb-2">
                                                    <i class="fa fa-clock-o text-primary me-2"></i> 3. Schedule Date & Time Slot
                                                </h5>
                                            </div>

                                            <div class="col-md-5">
                                                <label class="form-label fw-semibold small">Appointment Date *</label>
                                                <input type="date" name="date" id="appointment_date" min="<?= date('Y-m-d') ?>" value="<?= e($_POST['date'] ?? date('Y-m-d', strtotime('+1 day'))) ?>" class="form-control rounded-3" required>
                                                <div class="form-text small" style="font-size:11px;">Clinic is open Mon - Sat. Sunday for emergency calls.</div>
                                            </div>

                                            <div class="col-md-7" id="time_slots_section">
                                                <label class="form-label fw-semibold small d-flex justify-content-between align-items-center">
                                                    <span>Available Time Slots *</span>
                                                    <span class="badge bg-secondary-subtle text-secondary small" style="font-size: 11px;">
                                                        <i class="fa fa-shield me-1"></i> Duplicate Check Active
                                                    </span>
                                                </label>
                                                
                                                <div id="time_slots_container" class="border rounded-3 p-3 bg-light">
                                                    <!-- Dynamic Slot Pills Rendered via JavaScript -->
                                                    <span class="text-muted small"><i class="fa fa-info-circle me-1"></i> Please choose a dentist and date above to load available time slots.</span>
                                                </div>
                                                <input type="hidden" name="time" id="selected_time" value="<?= e($_POST['time'] ?? '') ?>">
                                            </div>

                                            <!-- Additional Notes -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold small">Additional Message or Medical Symptoms</label>
                                                <textarea name="message" class="form-control rounded-3" rows="3" placeholder="Describe any symptoms, tooth sensitivity, allergies, or questions for your dentist..."><?= e($_POST['message'] ?? '') ?></textarea>
                                            </div>

                                            <div class="col-12 text-center mt-4">
                                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow">
                                                    <span><i class="fa fa-calendar-check-o me-2"></i> Confirm Appointment Booking</span>
                                                </button>
                                                <p class="small text-muted mt-2 mb-0">
                                                    <i class="fa fa-lock me-1"></i> No upfront payment required. You can reschedule or cancel anytime from your patient dashboard.
                                                </p>
                                            </div>

                                        </div>
                                    </form>

                                </div>
                            </div>

                        <?php endif; ?>

                    </div>

                </div>
            </div>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

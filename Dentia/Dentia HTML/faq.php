<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Frequently Asked Questions
 */
$pageTitle = "Frequently Asked Questions (FAQs)";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-light border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Patient Guidance</span>
                <h1 class="fw-bold text-primary mb-2">Frequently Asked Questions</h1>
                <p class="text-muted mb-0">Answers to common inquiries regarding appointments, hospital admission, insurance, and medical records.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <a href="booking.php" class="btn btn-med-primary"><i class="fa fa-calendar-check me-2"></i> Book Appointment</a>
            </div>
        </div>
    </div>
</div>

<div class="py-5">
    <div class="container" style="max-width: 900px;">
        <div class="accordion accordion-flush shadow-sm rounded-4 border overflow-hidden" id="medinovaFaq">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold text-primary py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        1. How do I book an appointment with a specific medical specialist?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#medinovaFaq">
                    <div class="accordion-body text-muted lh-base">
                        You can book directly through our <a href="booking.php">Online Booking Engine</a>. Simply select your desired medical department (e.g. Cardiology, Orthopedics, Pediatrics), choose your attending doctor, view real-time available time slots, and submit. You will receive an immediate confirmation reference (e.g. MED-2026-XXXX).
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold text-primary py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        2. What should I do in the event of an acute medical emergency?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#medinovaFaq">
                    <div class="accordion-body text-muted lh-base">
                        Call our 24/7 Emergency Line immediately at <strong><?= e($settings['emergency_phone']) ?></strong> or dispatch a mobile ICU ambulance at <strong><?= e($settings['ambulance_phone']) ?></strong>. Our Level-I Trauma Center operates continuously without interruption.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold text-primary py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        3. Which insurance plans and payment methods does MEDINOVA accept?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#medinovaFaq">
                    <div class="accordion-body text-muted lh-base">
                        We accept all major PPO and HMO health insurance providers including BlueCross BlueShield, Aetna, Cigna, UnitedHealthcare, and Medicare. In addition, we accept Visa, MasterCard, digital banking, and cash settlements at our cashier desk.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold text-primary py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        4. How can I access my diagnostic lab test results and e-prescriptions?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#medinovaFaq">
                    <div class="accordion-body text-muted lh-base">
                        All registered patients have 24/7 access to their <a href="patient/dashboard.php">Patient Health Portal</a>. Once pathology results are verified by the technologist or prescriptions are authorized by your physician, they are automatically published to your electronic health record.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold text-primary py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                        5. What are the hospital visiting hours for inpatient wards?
                    </button>
                </h2>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#medinovaFaq">
                    <div class="accordion-body text-muted lh-base">
                        General Inpatient Ward visiting hours are daily from 10:00 AM to 01:00 PM and 04:00 PM to 07:00 PM. Intensive Care Units (ICU/CCU) have restricted visiting windows from 11:00 AM to 12:00 PM and 05:00 PM to 06:00 PM to ensure patient recovery and infection control.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

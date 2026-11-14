<?php
/**
 * Doctor Dental Clinic Dentia - Dynamic Detailed Service Page
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) ? sanitize_input($_GET['slug']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$service = null;
if (!empty($slug)) {
    $service = get_service_by_slug($slug);
} elseif ($id > 0) {
    $service = get_service_by_id($id);
}

if (!$service) {
    // Default to General Dentistry if not found
    $service = get_service_by_id(1);
}

$pageTitle = $service['name'];
require_once __DIR__ . '/includes/header.php';

$allServices = get_all_services();
$dentists = get_all_dentists(true);
$benefitsList = array_filter(array_map('trim', explode("\n", $service['benefits'] ?? '')));
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <!-- Subheader -->
    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Specialized Dental Procedure</span>
                    <h1 class="fw-bold mb-2"><?= e($service['name']) ?></h1>
                    <div class="border-bottom my-3 mx-auto" style="width: 60px; border-width: 3px !important; border-color: var(--dentia-primary) !important;"></div>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item"><a href="services.php" class="text-decoration-none text-muted">Services</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold"><?= e($service['name']) ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Service Overview -->
    <section class="py-5">
        <div class="container py-3">
            <div class="row g-5">
                
                <!-- Main Content Column -->
                <div class="col-lg-8">
                    <!-- Service Hero Card -->
                    <div class="position-relative mb-4">
                        <img src="<?= e($service['image']) ?>" alt="<?= e($service['name']) ?>" class="img-fluid rounded-4 shadow-sm w-100" style="max-height: 420px; object-fit: cover;">
                        
                        <div class="position-absolute top-0 end-0 m-3 d-flex gap-2">
                            <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill fw-semibold">
                                <i class="fa fa-clock-o text-primary me-1"></i> <?= e($service['duration']) ?>
                            </span>
                            <span class="badge bg-primary text-white shadow-sm px-3 py-2 rounded-pill fw-bold">
                                $<?= number_format($service['price'], 2) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Description -->
                    <h2 class="fw-bold mb-3">Comprehensive Overview</h2>
                    <p class="lead text-dark" style="line-height: 1.8;">
                        <?= e($service['full_description']) ?>
                    </p>

                    <!-- Key Benefits -->
                    <div class="card border-0 bg-light rounded-4 p-4 my-4">
                        <h4 class="fw-bold text-dark mb-3">
                            <i class="fa fa-check-square-o text-primary me-2"></i> Clinical Benefits & Advantages
                        </h4>
                        <div class="row g-3">
                            <?php foreach ($benefitsList as $benefit): ?>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success text-white rounded-circle p-1 me-2 d-flex align-items-center justify-content-center" style="width: 22px; height: 22px; font-size: 11px;">
                                            <i class="fa fa-check"></i>
                                        </div>
                                        <span class="fw-semibold text-secondary small"><?= e($benefit) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Step-by-Step Procedure Process -->
                    <h3 class="fw-bold mt-5 mb-4">What to Expect During Your Treatment</h3>
                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <div class="border rounded-4 p-4 h-100 bg-white shadow-sm">
                                <div class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3 fw-bold">
                                    Step 1: Diagnostics
                                </div>
                                <h6 class="fw-bold">Digital Examination</h6>
                                <p class="small text-muted mb-0">High-definition 3D imaging, digital scans, and a thorough oral health evaluation by our specialist.</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-4 p-4 h-100 bg-white shadow-sm">
                                <div class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3 fw-bold">
                                    Step 2: Care
                                </div>
                                <h6 class="fw-bold">Gentle Procedure</h6>
                                <p class="small text-muted mb-0">Comfortable treatment with modern local anesthesia or mild sedation in our sterile suites.</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-4 p-4 h-100 bg-white shadow-sm">
                                <div class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3 fw-bold">
                                    Step 3: Aftercare
                                </div>
                                <h6 class="fw-bold">Results & Prevention</h6>
                                <p class="small text-muted mb-0">Final polish, post-treatment instructions, and customized care guidelines for lasting results.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Embedded Direct Appointment Booking Form -->
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mt-5" id="book-now-section">
                        <div class="card-header bg-primary text-white p-4">
                            <h4 class="mb-1 text-white fw-bold"><i class="fa fa-calendar-plus-o me-2"></i> Book <?= e($service['name']) ?> Appointment</h4>
                            <p class="small text-white-50 mb-0">Choose your preferred doctor, date, and live time slot below.</p>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <div id="booking_alert_box" class="alert d-none rounded-3 mb-4"></div>

                            <form id="dentia_booking_form" method="POST" action="api/book-appointment.php">
                                <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Full Name *</label>
                                        <input type="text" name="name" class="form-control rounded-3" value="<?= e($patientData['name'] ?? '') ?>" placeholder="Your Full Name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Email Address *</label>
                                        <input type="email" name="email" class="form-control rounded-3" value="<?= e($patientData['email'] ?? '') ?>" placeholder="name@example.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Phone Number *</label>
                                        <input type="text" name="phone" class="form-control rounded-3" value="<?= e($patientData['phone'] ?? '') ?>" placeholder="+1 (555) 000-0000" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Preferred Dentist *</label>
                                        <select name="dentist_id" id="dentist_select" class="form-select rounded-3" required>
                                            <option value="">Select a Doctor</option>
                                            <?php foreach ($dentists as $d): ?>
                                                <option value="<?= $d['id'] ?>"><?= e($d['name']) ?> (<?= e($d['specialization']) ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Appointment Date *</label>
                                        <input type="date" name="date" id="appointment_date" min="<?= date('Y-m-d') ?>" class="form-control rounded-3" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Selected Service</label>
                                        <input type="text" class="form-control rounded-3 bg-light" value="<?= e($service['name']) ?> ($<?= number_format($service['price'], 2) ?>)" readonly>
                                    </div>

                                    <!-- Time Slots Selection Section -->
                                    <div class="col-12" id="time_slots_section">
                                        <label class="form-label fw-semibold small d-flex justify-content-between align-items-center">
                                            <span>Available Time Slots * (Real-Time Availability)</span>
                                            <span class="text-muted small" style="font-size: 11px;">Duplicate slots automatically blocked</span>
                                        </label>
                                        <div id="time_slots_container" class="border rounded-3 p-3 bg-light">
                                            <span class="text-muted small"><i class="fa fa-info-circle me-1"></i> Please choose a dentist and date to load available time slots.</span>
                                        </div>
                                        <input type="hidden" name="time" id="selected_time" value="">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Additional Notes or Symptoms (Optional)</label>
                                        <textarea name="message" class="form-control rounded-3" rows="3" placeholder="Tell us if you have any dental pain, dental insurance, or specific requests..."></textarea>
                                    </div>

                                    <div class="col-12 mt-4 text-center">
                                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
                                            <span><i class="fa fa-calendar-check-o me-2"></i> Confirm <?= e($service['name']) ?> Booking</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Column -->
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 100px;">
                        
                        <!-- Quick Book Card -->
                        <div class="card border-0 rounded-4 shadow-sm p-4 bg-primary text-white mb-4">
                            <h5 class="fw-bold text-white mb-2"><i class="fa fa-bolt me-2"></i> Need Immediate Care?</h5>
                            <p class="small text-white-50 mb-3">Our dental team handles emergency relief with same-day appointments.</p>
                            <a href="tel:<?= e($clinicSettings['emergency_phone']) ?>" class="btn btn-light text-primary rounded-pill w-100 fw-bold mb-2">
                                <i class="fa fa-phone me-1"></i> <?= e($clinicSettings['emergency_phone']) ?>
                            </a>
                            <a href="#book-now-section" class="btn btn-outline-light rounded-pill w-100 fw-semibold">
                                Book Procedure Below
                            </a>
                        </div>

                        <!-- All 10 Services List Navigation -->
                        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                            <h5 class="fw-bold mb-3 border-bottom pb-2">All Dental Procedures</h5>
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($allServices as $s): ?>
                                    <li class="mb-2">
                                        <a href="service-<?= e($s['slug']) ?>.php" class="d-flex justify-content-between align-items-center text-decoration-none p-2 rounded-2 <?= ($s['id'] == $service['id']) ? 'bg-primary text-white fw-bold' : 'text-dark hover-bg-light' ?>">
                                            <span><i class="fa fa-angle-right me-2"></i><?= e($s['name']) ?></span>
                                            <span class="small <?= ($s['id'] == $service['id']) ? 'text-white' : 'text-primary' ?>">$<?= number_format($s['price'], 0) ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Clinic Hours Card -->
                        <div class="card border-0 rounded-4 shadow-sm p-4 bg-light">
                            <h6 class="fw-bold mb-3"><i class="fa fa-clock-o text-primary me-2"></i> Clinic Hours</h6>
                            <div class="small mb-2"><strong>Monday - Friday:</strong><br>08:00 AM - 08:00 PM</div>
                            <div class="small mb-2"><strong>Saturday:</strong><br>09:00 AM - 05:00 PM</div>
                            <div class="small text-danger fw-bold"><strong>Sunday:</strong><br>24/7 Emergency On-Call</div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

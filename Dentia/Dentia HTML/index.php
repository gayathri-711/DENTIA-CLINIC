<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Public Modern Hospital Homepage
 */
$pageTitle = "Advanced Healthcare, Better Tomorrow";
require_once __DIR__ . '/includes/header.php';

$departments = get_departments(true);
$doctors = get_doctors();
?>

<!-- Hero Section -->
<section class="med-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="med-hero-badge">
                    <i class="fa fa-shield-heart text-warning"></i>
                    <span>JCI & NABH Accredited Tertiary Healthcare</span>
                </div>
                <h1 class="text-white">Precision Medicine.<br><span class="text-info">Compassionate Healing.</span></h1>
                <p class="fs-5 text-white-50 mb-4 lh-base" style="max-width: 580px;">
                    MEDINOVA brings together world-renowned physicians, robotic surgical suites, and 24/7 emergency trauma care across 12 medical specialities to provide world-class patient outcomes.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="booking.php" class="btn btn-warning btn-lg px-4 py-3 fw-bold rounded-pill shadow">
                        <i class="fa fa-calendar-check me-2"></i> Book an Appointment
                    </a>
                    <a href="departments.php" class="btn btn-outline-light btn-lg px-4 py-3 fw-bold rounded-pill">
                        <i class="fa fa-stethoscope me-2"></i> Explore Specialities
                    </a>
                </div>

                <!-- Live Quick Stats -->
                <div class="row g-3 mt-4 pt-3 border-top border-white border-opacity-25" style="max-width: 550px;">
                    <div class="col-4">
                        <h3 class="fw-bold text-white mb-0">12+</h3>
                        <small class="text-white-50">Speciality Wings</small>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold text-white mb-0">50+</h3>
                        <small class="text-white-50">Board Specialists</small>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold text-white mb-0">24/7</h3>
                        <small class="text-white-50">Emergency & ER</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block">
                <!-- Fast Booking Card -->
                <div class="card border-0 shadow-lg rounded-4 p-4 text-dark" style="background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(12px);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 fs-4">
                            <i class="fa fa-calendar-plus"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-primary">Schedule Consultation</h5>
                            <small class="text-muted">Instant slot reservation with attending specialists</small>
                        </div>
                    </div>

                    <form action="booking.php" method="GET">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Select Medical Department</label>
                            <select name="department_id" class="form-select py-2 border-primary-subtle" required>
                                <option value="">Choose Speciality...</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= e($dept['id']) ?>"><?= e($dept['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Preferred Date</label>
                            <input type="date" name="date" class="form-control py-2" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small text-muted"><i class="fa fa-phone-volume text-danger me-1"></i> Emergency Desk</span>
                                <strong class="text-primary"><?= e($settings['emergency_phone']) ?></strong>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-med-primary w-100 py-3 fw-bold">
                            Find Available Doctors <i class="fa fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Hospital Introduction Section -->
<section class="py-5 bg-white border-bottom">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="images/misc/l1.webp" class="img-fluid rounded-4 shadow-lg w-100" alt="MEDINOVA Hospital Facility" style="max-height: 440px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 m-3 p-3 bg-white rounded-3 shadow border d-flex align-items-center gap-3">
                        <div class="bg-success text-white p-3 rounded-circle fs-4">
                            <i class="fa fa-heart-pulse"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">99.4% Clinical Success</h6>
                            <small class="text-muted">Over 45,000 successful procedures</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-3">About MEDINOVA</span>
                <h2 class="display-6 fw-bold text-primary mb-3">A New Standard in Multi-Speciality Healthcare</h2>
                <p class="fs-5 text-muted lh-base mb-4">
                    At MEDINOVA, healthcare is more than treatment—it is a continuum of preventive diagnostics, empathetic bedside care, and state-of-the-art medical technology.
                </p>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa fa-circle-check text-primary fs-5 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Robotic Surgery Suites</h6>
                                <small class="text-muted">Sub-millimeter precision for joint and abdominal surgeries.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa fa-circle-check text-primary fs-5 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">24/7 Level-I Trauma Center</h6>
                                <small class="text-muted">Direct cardiac cath labs and stroke thrombolysis units.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa fa-circle-check text-primary fs-5 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Fully Automated Pathology</h6>
                                <small class="text-muted">Real-time digital results delivered to patient portal.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa fa-circle-check text-primary fs-5 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Paperless Patient Portals</h6>
                                <small class="text-muted">Instant access to medical records, e-prescriptions, and bills.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="about.php" class="btn btn-med-outline px-4 py-2 fw-bold">Read Our Full Story <i class="fa fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- 12 Medical Specialities Showcase -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5" style="max-width: 650px; margin: 0 auto;">
            <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Our Specialities</span>
            <h2 class="display-6 fw-bold text-primary">12 Centres of Excellence</h2>
            <p class="text-muted">Providing comprehensive medical, diagnostic, and surgical expertise under one unified health system.</p>
        </div>

        <div class="row g-4">
            <?php foreach (array_slice($departments, 0, 6) as $dept): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="med-card d-flex flex-column p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="med-card-icon">
                                <i class="fa <?= e($dept['icon']) ?>"></i>
                            </div>
                            <span class="badge bg-light text-primary border px-3 py-2 fw-bold"><?= e($dept['code']) ?></span>
                        </div>
                        <h4 class="fw-bold mb-2">
                            <a href="department-details.php?slug=<?= e($dept['slug']) ?>" class="text-dark text-decoration-none hover-teal">
                                <?= e($dept['name']) ?>
                            </a>
                        </h4>
                        <p class="text-muted flex-grow-1 mb-4"><?= e($dept['short_desc']) ?></p>
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="department-details.php?slug=<?= e($dept['slug']) ?>" class="text-primary fw-bold text-decoration-none">
                                Learn More <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                            <a href="booking.php?department_id=<?= e($dept['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Book Doctor
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="departments.php" class="btn btn-med-primary btn-lg px-5 py-3 fw-bold rounded-pill shadow">
                View All 12 Medical Departments <i class="fa fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Featured Specialist Doctors Section -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5">
            <div>
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Clinical Faculty</span>
                <h2 class="display-6 fw-bold text-primary mb-0">Meet Our Chief Specialists</h2>
            </div>
            <a href="doctors.php" class="btn btn-med-outline mt-3 mt-md-0">View All Hospital Doctors <i class="fa fa-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            <?php foreach (array_slice($doctors, 0, 4) as $doc): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="doctor-profile-card">
                        <img src="<?= e($doc['avatar']) ?>" class="doctor-profile-img" alt="<?= e($doc['name']) ?>">
                        <div class="doctor-profile-body">
                            <span class="badge bg-primary bg-opacity-10 text-primary mb-2"><?= e($doc['department_name'] ?? 'Specialist') ?></span>
                            <h5 class="fw-bold mb-1"><?= e($doc['name']) ?></h5>
                            <p class="small text-muted mb-2 text-truncate" title="<?= e($doc['specialization']) ?>"><?= e($doc['specialization']) ?></p>
                            <p class="small text-muted mb-3"><i class="fa fa-graduation-cap me-1"></i> <?= e($doc['qualification']) ?></p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="fw-bold text-primary">$<?= number_format($doc['consultation_fee'], 2) ?></span>
                                <a href="booking.php?doctor_id=<?= e($doc['id']) ?>" class="btn btn-sm btn-med-primary">
                                    Book
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Hospital Facilities & Infrastructure -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5" style="max-width: 650px; margin: 0 auto;">
            <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Infrastructure</span>
            <h2 class="display-6 fw-bold text-primary">Advanced Clinical Facilities</h2>
            <p class="text-muted">Engineered with high-standard infection controls, advanced surgical suites, and round-the-clock intensive care units.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 d-inline-block fs-3 mb-3">
                        <i class="fa fa-x-ray"></i>
                    </div>
                    <h5 class="fw-bold mb-2">3.0 Tesla Silent MRI & CT</h5>
                    <p class="text-muted mb-0">High-definition neuro and cardiac imaging with ultra-fast scan times and reduced radiation exposure.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="rounded-3 bg-danger bg-opacity-10 text-danger p-3 d-inline-block fs-3 mb-3">
                        <i class="fa fa-heart-pulse"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Digital Biplane Cath Lab</h5>
                    <p class="text-muted mb-0">Dedicated coronary intervention theatre for primary emergency angioplasty, stenting, and pacemaker implants.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 d-inline-block fs-3 mb-3">
                        <i class="fa fa-bed-pulse"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Level-III Neonatal & Adult ICU</h5>
                    <p class="text-muted mb-0">Independent isolation airflow cubicles, advanced mechanical ventilators, and 1:1 dedicated nursing care.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Patient Testimonials -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container py-4">
        <div class="text-center mb-5" style="max-width: 600px; margin: 0 auto;">
            <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Patient Stories</span>
            <h2 class="display-6 fw-bold text-primary">Trusted by Thousands</h2>
            <p class="text-muted">Read real reviews from patients and families who experienced healing and support at MEDINOVA.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 rounded-4 bg-light h-100 border">
                    <div class="text-warning mb-3">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                    <p class="text-muted fst-italic mb-4">"The speed and clinical precision of the cardiology team saved my father's life during an acute myocardial infarction. Outstanding emergency team!"</p>
                    <div class="d-flex align-items-center gap-3">
                        <img src="images/testimonial/1.webp" class="rounded-circle" width="48" height="48" alt="David Harrison">
                        <div>
                            <h6 class="fw-bold mb-0">David Harrison</h6>
                            <small class="text-primary">Cardiology Patient</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 bg-light h-100 border">
                    <div class="text-warning mb-3">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                    <p class="text-muted fst-italic mb-4">"Underwent robotic knee replacement with Dr. Julian Thorne. The hospital room was impeccably clean and the nursing care was world-class."</p>
                    <div class="d-flex align-items-center gap-3">
                        <img src="images/testimonial/2.webp" class="rounded-circle" width="48" height="48" alt="Michael Scott">
                        <div>
                            <h6 class="fw-bold mb-0">Michael Scott</h6>
                            <small class="text-primary">Orthopedics Patient</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 bg-light h-100 border">
                    <div class="text-warning mb-3">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                    <p class="text-muted fst-italic mb-4">"The pediatric wing is designed so gently. Dr. Sophia Martinez made our daughter feel safe and relaxed throughout her treatment. Truly grateful."</p>
                    <div class="d-flex align-items-center gap-3">
                        <img src="images/testimonial/3.webp" class="rounded-circle" width="48" height="48" alt="Amanda Clark">
                        <div>
                            <h6 class="fw-bold mb-0">Amanda Clark</h6>
                            <small class="text-primary">Pediatrics Parent</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fast Appointment Booking CTA -->
<section class="py-5" style="background: linear-gradient(135deg, var(--med-primary-dark), var(--med-primary)); color: #fff;">
    <div class="container text-center py-4">
        <h2 class="display-6 fw-bold text-white mb-3">Take the First Step Toward Better Health</h2>
        <p class="fs-5 text-white-50 mb-4" style="max-width: 650px; margin: 0 auto;">
            Our board-certified physicians across 12 specialities are ready to assist you. Book online in under two minutes with instant slot confirmation.
        </p>
        <a href="booking.php" class="btn btn-warning btn-lg px-5 py-3 fw-bold rounded-pill shadow">
            <i class="fa fa-calendar-check me-2"></i> Book Online Consultation Now
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

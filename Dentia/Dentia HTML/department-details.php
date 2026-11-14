<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Dynamic Department Profile & Clinical Services
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? 'cardiology';
$department = get_department_by_slug($slug);

if (!$department) {
    header("Location: departments.php");
    exit;
}

$pageTitle = $department['name'] . " Department";
require_once __DIR__ . '/includes/header.php';

// Department-specific doctors
$deptDoctors = get_doctors($department['id']);
?>

<!-- Department Header -->
<div class="py-5 bg-primary text-white" style="background: linear-gradient(135deg, var(--med-primary-dark), var(--med-secondary)) !important;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-25 px-3 py-1 rounded-pill mb-3">
                    <i class="fa <?= e($department['icon']) ?>"></i>
                    <span class="fw-bold text-uppercase">Center of Excellence &bull; Code: <?= e($department['code']) ?></span>
                </div>
                <h1 class="display-5 fw-bold mb-3"><?= e($department['name']) ?></h1>
                <p class="lead text-white-50 mb-0"><?= e($department['short_desc']) ?></p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="booking.php?department_id=<?= e($department['id']) ?>" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold shadow">
                    <i class="fa fa-calendar-check me-2"></i> Book Specialist
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left Clinical Information -->
            <div class="col-lg-8">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border mb-5">
                    <h3 class="fw-bold text-primary mb-3">Clinical Overview & Philosophy</h3>
                    <p class="fs-5 text-muted lh-lg"><?= nl2br(e($department['full_desc'])) ?></p>

                    <h4 class="fw-bold text-primary mt-5 mb-4">Key Diagnostic & Surgical Capabilities</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 d-flex align-items-start gap-3">
                                <i class="fa fa-circle-check text-success fs-4 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Advanced 24/7 Monitoring</h6>
                                    <small class="text-muted">Direct telemetry feeds linked to specialized critical care teams.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 d-flex align-items-start gap-3">
                                <i class="fa fa-circle-check text-success fs-4 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Minimally Invasive Protocols</h6>
                                    <small class="text-muted">Faster recovery times, reduced pain, and minimal surgical scarring.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 d-flex align-items-start gap-3">
                                <i class="fa fa-circle-check text-success fs-4 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Multidisciplinary Tumor Boards</h6>
                                    <small class="text-muted">Cross-consultation with radiology, pathology, and surgery specialists.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 d-flex align-items-start gap-3">
                                <i class="fa fa-circle-check text-success fs-4 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Point-of-Care Digital Imaging</h6>
                                    <small class="text-muted">Real-time ultrasound and digital diagnostics at the patient bedside.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attending Specialists -->
                <h3 class="fw-bold text-primary mb-4">Attending Department Doctors</h3>
                <?php if (!empty($deptDoctors)): ?>
                    <div class="row g-4 mb-5">
                        <?php foreach ($deptDoctors as $doc): ?>
                            <div class="col-md-6">
                                <div class="doctor-profile-card">
                                    <img src="<?= e($doc['avatar']) ?>" class="doctor-profile-img" alt="<?= e($doc['name']) ?>">
                                    <div class="doctor-profile-body">
                                        <span class="badge bg-primary bg-opacity-10 text-primary mb-2"><?= e($doc['specialization']) ?></span>
                                        <h5 class="fw-bold mb-1"><?= e($doc['name']) ?></h5>
                                        <p class="small text-muted mb-2"><i class="fa fa-graduation-cap me-1"></i> <?= e($doc['qualification']) ?></p>
                                        <p class="small text-muted mb-3"><i class="fa fa-clock me-1"></i> <?= e($doc['availability_days']) ?> &bull; <?= e($doc['availability_hours']) ?></p>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                            <span class="fw-bold text-primary">$<?= number_format($doc['consultation_fee'], 2) ?></span>
                                            <a href="booking.php?department_id=<?= e($department['id']) ?>&doctor_id=<?= e($doc['id']) ?>" class="btn btn-sm btn-med-primary">
                                                Book Visit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info rounded-3 p-4 mb-5">
                        <i class="fa fa-user-doctor me-2"></i> Our specialized attending physicians for <?= e($department['name']) ?> are currently conducting rounds. Appointments can be scheduled online or through our central reception desk.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <!-- Appointment Fast Booking Widget -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                    <h5 class="fw-bold text-primary mb-3"><i class="fa fa-calendar-check me-2 text-info"></i> Fast Appointment</h5>
                    <p class="text-muted small">Consult with our <?= e($department['name']) ?> faculty without waiting. Verified time slots available daily.</p>
                    <a href="booking.php?department_id=<?= e($department['id']) ?>" class="btn btn-med-primary w-100 py-3 fw-bold mb-3">
                        Schedule Consultation
                    </a>
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block"><i class="fa fa-phone text-primary me-2"></i> Department Direct Desk:</small>
                        <strong class="text-dark fs-6"><?= e($department['emergency_phone']) ?></strong>
                    </div>
                </div>

                <!-- All Departments Navigation Widget -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-primary mb-3">Other Speciality Wings</h5>
                    <div class="list-group list-group-flush">
                        <?php 
                        $allDepts = get_departments(true);
                        foreach ($allDepts as $d): 
                        ?>
                            <a href="department-details.php?slug=<?= e($d['slug']) ?>" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex justify-content-between align-items-center <?= $d['slug'] === $slug ? 'text-primary fw-bold' : 'text-muted' ?>">
                                <span><i class="fa <?= e($d['icon']) ?> me-2"></i> <?= e($d['name']) ?></span>
                                <i class="fa fa-chevron-right small text-muted"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

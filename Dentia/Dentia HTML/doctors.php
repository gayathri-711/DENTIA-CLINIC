<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Doctors Directory & Medical Specialist Showcase
 */
$pageTitle = "Specialist Doctors & Medical Faculty";
require_once __DIR__ . '/includes/header.php';

$deptFilter = isset($_GET['department_id']) ? intval($_GET['department_id']) : null;
$departments = get_departments(true);
$doctors = get_doctors($deptFilter);
?>

<!-- Breadcrumb Header -->
<div class="py-5 bg-light border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Clinical Faculty</span>
                <h1 class="fw-bold text-primary mb-2">Our Medical Specialists</h1>
                <p class="text-muted mb-0">Discover board-certified physicians, clinical researchers, and master surgeons across 12 multidisciplinary departments.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <a href="booking.php" class="btn btn-med-primary"><i class="fa fa-calendar-check me-2"></i> Book Appointment</a>
            </div>
        </div>
    </div>
</div>

<!-- Main Doctors Grid -->
<div class="py-5">
    <div class="container">
        <!-- Department Filter Pills -->
        <div class="d-flex flex-wrap gap-2 justify-content-center mb-5">
            <a href="doctors.php" class="btn btn-sm <?= !$deptFilter ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill px-3 py-2 fw-bold">
                All Departments
            </a>
            <?php foreach ($departments as $d): ?>
                <a href="doctors.php?department_id=<?= e($d['id']) ?>" class="btn btn-sm <?= $deptFilter == $d['id'] ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill px-3 py-2 fw-bold">
                    <i class="fa <?= e($d['icon']) ?> me-1"></i> <?= e($d['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($doctors)): ?>
            <div class="row g-4">
                <?php foreach ($doctors as $doc): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="doctor-profile-card h-100 d-flex flex-column">
                            <img src="<?= e($doc['avatar']) ?>" class="doctor-profile-img" alt="<?= e($doc['name']) ?>">
                            <div class="doctor-profile-body d-flex flex-column flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill"><?= e($doc['department_name'] ?? 'Department') ?></span>
                                    <span class="small text-muted"><i class="fa fa-award me-1 text-warning"></i> <?= e($doc['experience_years']) ?> Yrs Exp</span>
                                </div>
                                <h4 class="fw-bold mb-1 text-primary"><?= e($doc['name']) ?></h4>
                                <p class="text-secondary fw-semibold small mb-2"><?= e($doc['specialization']) ?></p>
                                <p class="small text-muted mb-2"><i class="fa fa-graduation-cap me-1"></i> <?= e($doc['qualification']) ?></p>
                                <p class="small text-muted mb-3"><i class="fa fa-door-open me-1"></i> <?= e($doc['room_no']) ?></p>
                                <p class="small text-muted mb-3"><i class="fa fa-calendar-days me-1"></i> <?= e($doc['availability_days']) ?> &bull; <?= e($doc['availability_hours']) ?></p>
                                <p class="small text-muted mb-4 flex-grow-1"><?= e($doc['bio']) ?></p>

                                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                    <div>
                                        <small class="text-muted d-block">Consultation Fee</small>
                                        <strong class="text-primary fs-5">$<?= number_format($doc['consultation_fee'], 2) ?></strong>
                                    </div>
                                    <a href="booking.php?department_id=<?= e($doc['department_id']) ?>&doctor_id=<?= e($doc['id']) ?>" class="btn btn-med-primary px-4 fw-bold">
                                        <i class="fa fa-calendar-check me-1"></i> Book Visit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info rounded-4 p-5 text-center shadow-sm">
                <i class="fa fa-user-doctor fs-1 text-primary mb-3 d-block"></i>
                <h4 class="fw-bold">No doctors found for this filter</h4>
                <p class="text-muted">Please select another speciality department or contact our central reception desk for scheduling assistance.</p>
                <a href="doctors.php" class="btn btn-primary mt-2">View All Doctors</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

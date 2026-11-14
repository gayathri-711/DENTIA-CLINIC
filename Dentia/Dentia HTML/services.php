<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Hospital Services, Diagnostic & Surgical Procedures
 */
$pageTitle = "Hospital Clinical Services & Diagnostic Wings";
require_once __DIR__ . '/includes/header.php';
$departments = get_departments(true);
?>

<!-- Breadcrumb Header -->
<div class="py-5 bg-light border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Hospital Services</span>
                <h1 class="fw-bold text-primary mb-2">Clinical & Surgical Services</h1>
                <p class="text-muted mb-0">From preventive health checks to complex robotic surgeries, discover our full spectrum of specialized healthcare services.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <a href="booking.php" class="btn btn-med-primary"><i class="fa fa-calendar-check me-2"></i> Book Appointment</a>
            </div>
        </div>
    </div>
</div>

<div class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($departments as $dept): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="med-card d-flex flex-column p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="med-card-icon">
                                <i class="fa <?= e($dept['icon']) ?>"></i>
                            </div>
                            <span class="badge bg-light text-primary border px-3 py-2 fw-bold"><?= e($dept['code']) ?></span>
                        </div>
                        <h4 class="fw-bold mb-2">
                            <a href="department-details.php?slug=<?= e($dept['slug']) ?>" class="text-dark text-decoration-none">
                                <?= e($dept['name']) ?>
                            </a>
                        </h4>
                        <p class="text-muted flex-grow-1 mb-4"><?= e($dept['short_desc']) ?></p>
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="department-details.php?slug=<?= e($dept['slug']) ?>" class="text-primary fw-bold text-decoration-none">
                                View Services <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                            <a href="booking.php?department_id=<?= e($dept['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Consult
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

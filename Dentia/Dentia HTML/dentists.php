<?php
/**
 * Doctor Dental Clinic Dentia - Experienced Dentists & Specialists
 */
$pageTitle = "Our Experienced Dental Specialists";
require_once __DIR__ . '/includes/header.php';

$dentists = get_all_dentists(true);
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <!-- Subheader -->
    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Clinical Team</span>
                    <h1 class="fw-bold mb-2">Our Experienced Dentists</h1>
                    <div class="border-bottom my-3 mx-auto" style="width: 60px; border-width: 3px !important; border-color: var(--dentia-primary) !important;"></div>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold">Dentists</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Dentists List Section -->
    <section class="py-5 bg-light">
        <div class="container py-3">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <span class="section-subtitle-modern">Medical Credentials</span>
                    <h2 class="fw-bold mt-2">Specialists Dedicated to Your Comfort</h2>
                    <p class="text-muted lead">Every dentist at Dentia holds board certifications, extensive surgical and aesthetic experience, and an empathetic approach to patient comfort.</p>
                </div>
            </div>

            <div class="row g-4 gy-5">
                <?php foreach ($dentists as $index => $dentist): ?>
                    <div class="col-md-12">
                        <div class="card border-0 rounded-4 shadow-sm overflow-hidden bg-white">
                            <div class="row g-0 align-items-center">
                                <div class="col-lg-4 col-md-5">
                                    <div class="position-relative h-100">
                                        <img src="<?= e($dentist['image']) ?>" alt="<?= e($dentist['name']) ?>" class="w-100 h-100 object-fit-cover" style="min-height: 340px;">
                                        <span class="badge bg-success position-absolute top-0 start-0 m-3 rounded-pill px-3 py-2 shadow-sm">
                                            <i class="fa fa-check-circle me-1"></i> <?= e($dentist['experience']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-7">
                                    <div class="p-4 p-lg-5">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                            <div>
                                                <h3 class="fw-bold mb-1 text-dark"><?= e($dentist['name']) ?></h3>
                                                <div class="text-primary fw-semibold fs-5"><?= e($dentist['specialization']) ?></div>
                                            </div>
                                            <a href="booking.php?dentist_id=<?= $dentist['id'] ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                                <i class="fa fa-calendar-check-o me-2"></i> Book with Doctor
                                            </a>
                                        </div>

                                        <div class="my-3 py-3 border-top border-bottom">
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <span class="text-muted small d-block">Academic Qualification:</span>
                                                    <strong class="text-dark small"><i class="fa fa-graduation-cap text-primary me-1"></i> <?= e($dentist['qualification']) ?></strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted small d-block">Weekly Availability:</span>
                                                    <strong class="text-dark small"><i class="fa fa-clock-o text-primary me-1"></i> <?= e($dentist['availability_days']) ?> (<?= e($dentist['availability_hours']) ?>)</strong>
                                                </div>
                                            </div>
                                        </div>

                                        <p class="text-muted mb-4" style="line-height: 1.7;">
                                            <?= e($dentist['description']) ?>
                                        </p>

                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if (!empty($dentist['phone'])): ?>
                                                    <a href="tel:<?= e($dentist['phone']) ?>" class="text-muted text-decoration-none small">
                                                        <i class="fa fa-phone text-primary me-1"></i> <?= e($dentist['phone']) ?>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($dentist['email'])): ?>
                                                    <a href="mailto:<?= e($dentist['email']) ?>" class="text-muted text-decoration-none small">
                                                        <i class="fa fa-envelope text-primary me-1"></i> <?= e($dentist['email']) ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                            <div class="social-icons">
                                                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                                                <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                                                <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


<?php
/**
 * Doctor Dental Clinic Dentia - All Dental Services Showcase
 */
$pageTitle = "Comprehensive Dental Services";
require_once __DIR__ . '/includes/header.php';

$allServices = get_all_services();
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <!-- Subheader -->
    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Advanced Dental Healthcare</span>
                    <h1 class="fw-bold mb-2">Our Specialized Dental Services</h1>
                    <div class="border-bottom my-3 mx-auto" style="width: 60px; border-width: 3px !important; border-color: var(--dentia-primary) !important;"></div>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold">All Services</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid & Real-time Search -->
    <section class="py-5 bg-light">
        <div class="container py-3">
            
            <!-- Search & Filter Controls -->
            <div class="row justify-content-between align-items-center mb-5 g-3">
                <div class="col-md-6 col-lg-5">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white border">
                        <span class="input-group-text bg-white border-0 ps-3 text-muted">
                            <i class="fa fa-search"></i>
                        </span>
                        <input type="text" id="service_search_input" class="form-control border-0 py-2 shadow-none" placeholder="Search dental procedures (e.g. whitening, implant, braces)...">
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 text-md-end text-muted small">
                    <i class="fa fa-stethoscope text-primary me-1"></i> Showing <strong><?= count($allServices) ?></strong> Board-Certified Procedures
                </div>
            </div>

            <!-- Service Cards Row -->
            <div class="row g-4" id="services_container">
                <?php foreach ($allServices as $service): ?>
                    <div class="col-lg-4 col-md-6 service-filter-item">
                        <div class="service-card-modern">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="service-icon-box">
                                    <img src="<?= e($service['icon']) ?>" alt="<?= e($service['name']) ?>" style="max-height: 40px; max-width: 40px;">
                                </div>
                                <span class="badge bg-light text-primary border rounded-pill px-3 py-2 small">
                                    <i class="fa fa-clock-o me-1"></i> <?= e($service['duration']) ?>
                                </span>
                            </div>

                            <h4 class="fw-bold mb-2">
                                <a href="service-<?= e($service['slug']) ?>.php" class="text-dark text-decoration-none">
                                    <?= e($service['name']) ?>
                                </a>
                            </h4>
                            <p class="text-muted flex-grow-1 small mb-4">
                                <?= e($service['short_description']) ?>
                            </p>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <div>
                                    <span class="small text-muted d-block">Starting from</span>
                                    <span class="service-price">$<?= number_format($service['price'], 2) ?></span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="service-<?= e($service['slug']) ?>.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                        Details
                                    </a>
                                    <a href="booking.php?service_id=<?= $service['id'] ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Emergency Consultation Banner -->
            <div class="emergency-callout-box mt-5">
                <div class="row align-items-center justify-content-between g-4">
                    <div class="col-lg-8">
                        <h3 class="text-white fw-bold mb-2">Not Sure Which Dental Treatment You Need?</h3>
                        <p class="text-white mb-0" style="opacity: 0.95;">
                            Schedule a comprehensive diagnostic consultation with our lead dental team. We will perform 3D digital imaging and craft a personalized treatment plan for your smile.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="booking.php" class="btn btn-light text-primary btn-lg rounded-pill px-4 fw-bold shadow">
                            <i class="fa fa-calendar-check-o me-2"></i> Schedule Consultation
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

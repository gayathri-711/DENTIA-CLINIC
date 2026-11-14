<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Facilities & Technology Virtual Tour Gallery
 */
$pageTitle = "Facilities & Technology Virtual Tour";
require_once __DIR__ . '/includes/header.php';

$galleryItems = [
    ['img' => 'images/gallery/l1.webp', 'title' => '3.0 Tesla Silent MRI Suite', 'category' => 'Radiology', 'desc' => 'High-resolution anatomical cross-sectional neuro and cardiac scanning.'],
    ['img' => 'images/gallery/l2.webp', 'title' => 'Sterility & Infection Control Zone', 'category' => 'Clinical Safety', 'desc' => 'NABH certified zero-contamination sterilization protocols.'],
    ['img' => 'images/gallery/l3.webp', 'title' => 'Executive Outpatient Consultation Suites', 'category' => 'Outpatient', 'desc' => 'Private consulting suites for multi-speciality clinical evaluations.'],
    ['img' => 'images/gallery/l4.webp', 'title' => 'Modular Laminar Flow Operating Theater', 'category' => 'Surgical OR', 'desc' => 'HEPA-filtered modular surgical theater with robotic instrumentation.'],
    ['img' => 'images/gallery/l5.webp', 'title' => 'Level-III Neonatal ICU Carestation', 'category' => 'Pediatrics', 'desc' => 'Advanced premature infant incubators and life-support monitors.'],
    ['img' => 'images/gallery/l6.webp', 'title' => 'Digital Biplane Cardiac Cath Lab', 'category' => 'Cardiology', 'desc' => '24/7 primary percutaneous coronary intervention and angioplasty.'],
    ['img' => 'images/gallery/l7.webp', 'title' => 'Automated Pathology Laboratory', 'category' => 'Diagnostics', 'desc' => 'Robotic track chemistry and molecular diagnostic analyzers.'],
    ['img' => 'images/gallery/l8.webp', 'title' => 'Inpatient Patient Recovery Suite', 'category' => 'Inpatient', 'desc' => 'Ergonomic electric beds with continuous wireless vital telemetry.'],
    ['img' => 'images/gallery/l9.webp', 'title' => 'Level-I Trauma Resuscitation Bays', 'category' => 'Emergency', 'desc' => 'Immediate life-support triage station connected to trauma team.']
];
?>

<!-- Breadcrumb Header -->
<div class="py-5 bg-light border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Hospital Campus</span>
                <h1 class="fw-bold text-primary mb-2">Facilities & Technology Tour</h1>
                <p class="text-muted mb-0">Explore our cutting-edge clinical infrastructure, surgical suites, and dedicated intensive care environments.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <a href="booking.php" class="btn btn-med-primary"><i class="fa fa-calendar-check me-2"></i> Book Appointment</a>
            </div>
        </div>
    </div>
</div>

<!-- Gallery Grid -->
<div class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($galleryItems as $item): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <div class="position-relative" style="height: 240px; overflow: hidden;">
                            <img src="<?= e($item['img']) ?>" class="w-100 h-100 object-fit-cover" alt="<?= e($item['title']) ?>">
                            <span class="badge bg-primary position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill"><?= e($item['category']) ?></span>
                        </div>
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <h5 class="fw-bold text-primary mb-2"><?= e($item['title']) ?></h5>
                            <p class="text-muted small mb-0 flex-grow-1"><?= e($item['desc']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
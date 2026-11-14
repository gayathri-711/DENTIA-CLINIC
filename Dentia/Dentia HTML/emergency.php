<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * 24/7 Emergency & Trauma Centre Protocols
 */
$pageTitle = "24/7 Emergency & Trauma Center";
require_once __DIR__ . '/includes/header.php';
$settings = get_hospital_settings();
?>

<!-- Emergency Banner -->
<div class="py-5 bg-danger text-white">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-white text-danger px-3 py-2 text-uppercase fw-bold mb-3">Level-I Comprehensive Trauma Center</span>
                <h1 class="display-5 fw-bold mb-2">24/7 Emergency & Resuscitation Unit</h1>
                <p class="lead mb-0 text-white-50">Immediate, lifesaving medical intervention for acute myocardial infarction, acute stroke, polytrauma, and severe pediatric emergencies.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <div class="p-3 bg-white text-dark rounded-4 shadow d-inline-block text-start">
                    <small class="text-danger fw-bold d-block"><i class="fa fa-phone-volume me-1"></i> EMERGENCY DIRECT HOTLINE</small>
                    <a href="tel:<?= e($settings['emergency_phone']) ?>" class="fs-4 fw-bold text-danger text-decoration-none"><?= e($settings['emergency_phone']) ?></a>
                    <small class="text-muted d-block mt-1"><i class="fa fa-truck-medical me-1"></i> Ambulance: <strong><?= e($settings['ambulance_phone']) ?></strong></small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="py-5 bg-light">
    <div class="container">
        <!-- Triage Protocol Grid -->
        <h3 class="fw-bold text-primary mb-4 text-center">Emergency Rapid Response Protocols</h3>
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border h-100 border-top border-4 border-danger">
                    <div class="d-flex align-items-center gap-2 text-danger mb-3">
                        <i class="fa fa-heart-pulse fs-2"></i>
                        <h5 class="fw-bold mb-0">Code STEMI (Heart Attack)</h5>
                    </div>
                    <p class="text-muted small">Door-to-Balloon time under 60 minutes. Direct transfer from ambulance bay to biplane catheterization laboratory for emergency coronary angioplasty.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border h-100 border-top border-4 border-primary">
                    <div class="d-flex align-items-center gap-2 text-primary mb-3">
                        <i class="fa fa-brain fs-2"></i>
                        <h5 class="fw-bold mb-0">Code Stroke (Brain Attack)</h5>
                    </div>
                    <p class="text-muted small">Rapid door-to-needle thrombolysis and endovascular mechanical thrombectomy team on standing standby 24/7 with immediate non-contrast head CT access.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border h-100 border-top border-4 border-warning">
                    <div class="d-flex align-items-center gap-2 text-warning mb-3">
                        <i class="fa fa-truck-medical fs-2"></i>
                        <h5 class="fw-bold mb-0">Code Trauma (Accident Bay)</h5>
                    </div>
                    <p class="text-muted small">Multidisciplinary trauma response team consisting of trauma surgeons, orthopedic consultants, anesthesiologists, and blood bank transfusion support.</p>
                </div>
            </div>
        </div>

        <!-- Emergency Address & Directions -->
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h4 class="fw-bold text-primary mb-3">Trauma Bay Location & Directions</h4>
                    <p class="text-muted mb-3">The MEDINOVA Emergency Bay features a dedicated sheltered ambulance ramp and rapid patient triage drop-off point, isolated from outpatient clinic traffic for swift access.</p>
                    <p class="mb-2"><strong><i class="fa fa-location-dot text-danger me-2"></i> Address:</strong> <?= e($settings['address']) ?></p>
                    <p class="mb-2"><strong><i class="fa fa-phone text-primary me-2"></i> Main Telephone:</strong> <?= e($settings['phone']) ?></p>
                    <p class="mb-0"><strong><i class="fa fa-clock text-success me-2"></i> Hours of Service:</strong> 24 Hours / 7 Days a Week / 365 Days a Year</p>
                </div>
                <div class="col-lg-6">
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm">
                        <iframe src="<?= e($settings['google_maps']) ?>" loading="lazy" style="border:0;" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

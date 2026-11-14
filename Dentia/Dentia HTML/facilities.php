<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Hospital Facilities & Advanced Infrastructure
 */
$pageTitle = "Hospital Facilities & Technology";
require_once __DIR__ . '/includes/header.php';
$settings = get_hospital_settings();
?>

<!-- Breadcrumb Header -->
<div class="py-5 bg-light border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Tertiary Infrastructure</span>
                <h1 class="fw-bold text-primary mb-2">Hospital Facilities & Technology</h1>
                <p class="text-muted mb-0">Engineered with international NABH & JCI patient safety standards, modular laminar surgical suites, and 24/7 advanced life support.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <a href="booking.php" class="btn btn-med-primary"><i class="fa fa-calendar-check me-2"></i> Book Appointment</a>
            </div>
        </div>
    </div>
</div>

<div class="py-5">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6">
                <div class="med-card p-4">
                    <div class="med-card-icon bg-primary bg-opacity-10 text-primary mb-3">
                        <i class="fa fa-x-ray"></i>
                    </div>
                    <h4 class="fw-bold mb-2">3.0 Tesla Silent MRI & CT</h4>
                    <p class="text-muted">High-resolution anatomical imaging suite with reduced scan acoustic noise, wide-bore comfort for claustrophobic patients, and rapid sub-millimeter stroke diagnosis.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="med-card p-4">
                    <div class="med-card-icon bg-danger bg-opacity-10 text-danger mb-3">
                        <i class="fa fa-heart-pulse"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Digital Biplane Cath Lab</h4>
                    <p class="text-muted">Interventional cardiology suite equipped for primary emergency percutaneous transluminal coronary angioplasty (PTCA), complex stent placements, and electrophysiology studies.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="med-card p-4">
                    <div class="med-card-icon bg-success bg-opacity-10 text-success mb-3">
                        <i class="fa fa-bed-pulse"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Critical Care (ICU / CCU / NICU)</h4>
                    <p class="text-muted">Multi-parameter hemodynamic patient monitoring, automated volumetric infusion pumps, dedicated HEPA-filtered isolation rooms, and 1:1 specialist nursing coverage.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="med-card p-4">
                    <div class="med-card-icon bg-secondary bg-opacity-10 text-secondary mb-3">
                        <i class="fa fa-robot"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Robotic Modular Operation Theatres</h4>
                    <p class="text-muted">16 ultra-clean surgical suites with vertical laminar airflows, anti-microbial seamless flooring, 4K laparoscopy visual towers, and robotic joint navigation systems.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="med-card p-4">
                    <div class="med-card-icon bg-warning bg-opacity-10 text-warning mb-3">
                        <i class="fa fa-pills"></i>
                    </div>
                    <h4 class="fw-bold mb-2">24/7 Central Pharmacy</h4>
                    <p class="text-muted">Fully stocked hospital formulary dispensing temperature-controlled cold-chain biologics, oncology infusions, and prescription medications with electronic barcode verification.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="med-card p-4">
                    <div class="med-card-icon bg-info bg-opacity-10 text-info mb-3">
                        <i class="fa fa-truck-medical"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Mobile ICU Ambulance Fleet</h4>
                    <p class="text-muted">Advanced cardiac life support (ACLS) transport vehicles staffed by emergency physicians and paramedics, featuring onboard defibrillators, transport ventilators, and telemetry.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


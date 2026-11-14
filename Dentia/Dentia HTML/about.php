<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * About Hospital Overview, Vision, Mission & Standards
 */
$pageTitle = "About MEDINOVA Hospital";
require_once __DIR__ . '/includes/header.php';
$settings = get_hospital_settings();
?>

<!-- Breadcrumb Header -->
<div class="py-5 bg-light border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Hospital Profile</span>
                <h1 class="fw-bold text-primary mb-2">About MEDINOVA</h1>
                <p class="text-muted mb-0">Committed to clinical excellence, patient-centric compassion, and cutting-edge healthcare technology across 12 tertiary medical specialities.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <a href="booking.php" class="btn btn-med-primary"><i class="fa fa-calendar-check me-2"></i> Book Appointment</a>
            </div>
        </div>
    </div>
</div>

<!-- Hospital Overview Section -->
<div class="py-5">
    <div class="container">
        <div class="row align-items-center g-5 mb-5">
            <div class="col-lg-6">
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-3">Our Foundation</span>
                <h2 class="fw-bold text-primary mb-4">Dedicated to Transforming Patient Lives for Over 20 Years</h2>
                <p class="fs-5 text-muted lh-base mb-3">
                    Founded with the vision to deliver world-class medical care at international benchmarks, <strong>MEDINOVA Multi-Speciality Hospital</strong> has grown into a leading tertiary referral center serving thousands of patients annually.
                </p>
                <p class="text-muted mb-4">
                    Our 450-bed quaternary facility integrates 12 Centers of Excellence—including Cardiovascular Sciences, Neurosciences, Robotic Orthopedics, Oncology, and Comprehensive Emergency Care. We pair internationally trained clinicians with AI-driven diagnostic imaging and precision surgical robotics to ensure the highest standard of healing.
                </p>
                <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                    <p class="fst-italic text-dark mb-0 fw-semibold">
                        "At MEDINOVA, our motto is 'Advanced Healthcare, Better Tomorrow.' We treat the patient, not just the symptom, fostering trust through transparent clinical decisions and compassionate care."
                    </p>
                    <small class="text-muted d-block mt-2">— Dr. Arthur Sterling, Chief Medical Officer</small>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="images/misc/l2.webp" class="img-fluid rounded-4 shadow-lg w-100" alt="MEDINOVA Infrastructure" style="max-height: 440px; object-fit: cover;">
                </div>
            </div>
        </div>

        <!-- Vision, Mission & Core Values -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 d-inline-block fs-3 mb-3">
                        <i class="fa fa-bullseye"></i>
                    </div>
                    <h4 class="fw-bold text-primary mb-2">Our Vision</h4>
                    <p class="text-muted mb-0">To be the most trusted healthcare institution in the region, recognized globally for clinical excellence, pioneering research, and empathetic patient outcomes.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="rounded-3 bg-secondary bg-opacity-10 text-secondary p-3 d-inline-block fs-3 mb-3">
                        <i class="fa fa-hand-holding-heart"></i>
                    </div>
                    <h4 class="fw-bold text-secondary mb-2">Our Mission</h4>
                    <p class="text-muted mb-0">To provide accessible, ethical, and high-precision multi-speciality medical care through continuous technological innovation and patient-centered service.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 d-inline-block fs-3 mb-3">
                        <i class="fa fa-shield-halved"></i>
                    </div>
                    <h4 class="fw-bold text-success mb-2">Healthcare Standards</h4>
                    <p class="text-muted mb-0">Fully accredited by Joint Commission International (JCI) and NABH. We maintain strict zero-tolerance infection prevention protocols and transparent electronic medical records.</p>
                </div>
            </div>
        </div>

        <!-- Infrastructure Highlights -->
        <div class="bg-light p-4 p-md-5 rounded-4 border">
            <h3 class="fw-bold text-primary mb-4 text-center">World-Class Infrastructure & Technology</h3>
            <div class="row g-4">
                <div class="col-md-3 col-sm-6 text-center">
                    <div class="display-5 fw-bold text-primary mb-1">450+</div>
                    <h6 class="fw-bold mb-1">Inpatient Beds</h6>
                    <small class="text-muted">Including ICU, CCU & Private Suites</small>
                </div>
                <div class="col-md-3 col-sm-6 text-center">
                    <div class="display-5 fw-bold text-secondary mb-1">16</div>
                    <h6 class="fw-bold mb-1">Modular OTs</h6>
                    <small class="text-muted">HEPA laminar airflow surgical suites</small>
                </div>
                <div class="col-md-3 col-sm-6 text-center">
                    <div class="display-5 fw-bold text-success mb-1">12</div>
                    <h6 class="fw-bold mb-1">Specialities</h6>
                    <small class="text-muted">Comprehensive multidisciplinary departments</small>
                </div>
                <div class="col-md-3 col-sm-6 text-center">
                    <div class="display-5 fw-bold text-danger mb-1">24/7</div>
                    <h6 class="fw-bold mb-1">Trauma & ER</h6>
                    <small class="text-muted">Emergency catheterization & stroke care</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

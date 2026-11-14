<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Global Responsive Public Footer
 */
$footerSettings = get_hospital_settings();
?>
<!-- Hospital Emergency Banner -->
<div class="py-4 bg-primary text-white" style="background: linear-gradient(135deg, var(--med-primary-dark), var(--med-secondary-dark)) !important;">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-danger d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 24px;">
                        <i class="fa fa-truck-medical"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 text-white fw-bold">Medical Emergency? Need an Ambulance?</h4>
                        <p class="mb-0 text-white-50">Our Level-I Trauma Center and Rapid Response Cardiac Team operate 24 hours a day, 365 days a year.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="tel:<?= e($footerSettings['emergency_phone']) ?>" class="btn btn-danger btn-lg rounded-pill px-4 fw-bold shadow">
                    <i class="fa fa-phone-volume me-2"></i> Call <?= e($footerSettings['emergency_phone']) ?>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Footer -->
<footer class="med-footer">
    <div class="container">
        <div class="row g-4 mb-5">
            <!-- Brand Overview -->
            <div class="col-lg-4 col-md-6">
                <div class="med-logo mb-3">
                    <div class="med-logo-icon">
                        <i class="fa fa-heart-pulse"></i>
                    </div>
                    <div>
                        <div class="med-logo-text text-white">MEDINOVA</div>
                        <div class="med-logo-tagline text-info">Advanced Healthcare</div>
                    </div>
                </div>
                <p class="text-white-50 mb-4">
                    MEDINOVA is a state-of-the-art tertiary hospital dedicated to clinical excellence, patient-centric compassion, and cutting-edge healthcare technology across 12 medical specialities.
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- 12 Speciality Departments -->
            <div class="col-lg-3 col-md-6">
                <h5>Speciality Centers</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="department-details.php?slug=cardiology"><i class="fa fa-angle-right me-2 text-info"></i> Cardiology & Cath Lab</a></li>
                    <li class="mb-2"><a href="department-details.php?slug=neurology"><i class="fa fa-angle-right me-2 text-info"></i> Neurology & Stroke Unit</a></li>
                    <li class="mb-2"><a href="department-details.php?slug=orthopedics"><i class="fa fa-angle-right me-2 text-info"></i> Robotic Joint Surgery</a></li>
                    <li class="mb-2"><a href="department-details.php?slug=pediatrics"><i class="fa fa-angle-right me-2 text-info"></i> Pediatrics & Level-III NICU</a></li>
                    <li class="mb-2"><a href="department-details.php?slug=oncology"><i class="fa fa-angle-right me-2 text-info"></i> Comprehensive Oncology</a></li>
                    <li class="mb-2"><a href="departments.php" class="text-info fw-bold"><i class="fa fa-arrow-right me-1"></i> View All 12 Departments</a></li>
                </ul>
            </div>

            <!-- Quick Portals & Links -->
            <div class="col-lg-2 col-md-6">
                <h5>Hospital Portals</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="login.php?role=patient"><i class="fa fa-angle-right me-2 text-info"></i> Patient Portal</a></li>
                    <li class="mb-2"><a href="login.php?role=doctor"><i class="fa fa-angle-right me-2 text-info"></i> Doctor Portal</a></li>
                    <li class="mb-2"><a href="login.php?role=receptionist"><i class="fa fa-angle-right me-2 text-info"></i> Reception Desk</a></li>
                    <li class="mb-2"><a href="login.php?role=nurse"><i class="fa fa-angle-right me-2 text-info"></i> Inpatient Nursing</a></li>
                    <li class="mb-2"><a href="login.php?role=lab_technician"><i class="fa fa-angle-right me-2 text-info"></i> Diagnostic Lab</a></li>
                    <li class="mb-2"><a href="login.php?role=pharmacist"><i class="fa fa-angle-right me-2 text-info"></i> Pharmacy Dispensary</a></li>
                    <li class="mb-2"><a href="login.php?role=admin"><i class="fa fa-angle-right me-2 text-info"></i> Admin Dashboard</a></li>
                </ul>
            </div>

            <!-- Contact & Hours -->
            <div class="col-lg-3 col-md-6">
                <h5>Hospital Address</h5>
                <p class="text-white-50 mb-2">
                    <i class="fa fa-map-marker-alt me-2 text-danger"></i> <?= e($footerSettings['address']) ?>
                </p>
                <p class="text-white-50 mb-2">
                    <i class="fa fa-phone me-2 text-info"></i> <?= e($footerSettings['phone']) ?>
                </p>
                <p class="text-white-50 mb-2">
                    <i class="fa fa-envelope me-2 text-info"></i> <?= e($footerSettings['email']) ?>
                </p>
                <div class="mt-3 p-3 rounded-3" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    <small class="text-white d-block fw-bold"><i class="fa fa-shield-heart me-1 text-success"></i> NABH & JCI Accredited</small>
                    <small class="text-white-50">Certified for international patient safety & clinical standards.</small>
                </div>
            </div>
        </div>

        <hr class="border-secondary mb-4 opacity-25">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <small class="text-white-50">&copy; <?= date('Y') ?> <strong>MEDINOVA Multi-Speciality Hospital</strong>. All rights reserved.</small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-white-50">Designed with Advanced Clinical Standards &bull; HIPAA & HL7 Compliant</small>
            </div>
        </div>
    </div>
</footer>

<!-- Core Scripts -->
<script src="js/plugins.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/custom-app.js"></script>

</body>
</html>

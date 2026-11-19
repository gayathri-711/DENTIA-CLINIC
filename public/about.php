<?php
/**
 * Doctor Dental Clinic Dentia - About Clinic & Heritage
 */
$pageTitle = "About Our Clinic & Philosophy";
require_once __DIR__ . '/includes/header.php';

$dentists = get_all_dentists(true);
$clinicSettings = get_clinic_settings();
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <!-- Subheader -->
    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Our Mission & Care</span>
                    <h1 class="fw-bold mb-2">About Doctor Dental Clinic Dentia</h1>
                    <div class="border-bottom my-3 mx-auto" style="width: 60px; border-width: 3px !important; border-color: var(--dentia-primary) !important;"></div>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold">About Us</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Clinic Intro & Philosophy -->
    <section class="py-5 bg-white">
        <div class="container py-3">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="section-subtitle-modern">Our Story</span>
                    <h2 class="fw-bold mt-2 mb-4">Dedicated to Exceptional Dental Health For Over 15 Years</h2>
                    <p class="lead text-dark" style="line-height: 1.8;">
                        Doctor Dental Clinic Dentia was founded with a singular conviction: dental visits should be welcoming, transparent, and completely free of discomfort.
                    </p>
                    <p class="text-muted" style="line-height: 1.8;">
                        Located in the heart of New York, our practice combines compassionate patient care with the gold standard of contemporary dental science. From routine checkups to intricate smile makeovers and guided implant surgery, we treat every patient like family.
                    </p>

                    <div class="row g-3 my-4">
                        <div class="col-sm-6">
                            <div class="border rounded-3 p-3 bg-light">
                                <h4 class="fw-bold text-primary mb-1">10,000+</h4>
                                <div class="small text-muted">Successful Smile Transformations</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded-3 p-3 bg-light">
                                <h4 class="fw-bold text-success mb-1">99.2%</h4>
                                <div class="small text-muted">Implant Integration Success Rate</div>
                            </div>
                        </div>
                    </div>

                    <a href="booking.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow">
                        <i class="fa fa-calendar-check-o me-2"></i> Book Your First Visit
                    </a>
                </div>

                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="images/misc/l2.webp" alt="Clinic Interior" class="img-fluid rounded-4 shadow-lg w-100">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values Section -->
    <section class="py-5 bg-light">
        <div class="container py-3">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <span class="section-subtitle-modern">Our Pillars</span>
                    <h2 class="fw-bold mt-2">The Principles That Guide Our Care</h2>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 rounded-4 p-4 shadow-sm h-100 bg-white">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; font-size: 22px;">
                            <i class="fa fa-heart"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Patient Empathy</h5>
                        <p class="text-muted small mb-0">We listen carefully to your concerns, respect your time, and customize every procedure to minimize anxiety and discomfort.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 rounded-4 p-4 shadow-sm h-100 bg-white">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; font-size: 22px;">
                            <i class="fa fa-shield"></i>
                        </div>
                        <h5 class="fw-bold mb-2">OSHA Sterility Protocol</h5>
                        <p class="text-muted small mb-0">Our hospital-grade sterilization center uses multi-stage autoclave cycles and spore-testing to ensure pristine patient safety.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 rounded-4 p-4 shadow-sm h-100 bg-white">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; font-size: 22px;">
                            <i class="fa fa-desktop"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Digital Accuracy</h5>
                        <p class="text-muted small mb-0">Digital impression scanners, 3D Cone Beam Computed Tomography (CBCT), and computer-guided ceramic milling.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


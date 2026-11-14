<?php
/**
 * Doctor Dental Clinic Dentia - Modern Homepage
 */
$pageTitle = "Advanced & Compassionate Dental Care";
require_once __DIR__ . '/includes/header.php';

$services = get_all_services(true); // featured services
$dentists = get_all_dentists(true);
$testimonials = get_all_testimonials(true);
$blogs = get_recent_blogs(3);
?>

<!-- Content begin -->
<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <!-- Hero Section Begin -->
    <section class="section-dark text-light no-top no-bottom position-relative overflow-hidden" style="background: linear-gradient(135deg, #0b2545 0%, #134074 100%); min-height: 85vh; display: flex; align-items: center;">
        <div class="container position-relative z-2 py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-primary px-3 py-2 rounded-pill mb-3 text-uppercase fw-bold" style="letter-spacing: 1px;">
                        <i class="fa fa-certificate me-1"></i> Board Certified Dental Specialists
                    </span>
                    <h1 class="display-4 fw-bold mb-3 text-white">
                        Excellence in Modern Dentistry & Beautiful Smiles
                    </h1>
                    <p class="lead text-light mb-4" style="opacity: 0.9;">
                        Experience pain-free, state-of-the-art dental care tailored for your family. From cosmetic smile makeovers and dental implants to routine pediatric checkups.
                    </p>
                    
                    <div class="d-flex flex-wrap gap-3 mb-5">
                        <a href="booking.php" class="btn btn-primary btn-lg rounded-pill px-4 py-3 shadow-lg fw-bold">
                            <i class="fa fa-calendar-check-o me-2"></i> Book Appointment
                        </a>
                        <a href="services.php" class="btn btn-outline-light btn-lg rounded-pill px-4 py-3 fw-bold">
                            <i class="fa fa-th-list me-2"></i> Explore Services
                        </a>
                    </div>

                    <!-- Quick Highlights -->
                    <div class="row g-3 pt-3 border-top border-secondary">
                        <div class="col-sm-4 col-6">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-check-circle text-info fs-4 me-2"></i>
                                <div>
                                    <div class="fw-bold text-white">15+ Years</div>
                                    <div class="small text-white-50">Clinical Care</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-smile-o text-warning fs-4 me-2"></i>
                                <div>
                                    <div class="fw-bold text-white">10,000+</div>
                                    <div class="small text-white-50">Happy Patients</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-12">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-shield text-success fs-4 me-2"></i>
                                <div>
                                    <div class="fw-bold text-white">100% Sterile</div>
                                    <div class="small text-white-50">OSHA Safety</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 position-relative">
                    <div class="position-relative text-center">
                        <img src="images/misc/l1.webp" alt="Modern Dental Clinic" class="img-fluid rounded-4 shadow-lg border border-3 border-light">
                        
                        <!-- Floating Badge 1 -->
                        <div class="hero-floating-badge badge-1 text-dark text-start">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-circle p-2">
                                    <i class="fa fa-heartbeat fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold mb-0">Gentle & Painless</div>
                                    <small class="text-muted">Advanced sedation options</small>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Badge 2 -->
                        <div class="hero-floating-badge badge-2 text-dark text-start d-none d-sm-block">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-star text-warning fs-5"></i>
                                <i class="fa fa-star text-warning fs-5"></i>
                                <i class="fa fa-star text-warning fs-5"></i>
                                <i class="fa fa-star text-warning fs-5"></i>
                                <i class="fa fa-star text-warning fs-5"></i>
                            </div>
                            <div class="fw-bold mt-1">4.9 / 5.0 Rating</div>
                            <small class="text-muted">Over 1,200 verified reviews</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Quick Booking Bar -->
    <section class="py-4 bg-white shadow-sm border-bottom">
        <div class="container">
            <div class="row align-items-center justify-content-between g-3">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                            <i class="fa fa-clock-o fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Emergency Walk-ins Welcome</h6>
                            <small class="text-muted">Same-day relief for acute dental pain</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger-subtle text-danger p-3 rounded-circle me-3">
                            <i class="fa fa-ambulance fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">24/7 Dental Emergency Hotline</h6>
                            <a href="tel:<?= e($clinicSettings['emergency_phone']) ?>" class="fs-5 fw-bold text-danger text-decoration-none">
                                <?= e($clinicSettings['emergency_phone']) ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 text-lg-end">
                    <a href="booking.php" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold w-100 w-lg-auto">
                        <i class="fa fa-calendar me-2"></i> Book Online Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Dental Services Section -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <span class="section-subtitle-modern">Specialized Dental Care</span>
                    <h2 class="fw-bold mt-2">Comprehensive Dental Services</h2>
                    <p class="text-muted lead">We provide comprehensive preventive, cosmetic, restorative, and surgical dental treatments under one comfortable roof.</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($services as $service): ?>
                    <div class="col-lg-4 col-md-6">
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
                                    <a href="service-<?= e($service['slug']) ?>.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Learn More">
                                        Details
                                    </a>
                                    <a href="booking.php?service_id=<?= $service['id'] ?>" class="btn btn-sm btn-primary rounded-pill px-3" title="Book this procedure">
                                        Book
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-5">
                <a href="services.php" class="btn btn-outline-primary btn-lg rounded-pill px-5 fw-bold">
                    View All 10 Dental Services <i class="fa fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="section-subtitle-modern">The Dentia Standard</span>
                    <h2 class="fw-bold mt-2 mb-4">Why Families Trust Dentia For Their Dental Care</h2>
                    <p class="text-muted mb-4">
                        We combine compassionate clinical expertise with the latest advances in digital dentistry to deliver treatments that are comfortable, swift, and beautifully durable.
                    </p>

                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary text-white rounded-3 p-3 me-3">
                                    <i class="fa fa-laptop fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Digital 3D Dentistry</h6>
                                    <p class="small text-muted mb-0">Low-radiation digital X-rays and precise 3D intraoral scanners.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-success text-white rounded-3 p-3 me-3">
                                    <i class="fa fa-leaf fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Painless Anesthesia</h6>
                                    <p class="small text-muted mb-0">Gentle computerized numbing and anxiety-free sedation options.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-info text-white rounded-3 p-3 me-3">
                                    <i class="fa fa-hospital-o fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Hospital-Grade Sterility</h6>
                                    <p class="small text-muted mb-0">Autoclave sterilization and strict infection control protocols.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-warning text-white rounded-3 p-3 me-3">
                                    <i class="fa fa-credit-card fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Transparent Pricing</h6>
                                    <p class="small text-muted mb-0">No hidden fees, flexible financing, and broad insurance acceptance.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3">
                        <a href="about.php" class="btn btn-outline-primary rounded-pill px-4 fw-semibold">
                            Learn More About Our Clinic
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="images/misc/l4.webp" alt="Doctor Dental Care" class="img-fluid rounded-4 shadow-lg w-100">
                        <div class="position-absolute bottom-0 start-0 m-4 bg-white p-4 rounded-3 shadow-lg d-none d-sm-block border" style="max-width: 320px;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success text-white rounded-circle p-3">
                                    <i class="fa fa-check fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">Same-Day Emergency</h6>
                                    <small class="text-muted">Walk-in relief guaranteed for dental emergencies</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Experienced Dentists Section -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <span class="section-subtitle-modern">Medical Excellence</span>
                    <h2 class="fw-bold mt-2">Meet Our Experienced Dentists</h2>
                    <p class="text-muted lead">Our board-certified dentists bring decades of specialized training from premier medical institutions.</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($dentists as $dentist): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="dentist-card-modern h-100 d-flex flex-column">
                            <div class="dentist-img-wrap">
                                <img src="<?= e($dentist['image']) ?>" alt="<?= e($dentist['name']) ?>">
                                <span class="dentist-badge-active"><i class="fa fa-circle me-1 small"></i> Available</span>
                            </div>
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <h5 class="fw-bold mb-1"><?= e($dentist['name']) ?></h5>
                                <div class="text-primary small fw-semibold mb-2"><?= e($dentist['specialization']) ?></div>
                                <div class="text-muted small mb-3">
                                    <i class="fa fa-graduation-cap me-1"></i> <?= e($dentist['qualification']) ?>
                                </div>
                                
                                <div class="bg-light p-2 rounded-2 small text-muted mb-4 mt-auto">
                                    <i class="fa fa-calendar-o me-1 text-primary"></i> <?= e($dentist['availability_days']) ?> (<?= e($dentist['availability_hours']) ?>)
                                </div>

                                <a href="booking.php?dentist_id=<?= $dentist['id'] ?>" class="btn btn-outline-primary rounded-pill w-100 fw-semibold">
                                    <i class="fa fa-calendar-check-o me-1"></i> Book with Doctor
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-4">
                <a href="dentists.php" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                    View Complete Medical Credentials <i class="fa fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Facilities and Equipment Section -->
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <span class="section-subtitle-modern">State of the Art Clinic</span>
                    <h2 class="fw-bold mt-2">Modern Facilities & Advanced Equipment</h2>
                    <p class="text-muted lead">We invest in cutting-edge dental technology to ensure gentle treatment, accurate diagnosis, and rapid recovery.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 rounded-4 overflow-hidden shadow-sm h-100">
                        <img src="images/gallery/l1.webp" alt="3D Dental Imaging" class="card-img-top" style="height: 220px; object-fit: cover;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-2">3D CBCT Digital Imaging</h5>
                            <p class="text-muted small mb-0">High-resolution panoramic imaging with 90% less radiation for millimeter-precise implant planning.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 rounded-4 overflow-hidden shadow-sm h-100">
                        <img src="images/gallery/l2.webp" alt="Sterilization Suite" class="card-img-top" style="height: 220px; object-fit: cover;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-2">Hospital-Grade Sterilization</h5>
                            <p class="text-muted small mb-0">Class B medical autoclaves, ultrasonic instrument washing, and biological test monitoring.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 rounded-4 overflow-hidden shadow-sm h-100">
                        <img src="images/gallery/l4.webp" alt="Ergonomic Dental Operatories" class="card-img-top" style="height: 220px; object-fit: cover;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-2">Ergonomic Dental Suites</h5>
                            <p class="text-muted small mb-0">Comfort memory foam treatment chairs, ceiling entertainment screens, and noise-canceling headphones.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Patient Testimonials Section -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <span class="section-subtitle-modern">Patient Stories</span>
                    <h2 class="fw-bold mt-2">What Our Patients Say</h2>
                    <p class="text-muted lead">Real feedback from patients who entrusted their smiles to Doctor Dental Clinic Dentia.</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($testimonials as $item): ?>
                    <div class="col-lg-6">
                        <div class="card border-0 rounded-4 shadow-sm p-4 h-100 bg-white">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="text-warning">
                                    <?php for ($i = 0; $i < $item['rating']; $i++): ?>
                                        <i class="fa fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="badge bg-success-subtle text-success small rounded-pill px-3 py-1">
                                    <i class="fa fa-check-circle me-1"></i> Verified Patient
                                </span>
                            </div>
                            <p class="text-dark fst-italic mb-4" style="line-height: 1.7;">
                                "<?= e($item['comment']) ?>"
                            </p>
                            <div class="d-flex align-items-center mt-auto">
                                <img src="<?= e($item['image']) ?>" alt="<?= e($item['patient_name']) ?>" class="rounded-circle me-3" style="width: 52px; height: 52px; object-fit: cover;">
                                <div>
                                    <h6 class="fw-bold mb-0"><?= e($item['patient_name']) ?></h6>
                                    <small class="text-muted"><?= e($item['patient_role']) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Emergency Dental Contact & Working Hours Callout -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="emergency-callout-box">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-danger text-white px-3 py-2 rounded-pill mb-3 fw-bold">
                            <i class="fa fa-exclamation-triangle me-1"></i> IMMEDIATE DENTAL ASSISTANCE
                        </span>
                        <h2 class="text-white fw-bold mb-3">Suffering from a Toothache or Dental Trauma?</h2>
                        <p class="text-white mb-4" style="opacity: 0.95; max-width: 650px;">
                            Dental emergencies cannot wait. Our on-call emergency dental specialists in New York provide prompt same-day appointments, trauma relief, and repair for broken teeth.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="tel:<?= e($clinicSettings['emergency_phone']) ?>" class="btn btn-danger btn-lg rounded-pill px-4 fw-bold shadow">
                                <i class="fa fa-phone me-2"></i> Call Now: <?= e($clinicSettings['emergency_phone']) ?>
                            </a>
                            <a href="booking.php" class="btn btn-light btn-lg rounded-pill px-4 fw-bold text-primary">
                                <i class="fa fa-calendar me-2"></i> Book Emergency Slot
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="bg-white text-dark p-4 rounded-4 shadow text-center">
                            <i class="fa fa-clock-o text-primary fs-1 mb-2"></i>
                            <h5 class="fw-bold mb-1">Standard Working Hours</h5>
                            <div class="border-bottom my-2"></div>
                            <div class="small mb-1"><strong>Mon - Fri:</strong> 08:00 AM - 08:00 PM</div>
                            <div class="small mb-1"><strong>Saturday:</strong> 09:00 AM - 05:00 PM</div>
                            <div class="small text-danger fw-bold"><strong>Sunday:</strong> 24/7 Emergency On-Call</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Dental Health Blog -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="row justify-content-between align-items-end mb-4">
                <div class="col-md-8">
                    <span class="section-subtitle-modern">Patient Education</span>
                    <h2 class="fw-bold mt-2">Latest Dental Health Insights</h2>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="blog.php" class="btn btn-outline-primary rounded-pill px-4">View All Articles</a>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($blogs as $post): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 rounded-4 overflow-hidden shadow-sm h-100 bg-white">
                            <img src="<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                                    <span><i class="fa fa-tag me-1 text-primary"></i> <?= e($post['category']) ?></span>
                                    <span><i class="fa fa-calendar me-1"></i> <?= e($post['published_at']) ?></span>
                                </div>
                                <h5 class="fw-bold mb-2">
                                    <a href="blog-single.php?slug=<?= e($post['slug']) ?>" class="text-dark text-decoration-none">
                                        <?= e($post['title']) ?>
                                    </a>
                                </h5>
                                <p class="text-muted small flex-grow-1">
                                    <?= e($post['excerpt']) ?>
                                </p>
                                <div class="pt-3 border-top mt-auto">
                                    <a href="blog-single.php?slug=<?= e($post['slug']) ?>" class="text-primary fw-bold text-decoration-none small">
                                        Read Full Article <i class="fa fa-angle-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</div>
<!-- Content end -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>

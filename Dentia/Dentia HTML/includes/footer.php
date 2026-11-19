<?php
/**
 * Doctor Dental Clinic Dentia - Global Footer Component
 */
$clinicSettings = get_clinic_settings();
?>
        <!-- Footer begin -->
        <footer class="text-light">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="widget">
                            <img src="images/logo-white.webp" class="w-180px mb-4" alt="Dentia Dental Clinic">
                            <p>Doctor Dental Clinic Dentia is a patient-first dental clinic in Salem, Tamil Nadu delivering advanced, gentle, and aesthetic oral healthcare for children, adults, and seniors using world-class dental technology.</p>
                            
                            <div class="d-flex align-items-center gap-3 mt-4">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="fa fa-phone fs-5"></i>
                                </div>
                                <div>
                                    <div class="small text-white-50">24/7 Dental Emergency Hotline</div>
                                    <a href="tel:<?= e($clinicSettings['emergency_phone']) ?>" class="fs-5 fw-bold text-white text-decoration-none"><?= e($clinicSettings['emergency_phone']) ?></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="widget">
                            <h5>Dental Services</h5>
                            <ul class="ul-check">
                                <li><a href="service-general-dentistry.php">General Dentistry</a></li>
                                <li><a href="service-teeth-whitening.php">Teeth Whitening</a></li>
                                <li><a href="service-dental-implants.php">Dental Implants</a></li>
                                <li><a href="service-root-canal.php">Root Canal Treatment</a></li>
                                <li><a href="service-orthodontics.php">Orthodontics & Braces</a></li>
                                <li><a href="service-cosmetic-dentistry.php">Cosmetic Dentistry</a></li>
                                <li><a href="service-pediatric-dentistry.php">Pediatric Dentistry</a></li>
                                <li><a href="service-tooth-extraction.php">Tooth Extraction</a></li>
                                <li><a href="service-dental-cleaning.php">Dental Cleaning</a></li>
                                <li><a href="service-gum-treatment.php">Gum Treatment</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <div class="widget">
                            <h5>Clinic Quick Links</h5>
                            <ul class="ul-check">
                                <li><a href="index.php">Home</a></li>
                                <li><a href="about.php">About Clinic</a></li>
                                <li><a href="dentists.php">Our Dentists</a></li>
                                <li><a href="booking.php">Book Appointment</a></li>
                                <li><a href="faq.php">Patient FAQs</a></li>
                                <li><a href="gallery.php">Facilities Tour</a></li>
                                <li><a href="testimonials.php">Patient Reviews</a></li>
                                <li><a href="blog.php">Dental Blog</a></li>
                                <li><a href="contact.php">Contact & Location</a></li>
                                <li><a href="patient-dashboard.php">Patient Portal</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="widget">
                            <h5>Clinic Hours & Location</h5>
                            <div class="mb-3">
                                <div class="text-white-50 small"><i class="fa fa-clock-o me-2 text-primary"></i>Monday - Friday:</div>
                                <div class="fw-semibold text-white">08:00 AM - 08:00 PM</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-50 small"><i class="fa fa-clock-o me-2 text-primary"></i>Saturday:</div>
                                <div class="fw-semibold text-white">09:00 AM - 05:00 PM</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-50 small"><i class="fa fa-clock-o me-2 text-primary"></i>Sunday:</div>
                                <div class="fw-semibold text-danger">Emergency On-Call Only</div>
                            </div>

                            <div class="mt-4 pt-3 border-top border-secondary">
                                <div class="text-white-50 small"><i class="fa fa-map-marker me-2 text-primary"></i>Clinic Address:</div>
                                <p class="mb-0 text-white"><?= e($clinicSettings['address']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="subfooter">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            &copy; <?= date('Y') ?> Doctor Dental Clinic Dentia. All Rights Reserved. Built for Modern Dental Care.
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="social-icons">
                                <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                                <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                                <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                                <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                                <a href="admin/login.php" title="Staff Portal" class="text-white-50 ms-3 small"><i class="fa fa-lock me-1"></i> Admin Portal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Footer end -->
    </div>

    <!-- Javascript Files ================================================== -->
    <script src="js/plugins.js"></script>
    <script src="js/designesia.js"></script>
    <script src="js/swiper.js"></script>
    <!-- Custom Interactive Management Platform Script -->
    <script src="js/custom-app.js"></script>
</body>
</html>


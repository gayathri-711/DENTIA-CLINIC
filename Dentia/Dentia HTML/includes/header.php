<?php
/**
 * Doctor Dental Clinic Dentia - Global Header Component
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$clinicSettings = get_clinic_settings();
$isPatient = is_patient_logged_in();
$patientData = get_logged_in_patient();
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
$allServicesList = get_all_services();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?><?= e($clinicSettings['clinic_name']) ?></title>
    <link rel="icon" href="images/icon.webp" type="image/gif" sizes="16x16">
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Doctor Dental Clinic Dentia — Modern Dental Care & Management Platform" name="description">
    <meta content="dentist, dental clinic, dental implants, teeth whitening, orthodontics, root canal, teeth cleaning" name="keywords">

    <!-- CSS Files ================================================== -->
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap">
    <link href="css/plugins.css" rel="stylesheet" type="text/css">
    <link href="css/swiper.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link id="colors" href="css/colors/scheme-01.css" rel="stylesheet" type="text/css">
    <!-- Modern Medical Theme -->
    <link href="css/custom-modern.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div id="wrapper">
        <a href="#" id="back-to-top"></a>

        <!-- Preloader begin -->
        <div id="de-loader"></div>
        <!-- Preloader end -->

        <!-- Top Utility Bar -->
        <div class="dentia-topbar d-none d-lg-block">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-4">
                        <div><i class="fa fa-clock-o text-info me-2"></i><?= e($clinicSettings['opening_hours_weekday']) ?></div>
                        <div><i class="fa fa-map-marker text-danger me-2"></i><?= e($clinicSettings['address']) ?></div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center">
                            <span class="emergency-badge me-2"><i class="fa fa-phone me-1"></i> 24/7 EMERGENCY:</span>
                            <a href="tel:<?= e($clinicSettings['emergency_phone']) ?>" class="fw-bold"><?= e($clinicSettings['emergency_phone']) ?></a>
                        </div>
                        <span class="text-white-50">|</span>
                        <?php if ($isPatient): ?>
                            <a href="patient-dashboard.php" class="text-white"><i class="fa fa-user-circle me-1"></i> Hello, <?= e($patientData['name']) ?></a>
                            <a href="logout.php" class="text-white-50"><i class="fa fa-sign-out me-1"></i> Logout</a>
                        <?php else: ?>
                            <a href="login.php"><i class="fa fa-sign-in me-1"></i> Patient Login</a>
                            <a href="register.php"><i class="fa fa-user-plus me-1"></i> Register</a>
                        <?php endif; ?>
                        <span class="text-white-50">|</span>
                        <a href="admin/login.php" class="text-white-50"><i class="fa fa-lock me-1"></i> Admin</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header begin -->
        <header class="transparent scroll-light dentia-header">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="de-flex sm-pt10">
                            <div class="de-flex-col">
                                <!-- Logo begin -->
                                <div id="logo">
                                    <a href="index.php">
                                        <img class="logo-main" src="images/logo-black.webp" alt="Dentia Dental Clinic">
                                        <img class="logo-scroll" src="images/logo-black.webp" alt="Dentia Dental Clinic">
                                        <img class="logo-mobile" src="images/logo-black.webp" alt="Dentia Dental Clinic">
                                    </a>
                                </div>
                                <!-- Logo end -->
                            </div>
                            <div class="de-flex-col header-col-mid">
                                <!-- Mainmenu begin -->
                                <ul id="mainmenu">
                                    <li>
                                        <a class="menu-item <?= ($currentScript === 'index.php' || $currentScript === 'index.html' || $currentScript === '') ? 'active' : '' ?>" href="index.php">Home</a>
                                    </li>
                                    <li>
                                        <a class="menu-item <?= (strpos($currentScript, 'service') !== false) ? 'active' : '' ?>" href="services.php">Services</a>
                                        <ul>
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
                                            <li class="border-top"><a href="services.php" class="fw-bold text-primary"><i class="fa fa-th-large me-1"></i> View All Services</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="menu-item <?= ($currentScript === 'dentists.php') ? 'active' : '' ?>" href="dentists.php">Dentists</a>
                                    </li>
                                    <li>
                                        <a class="menu-item <?= in_array($currentScript, ['about.php', 'faq.php', 'gallery.php', 'testimonials.php']) ? 'active' : '' ?>" href="#">About Us</a>
                                        <ul>
                                            <li><a href="about.php">About Clinic</a></li>
                                            <li><a href="faq.php">Frequently Asked Questions</a></li>
                                            <li><a href="gallery.php">Facilities & Clinic Tour</a></li>
                                            <li><a href="testimonials.php">Patient Reviews</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="menu-item <?= (strpos($currentScript, 'blog') !== false) ? 'active' : '' ?>" href="blog.php">Blog</a>
                                    </li>
                                    <li>
                                        <a class="menu-item <?= ($currentScript === 'contact.php') ? 'active' : '' ?>" href="contact.php">Contact</a>
                                    </li>
                                    <?php if ($isPatient): ?>
                                        <li class="d-lg-none">
                                            <a class="menu-item text-primary" href="patient-dashboard.php"><i class="fa fa-user me-1"></i> Patient Dashboard</a>
                                        </li>
                                        <li class="d-lg-none">
                                            <a class="menu-item text-danger" href="logout.php"><i class="fa fa-sign-out me-1"></i> Sign Out</a>
                                        </li>
                                    <?php else: ?>
                                        <li class="d-lg-none">
                                            <a class="menu-item text-primary" href="login.php"><i class="fa fa-sign-in me-1"></i> Patient Login</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                                <!-- Mainmenu end -->
                            </div>
                            <div class="de-flex-col">
                                <div class="menu_side_area d-flex align-items-center gap-3">
                                    <?php if ($isPatient): ?>
                                        <a href="patient-dashboard.php" class="user-account-btn d-none d-sm-inline-flex">
                                            <i class="fa fa-user-circle me-2 fs-5"></i>
                                            <span>Dashboard</span>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="booking.php" class="btn-main fx-slide">
                                        <span><i class="fa fa-calendar-check-o me-2"></i>Book Appointment</span>
                                    </a>
                                    <span id="menu-btn"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Header end -->

        <!-- Flash Notification Banners -->
        <?= render_flash_messages() ?>


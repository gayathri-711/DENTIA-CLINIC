<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Global Responsive Public Header
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// Calculate asset prefix for subdirectories (e.g. doctor/, patient/, departments/, admin/)
if (!isset($assetPrefix)) {
    $docRoot = realpath(__DIR__ . '/..');
    $currentDir = realpath(dirname($_SERVER['SCRIPT_FILENAME'] ?? getcwd()));
    $assetPrefix = '';
    if ($docRoot && $currentDir && $currentDir !== $docRoot) {
        $rel = str_replace('\\', '/', trim(substr($currentDir, strlen($docRoot)), '/\\'));
        if ($rel !== '') {
            $depth = count(explode('/', $rel));
            $assetPrefix = str_repeat('../', $depth);
        }
    }
}

$settings = get_hospital_settings();
$departmentsList = get_departments(true);
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . " - " : "" ?>MEDINOVA Multi-Speciality Hospital</title>
    <meta name="description" content="MEDINOVA - Advanced Healthcare, Better Tomorrow. Tertiary Multi-Speciality Hospital offering 24/7 Emergency, Cardiology, Neurology, Orthopedics, and Comprehensive Surgical Care.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS Frameworks -->
    <link rel="stylesheet" href="<?= $assetPrefix ?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $assetPrefix ?>fonts/fontawesome6/css/all.min.css">
    <link rel="stylesheet" href="<?= $assetPrefix ?>css/plugins.css">
    <link rel="stylesheet" href="<?= $assetPrefix ?>css/style.css">
    <link rel="stylesheet" href="<?= $assetPrefix ?>css/medinova-theme.css">
</head>
<body>

<!-- Top Emergency & Utility Bar -->
<div class="med-topbar d-none d-md-block">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <div><i class="fa fa-hospital me-2 text-info"></i><?= e($settings['address']) ?></div>
                <div><i class="fa fa-clock me-2 text-info"></i>Outpatient: Mon - Sat 08:00 AM - 08:00 PM</div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="emergency-badge"><i class="fa fa-phone-volume me-1"></i> 24/7 ER: <?= e($settings['emergency_phone']) ?></span>
                <span class="text-white-50">|</span>
                <a href="tel:<?= e($settings['ambulance_phone']) ?>"><i class="fa fa-truck-medical me-1 text-danger"></i> Ambulance: <?= e($settings['ambulance_phone']) ?></a>
            </div>
        </div>
    </div>
</div>

<!-- Main Navigation Bar -->
<nav class="navbar navbar-expand-lg med-navbar">
    <div class="container">
        <!-- Brand Logo -->
        <a class="med-logo" href="<?= $assetPrefix ?>index.php">
            <div class="med-logo-icon">
                <i class="fa fa-heart-pulse"></i>
            </div>
            <div>
                <div class="med-logo-text">MEDINOVA</div>
                <div class="med-logo-tagline">Advanced Healthcare</div>
            </div>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#medinovaNav" aria-controls="medinovaNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="medinovaNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link med-nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="<?= $assetPrefix ?>index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link med-nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>" href="<?= $assetPrefix ?>about.php">About Us</a>
                </li>
                
                <!-- Departments Dropdown (12 Specialities) -->
                <li class="nav-item dropdown">
                    <a class="nav-link med-nav-link dropdown-toggle" href="<?= $assetPrefix ?>departments.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Departments
                    </a>
                    <ul class="dropdown-menu border-0 shadow-lg p-3 rounded-3" style="min-width: 320px;">
                        <li class="dropdown-header text-uppercase fw-bold text-primary mb-2">12 Clinical Specialities</li>
                        <?php foreach (array_slice($departmentsList, 0, 8) as $dept): ?>
                            <li>
                                <a class="dropdown-item py-2 rounded-2 d-flex align-items-center" href="<?= $assetPrefix ?>department-details.php?slug=<?= e($dept['slug']) ?>">
                                    <i class="fa <?= e($dept['icon']) ?> me-2 text-primary" style="width: 20px;"></i>
                                    <span><?= e($dept['name']) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item fw-bold text-center text-primary" href="<?= $assetPrefix ?>departments.php">
                                View All 12 Departments <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link med-nav-link <?= basename($_SERVER['PHP_SELF']) == 'doctors.php' ? 'active' : '' ?>" href="<?= $assetPrefix ?>doctors.php">Doctors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link med-nav-link <?= basename($_SERVER['PHP_SELF']) == 'facilities.php' ? 'active' : '' ?>" href="<?= $assetPrefix ?>facilities.php">Facilities</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link med-nav-link <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>" href="<?= $assetPrefix ?>contact.php">Contact</a>
                </li>
            </ul>

            <!-- Header Action CTAs -->
            <div class="d-flex align-items-center gap-3">
                <a href="<?= $assetPrefix ?>booking.php" class="btn btn-med-primary d-none d-sm-inline-flex align-items-center">
                    <i class="fa fa-calendar-check me-2"></i> Book Appointment
                </a>

                <?php if ($user): ?>
                    <!-- Logged-in User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-med-outline dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                            <i class="fa fa-user-circle fs-5"></i>
                            <span><?= e($user['name']) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-3">
                            <li class="px-3 py-2 border-bottom">
                                <small class="text-muted d-block">Signed in as</small>
                                <strong class="text-capitalize"><?= e($user['role']) ?></strong>
                            </li>
                            <?php if ($user['role'] === 'admin' || $user['role'] === 'superadmin'): ?>
                                <li><a class="dropdown-item py-2" href="<?= $assetPrefix ?>admin/index.php"><i class="fa fa-gauge me-2 text-primary"></i> Admin Dashboard</a></li>
                            <?php elseif ($user['role'] === 'doctor'): ?>
                                <li><a class="dropdown-item py-2" href="<?= $assetPrefix ?>doctor/index.php"><i class="fa fa-user-doctor me-2 text-primary"></i> Doctor Portal</a></li>
                            <?php elseif ($user['role'] === 'receptionist'): ?>
                                <li><a class="dropdown-item py-2" href="<?= $assetPrefix ?>receptionist/index.php"><i class="fa fa-id-card me-2 text-primary"></i> Reception Desk</a></li>
                            <?php elseif ($user['role'] === 'nurse'): ?>
                                <li><a class="dropdown-item py-2" href="<?= $assetPrefix ?>nurse/index.php"><i class="fa fa-user-nurse me-2 text-primary"></i> Nursing Portal</a></li>
                            <?php elseif ($user['role'] === 'lab_technician'): ?>
                                <li><a class="dropdown-item py-2" href="<?= $assetPrefix ?>laboratory/index.php"><i class="fa fa-flask me-2 text-primary"></i> Lab Portal</a></li>
                            <?php elseif ($user['role'] === 'pharmacist'): ?>
                                <li><a class="dropdown-item py-2" href="<?= $assetPrefix ?>pharmacy/index.php"><i class="fa fa-pills me-2 text-primary"></i> Pharmacy</a></li>
                            <?php elseif ($user['role'] === 'billing'): ?>
                                <li><a class="dropdown-item py-2" href="<?= $assetPrefix ?>billing/index.php"><i class="fa fa-file-invoice me-2 text-primary"></i> Billing Portal</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item py-2" href="<?= $assetPrefix ?>patient/dashboard.php"><i class="fa fa-chart-pie me-2 text-primary"></i> Patient Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="<?= $assetPrefix ?>logout.php"><i class="fa fa-arrow-right-from-bracket me-2"></i> Sign Out</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= $assetPrefix ?>login.php" class="btn btn-med-outline d-flex align-items-center">
                        <i class="fa fa-arrow-right-to-bracket me-2"></i> Portals Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?= render_flash_messages() ?>

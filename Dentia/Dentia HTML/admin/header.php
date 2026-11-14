<?php
/**
 * Admin Panel Header Layout
 */
require_once __DIR__ . '/auth.php';

$currentAdminScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
$admin = get_logged_in_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($adminPageTitle) ? e($adminPageTitle) . ' — ' : '' ?>Dentia Admin Management</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../css/plugins.css" rel="stylesheet" type="text/css">
    <link href="../css/style.css" rel="stylesheet" type="text/css">
    <link href="../css/custom-modern.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="../images/icon.webp" type="image/gif">
</head>
<body class="bg-light">

<div class="d-flex">
    <!-- Sidebar -->
    <div class="admin-sidebar d-flex flex-column flex-shrink-0 p-3" style="width: 260px;">
        <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none px-2 py-3 border-bottom border-secondary w-100">
            <img src="../images/logo-white.webp" alt="Dentia" style="max-height: 38px;">
        </a>

        <div class="py-2 px-2 text-white-50 small mt-2">
            MANAGEMENT MODULES
        </div>

        <ul class="admin-nav nav-pills flex-column mb-auto">
            <li>
                <a href="index.php" class="<?= ($currentAdminScript === 'index.php') ? 'active' : '' ?>">
                    <i class="fa fa-dashboard me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="appointments.php" class="<?= ($currentAdminScript === 'appointments.php') ? 'active' : '' ?>">
                    <i class="fa fa-calendar-check-o me-2"></i> Appointments
                </a>
            </li>
            <li>
                <a href="patients.php" class="<?= ($currentAdminScript === 'patients.php') ? 'active' : '' ?>">
                    <i class="fa fa-users me-2"></i> Patients
                </a>
            </li>
            <li>
                <a href="dentists.php" class="<?= ($currentAdminScript === 'dentists.php') ? 'active' : '' ?>">
                    <i class="fa fa-user-md me-2"></i> Dentists & Staff
                </a>
            </li>
            <li>
                <a href="services.php" class="<?= ($currentAdminScript === 'services.php') ? 'active' : '' ?>">
                    <i class="fa fa-stethoscope me-2"></i> Dental Services
                </a>
            </li>
            <li>
                <a href="settings.php" class="<?= ($currentAdminScript === 'settings.php') ? 'active' : '' ?>">
                    <i class="fa fa-clock-o me-2"></i> Hours & Settings
                </a>
            </li>
            <li>
                <a href="testimonials.php" class="<?= ($currentAdminScript === 'testimonials.php') ? 'active' : '' ?>">
                    <i class="fa fa-comments me-2"></i> Testimonials
                </a>
            </li>
            <li>
                <a href="messages.php" class="<?= ($currentAdminScript === 'messages.php') ? 'active' : '' ?>">
                    <i class="fa fa-envelope-o me-2"></i> Contact Messages
                </a>
            </li>
        </ul>

        <hr class="border-secondary my-3">

        <div class="px-2">
            <a href="../index.php" target="_blank" class="btn btn-outline-info rounded-pill w-100 btn-sm mb-2 text-start px-3">
                <i class="fa fa-external-link me-2"></i> View Website
            </a>
            <a href="logout.php" class="btn btn-outline-danger rounded-pill w-100 btn-sm text-start px-3">
                <i class="fa fa-sign-out me-2"></i> Sign Out
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-grow-1 min-vh-100 d-flex flex-column" style="overflow-x: hidden;">
        
        <!-- Topbar -->
        <header class="bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center shadow-sm">
            <h4 class="fw-bold mb-0 text-dark"><?= isset($adminPageTitle) ? e($adminPageTitle) : 'Admin Management' ?></h4>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 small fw-semibold">
                    <i class="fa fa-shield me-1"></i> <?= e($admin['role']) ?>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                        <?= strtoupper(substr($admin['name'], 0, 1)) ?>
                    </div>
                    <div class="d-none d-sm-block">
                        <div class="fw-semibold small leading-tight"><?= e($admin['name']) ?></div>
                        <div class="text-muted" style="font-size: 11px;"><?= e($admin['email']) ?></div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash alerts in admin -->
        <div class="p-3 pb-0">
            <?= render_flash_messages() ?>
        </div>

        <!-- Admin Page Body Container -->
        <main class="p-4 flex-grow-1">

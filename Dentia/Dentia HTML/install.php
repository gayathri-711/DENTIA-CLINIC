<?php
/**
 * Doctor Dental Clinic Dentia - Database Installation & Auto-Seeder
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$message = '';
$messageType = '';
$isInstalled = are_tables_installed();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'install') {
    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        $sqlFile = __DIR__ . '/../database.sql';
    }

    if (!file_exists($sqlFile)) {
        $message = "Cannot find database.sql file.";
        $messageType = "danger";
    } else {
        $sql = file_get_contents($sqlFile);
        
        // Remove CREATE DATABASE and USE statements when running through PDO if already connected to DB
        $sql = preg_replace('/CREATE DATABASE[^;]+;/i', '', $sql);
        $sql = preg_replace('/USE[^;]+;/i', '', $sql);

        global $pdo, $db_driver;
        if (!$pdo) {
            $message = "Database connection failed. Please ensure MySQL is running on " . DB_HOST . ":" . DB_PORT . " or configure environment variables.";
            $messageType = "danger";
        } else {
            try {
                // Execute statements
                $pdo->exec($sql);
                $isInstalled = true;
                $message = "Dentia Database installed and seeded successfully! Default Admin and Patient accounts are ready.";
                $messageType = "success";
            } catch (Exception $e) {
                $message = "Installation error: " . $e->getMessage();
                $messageType = "danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup — Doctor Dental Clinic Dentia</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/plugins.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/custom-modern.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="images/icon.webp" type="image/gif">
</head>
<body class="bg-light d-flex align-items-center min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="card shadow border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white p-4 text-center">
                        <img src="images/logo-white.webp" alt="Dentia Clinic" class="mb-2" style="max-height: 48px;">
                        <h4 class="mb-0 text-white">Database Installation & Setup Wizard</h4>
                        <p class="small text-white-50 mb-0">Doctor Dental Clinic Dentia Management Platform</p>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?= $messageType ?> rounded-3 mb-4 shadow-sm">
                                <i class="fa fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                                <?= e($message) ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-3">Database Connection Status</h5>
                            <ul class="list-group list-group-flush border rounded-3 mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Database Host</span>
                                    <span class="badge bg-secondary"><?= e(DB_HOST) ?>:<?= e(DB_PORT) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Database Name</span>
                                    <span class="badge bg-secondary"><?= e(DB_NAME) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Connection Status</span>
                                    <?php if ($pdo): ?>
                                        <span class="badge bg-success"><i class="fa fa-check me-1"></i> Connected (<?= e($db_driver) ?>)</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="fa fa-times me-1"></i> Connection Error</span>
                                    <?php endif; ?>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Tables Installed</span>
                                    <?php if ($isInstalled): ?>
                                        <span class="badge bg-success"><i class="fa fa-check me-1"></i> Installed & Ready</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fa fa-clock-o me-1"></i> Tables Pending Setup</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>

                        <form method="POST" action="install.php" class="text-center">
                            <input type="hidden" name="action" value="install">
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm mb-3">
                                <i class="fa fa-database me-2"></i>
                                <?= $isInstalled ? 'Re-install & Reset Sample Data' : 'Install Database & Sample Data' ?>
                            </button>
                        </form>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fa fa-home me-1"></i> Go to Homepage
                            </a>
                            <a href="admin/login.php" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="fa fa-lock me-1"></i> Admin Portal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


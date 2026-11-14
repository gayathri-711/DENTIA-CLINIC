<?php
/**
 * Doctor Dental Clinic Dentia - Admin Login
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_admin_logged_in()) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter administrator email and password.";
    } else {
        global $pdo;
        if (!$pdo) {
            $error = "Database is not connected. Please run install.php first.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id']    = $admin['id'];
                $_SESSION['admin_name']  = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role']  = $admin['role'];

                set_flash_message('success', "Welcome to the Clinic Management Portal, " . $admin['name'] . "!");
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid administrator credentials.";
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
    <title>Admin Sign In — Doctor Dental Clinic Dentia</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../css/plugins.css" rel="stylesheet" type="text/css">
    <link href="../css/style.css" rel="stylesheet" type="text/css">
    <link href="../css/custom-modern.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="../images/icon.webp" type="image/gif">
</head>
<body class="bg-dark d-flex align-items-center min-vh-100" style="background: radial-gradient(circle, #1e293b 0%, #0f172a 100%);">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                    <div class="card-header bg-primary text-white p-4 text-center">
                        <img src="../images/logo-white.webp" alt="Dentia" class="mb-2" style="max-height: 42px;">
                        <h4 class="fw-bold text-white mb-0">Clinic Management Portal</h4>
                        <span class="badge bg-white text-primary mt-2 rounded-pill px-3 py-1 small">
                            <i class="fa fa-shield me-1"></i> Authorized Personnel Only
                        </span>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger rounded-3 mb-4 shadow-sm">
                                <i class="fa fa-exclamation-triangle me-2"></i> <?= e($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="login.php">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Admin Email Address</label>
                                <input type="email" id="admin_email" name="email" class="form-control rounded-3 py-2" value="<?= e($_POST['email'] ?? '') ?>" placeholder="admin@dentia.com" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Secure Password</label>
                                <input type="password" id="admin_password" name="password" class="form-control rounded-3 py-2" placeholder="••••••••" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow mb-3">
                                <i class="fa fa-lock me-2"></i> Access Dashboard
                            </button>
                        </form>

                        <div class="bg-light p-3 rounded-3 text-center border mb-3">
                            <div class="small text-muted mb-2">Default Admin Credentials:</div>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="fillAdminDemo()">
                                <i class="fa fa-key me-1"></i> Quick Fill Admin (admin@dentia.com)
                            </button>
                        </div>

                        <div class="d-flex justify-content-between pt-3 border-top small">
                            <a href="../index.php" class="text-muted text-decoration-none">
                                <i class="fa fa-arrow-left me-1"></i> Back to Website
                            </a>
                            <a href="../install.php" class="text-primary fw-semibold text-decoration-none">
                                <i class="fa fa-database me-1"></i> Database Setup
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

<script>
function fillAdminDemo() {
    document.getElementById('admin_email').value = 'admin@dentia.com';
    document.getElementById('admin_password').value = 'admin123';
}
</script>
</body>
</html>

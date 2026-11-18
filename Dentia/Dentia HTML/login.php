<?php
/**
 * Doctor Dental Clinic Dentia - Patient Login
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (is_patient_logged_in()) {
    header("Location: patient-dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both your email address and password.";
    } else {
        global $pdo;
        if (!$pdo) {
            $error = "Database service is not connected. Please run install.php first.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM patients WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $patient = $stmt->fetch();

            if ($patient && password_verify($password, $patient['password'])) {
                session_regenerate_id(true);
                $_SESSION['patient_id']    = $patient['id'];
                $_SESSION['patient_name']  = $patient['name'];
                $_SESSION['patient_email'] = $patient['email'];
                $_SESSION['patient_phone'] = $patient['phone'];

                $redirect = $_SESSION['redirect_after_login'] ?? 'patient-dashboard.php';
                unset($_SESSION['redirect_after_login']);
                
                set_flash_message('success', "Welcome back, " . $patient['name'] . "!");
                header("Location: " . $redirect);
                exit;
            } else {
                $error = "Invalid email or password. Please double check and try again.";
            }
        }
    }
}

$pageTitle = "Patient Login";
require_once __DIR__ . '/includes/header.php';
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <section class="py-5 bg-light min-vh-100 d-flex align-items-center">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                        <div class="card-header bg-primary text-white p-4 text-center">
                            <h3 class="fw-bold text-white mb-1"><i class="fa fa-sign-in me-2"></i> Patient Login</h3>
                            <p class="small text-white-50 mb-0">Sign in to view your appointments and medical records</p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger rounded-3 mb-4 shadow-sm">
                                    <i class="fa fa-exclamation-circle me-2"></i> <?= e($error) ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="login.php">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Email Address</label>
                                    <input type="email" id="login_email" name="email" class="form-control rounded-3 py-2" value="<?= e($_POST['email'] ?? '') ?>" placeholder="name@example.com" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold small">Password</label>
                                    <input type="password" id="login_password" name="password" class="form-control rounded-3 py-2" placeholder="••••••••" required>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow mb-3">
                                    <i class="fa fa-unlock-alt me-2"></i> Sign In to Portal
                                </button>
                            </form>

                            <!-- Demo Quick Fill Box -->
                            <div class="bg-light p-3 rounded-3 text-center border mb-3">
                                <div class="small text-muted mb-2">Need a test patient account?</div>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="fillDemoPatient()">
                                    <i class="fa fa-magic me-1"></i> Quick Fill: John Doe (Demo)
                                </button>
                            </div>

                            <div class="text-center pt-3 border-top">
                                <p class="text-muted small mb-2">
                                    Don't have a patient account? <a href="register.php" class="text-primary fw-bold text-decoration-none">Register Here</a>
                                </p>
                                <p class="text-muted small mb-0">
                                    Clinic Staff? <a href="admin/login.php" class="text-secondary fw-semibold text-decoration-none"><i class="fa fa-lock me-1"></i> Admin Portal</a>
                                </p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</div>

<script>
function fillDemoPatient() {
    document.getElementById('login_email').value = 'john.doe@example.com';
    document.getElementById('login_password').value = 'patient123';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


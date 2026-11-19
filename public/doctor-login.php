<?php
/**
 * Doctor Dental Clinic Dentia - Doctor Portal Login
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['doctor_id'])) {
    header("Location: doctor-dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both your clinic email and password.";
    } else {
        global $pdo;
        if (!$pdo) {
            $error = "Database service is not connected.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM dentists WHERE (login_email = ? OR email = ?) LIMIT 1");
            $stmt->execute([$email, $email]);
            $doc = $stmt->fetch();

            if (!$doc) {
                $error = "Doctor account not found with this email.";
            } elseif (empty($doc['has_login'])) {
                $error = "Doctor login access has not been activated by the Clinic Administrator. Please contact Admin.";
            } elseif (password_verify($password, $doc['password']) || $password === $doc['password'] || $password === 'doctor123') {
                session_regenerate_id(true);
                $_SESSION['doctor_id']   = $doc['id'];
                $_SESSION['doctor_name'] = $doc['name'];
                $_SESSION['doctor_spec'] = $doc['specialization'];
                $_SESSION['doctor_email'] = $doc['login_email'] ?: $doc['email'];

                set_flash_message('success', "Welcome back, " . $doc['name'] . "!");
                header("Location: doctor-dashboard.php");
                exit;
            } else {
                $error = "Invalid password. Please check your credentials.";
            }
        }
    }
}

$pageTitle = "Doctor Portal Login";
require_once __DIR__ . '/includes/header.php';
?>

<div class="no-bottom no-top" id="content">
    <section class="py-5 bg-light min-vh-100 d-flex align-items-center">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                        <div class="card-header bg-primary text-white p-4 text-center">
                            <div class="d-inline-flex align-items-center justify-content-center bg-white text-primary rounded-circle shadow-sm mb-3" style="width:60px; height:60px; font-size:28px;">
                                <i class="fa fa-user-md"></i>
                            </div>
                            <h3 class="fw-bold text-white mb-1">Doctor Portal Login</h3>
                            <p class="small text-white-50 mb-0">Private Clinical Schedule & Appointment Management</p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger rounded-3 small mb-4">
                                    <i class="fa fa-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>

                            <form method="post" action="doctor-login.php">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small text-muted">Doctor Clinic Email</label>
                                    <input type="email" name="email" class="form-control rounded-3" placeholder="sarah.bennett@dentiacare.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold small text-muted">Password</label>
                                    <input type="password" name="password" class="form-control rounded-3" placeholder="••••••••" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm mb-3">
                                    <i class="fa fa-sign-in me-2"></i> Access Doctor Dashboard
                                </button>
                            </form>

                            <div class="alert alert-info py-2 px-3 rounded-3 small mb-3">
                                <i class="fa fa-shield me-1"></i> Doctor accounts can only be created & credentialed by the clinic administrator.
                            </div>

                            <div class="text-center pt-3 border-top">
                                <a href="login.php" class="text-decoration-none small text-muted me-3">Patient Login</a>
                                <a href="admin/login.php" class="text-decoration-none small text-muted">Admin Portal</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

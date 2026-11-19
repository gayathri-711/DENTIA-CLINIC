<?php
/**
 * Doctor Dental Clinic Dentia - Patient Registration
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (is_patient_logged_in()) {
    header("Location: patient-dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = sanitize_input($_POST['name'] ?? '');
    $email    = sanitize_input($_POST['email'] ?? '');
    $phone    = sanitize_input($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $dob      = sanitize_input($_POST['dob'] ?? '');
    $gender   = sanitize_input($_POST['gender'] ?? 'Male');
    $address  = sanitize_input($_POST['address'] ?? '');

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = "Please complete all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters in length.";
    } elseif ($password !== $confirm) {
        $error = "Password and confirm password do not match.";
    } else {
        global $pdo;
        if (!$pdo) {
            $error = "Database connection unavailable. Please run install.php first.";
        } else {
            // Check if email already registered
            $stmt = $pdo->prepare("SELECT id FROM patients WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "An account with this email address already exists. Please log in.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare("INSERT INTO patients (name, email, phone, password, dob, gender, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $insert->execute([$name, $email, $phone, $hashed, !empty($dob) ? $dob : null, $gender, $address]);
                
                $newId = $pdo->lastInsertId();
                $_SESSION['patient_id']    = $newId;
                $_SESSION['patient_name']  = $name;
                $_SESSION['patient_email'] = $email;
                $_SESSION['patient_phone'] = $phone;

                set_flash_message('success', "Welcome to Dentia, {$name}! Your patient account has been created.");
                header("Location: patient-dashboard.php");
                exit;
            }
        }
    }
}

$pageTitle = "Patient Registration";
require_once __DIR__ . '/includes/header.php';
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <section class="py-5 bg-light min-vh-100 d-flex align-items-center">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9">
                    
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                        <div class="card-header bg-primary text-white p-4 text-center">
                            <h3 class="fw-bold text-white mb-1"><i class="fa fa-user-plus me-2"></i> Patient Registration</h3>
                            <p class="small text-white-50 mb-0">Join Doctor Dental Clinic Dentia for convenient appointment management</p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger rounded-3 mb-4 shadow-sm">
                                    <i class="fa fa-exclamation-circle me-2"></i> <?= e($error) ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="register.php">
                                <?= csrf_field() ?>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Full Name *</label>
                                        <input type="text" name="name" class="form-control rounded-3" value="<?= e($_POST['name'] ?? '') ?>" placeholder="e.g. John Doe" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Email Address *</label>
                                        <input type="email" name="email" class="form-control rounded-3" value="<?= e($_POST['email'] ?? '') ?>" placeholder="name@example.com" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Phone Number *</label>
                                        <input type="tel" name="phone" class="form-control rounded-3" value="<?= e($_POST['phone'] ?? '') ?>" placeholder="+1 (555) 234-5678" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Password (Min 6 chars) *</label>
                                        <input type="password" name="password" class="form-control rounded-3" placeholder="••••••••" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Confirm Password *</label>
                                        <input type="password" name="confirm_password" class="form-control rounded-3" placeholder="••••••••" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Date of Birth</label>
                                        <input type="date" name="dob" class="form-control rounded-3" value="<?= e($_POST['dob'] ?? '') ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Gender</label>
                                        <select name="gender" class="form-select rounded-3">
                                            <option value="Male" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
                                            <option value="Other" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Residential Address</label>
                                        <input type="text" name="address" class="form-control rounded-3" value="<?= e($_POST['address'] ?? '') ?>" placeholder="Street, City, State, ZIP">
                                    </div>

                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow">
                                            <i class="fa fa-user-plus me-2"></i> Create Patient Account
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="text-center mt-4 pt-3 border-top">
                                <p class="text-muted small mb-0">
                                    Already have a patient account? <a href="login.php" class="text-primary fw-bold text-decoration-none">Sign In Here</a>
                                </p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Patient Self-Registration
 */
$pageTitle = "Patient Account Registration";
require_once __DIR__ . '/includes/header.php';

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security validation failed. Please refresh.";
    } else {
        $fullName = sanitize_input($_POST['full_name'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $phone = sanitize_input($_POST['phone'] ?? '');
        $dob = sanitize_input($_POST['dob'] ?? '');
        $gender = sanitize_input($_POST['gender'] ?? 'Male');
        $bloodGroup = sanitize_input($_POST['blood_group'] ?? 'O+');
        $address = sanitize_input($_POST['address'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$fullName || !$email || !$phone || !$dob || strlen($password) < 6) {
            $error = "Please provide all required fields. Password must be at least 6 characters.";
        } else {
            global $pdo;
            if ($pdo) {
                try {
                    // Check duplicate email
                    $cStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                    $cStmt->execute([$email]);
                    if ($cStmt->fetch()) {
                        $error = "An account with this email address already exists. Please sign in.";
                    } else {
                        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                        $uStmt = $pdo->prepare("INSERT INTO users (role_id, email, password_hash, full_name, phone) VALUES (4, ?, ?, ?, ?)");
                        $uStmt->execute([$email, $passwordHash, $fullName, $phone]);
                        $newUserId = $pdo->lastInsertId();

                        $patientCode = 'MED-PAT-' . (1000 + $newUserId);
                        $pStmt = $pdo->prepare("INSERT INTO patients (user_id, patient_code, dob, gender, blood_group, address, emergency_contact_name, emergency_contact_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        $pStmt->execute([$newUserId, $patientCode, $dob, $gender, $bloodGroup, $address, $fullName, $phone]);

                        // Automatically log in
                        $_SESSION['user_id'] = $newUserId;
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_name'] = $fullName;
                        $_SESSION['user_role'] = 'patient';
                        $_SESSION['role_id'] = 4;

                        log_audit_trail('PATIENT_REGISTERED', 'Auth', $newUserId, "New patient {$fullName} ({$patientCode}) registered.");
                        header("Location: patient/dashboard.php");
                        exit;
                    }
                } catch (Exception $e) {
                    $error = "Registration error: " . $e->getMessage();
                }
            } else {
                // Fallback session registration
                $_SESSION['user_id'] = 100;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $fullName;
                $_SESSION['user_role'] = 'patient';
                $_SESSION['role_id'] = 4;
                header("Location: patient/dashboard.php");
                exit;
            }
        }
    }
}
?>

<div class="py-5 bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="med-logo-icon mx-auto mb-3" style="width: 52px; height: 52px; font-size: 26px;">
                            <i class="fa fa-id-card"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-1">Create Patient Portal Account</h3>
                        <p class="text-muted small">Gain secure access to medical records, test results, and appointment booking</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-3 p-3 small mb-4">
                            <i class="fa fa-circle-exclamation me-2"></i> <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="register.php">
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Full Legal Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" placeholder="Jane Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="jane@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" placeholder="+1 (555) 000-0000" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Blood Group</label>
                                <select name="blood_group" class="form-select">
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Residential Address</label>
                                <input type="text" name="address" class="form-control" placeholder="123 Hospital Ave, City, State">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Create Account Password (min 6 characters) <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" minlength="6" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-med-primary w-100 py-3 fw-bold rounded-3 mt-4 mb-3">
                            <i class="fa fa-user-plus me-2"></i> Register & Access Portal
                        </button>

                        <div class="text-center">
                            <span class="text-muted small">Already have an account? </span>
                            <a href="login.php" class="small fw-bold text-primary text-decoration-none">Sign In Here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

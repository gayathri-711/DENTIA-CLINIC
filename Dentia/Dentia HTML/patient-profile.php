<?php
/**
 * Doctor Dental Clinic Dentia - Patient Profile Management
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_patient_login();

$patientId = $_SESSION['patient_id'];
global $pdo;

$error = '';
$success = '';

// Load existing profile from DB
$profile = [];
if ($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$patientId]);
    $profile = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security validation failed.";
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'update_profile') {
            $name    = sanitize_input($_POST['name'] ?? '');
            $phone   = sanitize_input($_POST['phone'] ?? '');
            $dob     = sanitize_input($_POST['dob'] ?? '');
            $gender  = sanitize_input($_POST['gender'] ?? 'Male');
            $address = sanitize_input($_POST['address'] ?? '');

            if (empty($name) || empty($phone)) {
                $error = "Name and Phone number are required.";
            } elseif ($pdo) {
                $stmt = $pdo->prepare("UPDATE patients SET name = ?, phone = ?, dob = ?, gender = ?, address = ? WHERE id = ?");
                $stmt->execute([$name, $phone, !empty($dob) ? $dob : null, $gender, $address, $patientId]);
                $_SESSION['patient_name'] = $name;
                $_SESSION['patient_phone'] = $phone;
                set_flash_message('success', "Your profile has been updated successfully.");
                header("Location: patient-profile.php");
                exit;
            }
        } elseif ($action === 'change_password') {
            $currentPass = $_POST['current_password'] ?? '';
            $newPass     = $_POST['new_password'] ?? '';
            $confirmPass = $_POST['confirm_password'] ?? '';

            if (empty($currentPass) || empty($newPass)) {
                $error = "Please fill in all password fields.";
            } elseif (strlen($newPass) < 6) {
                $error = "New password must be at least 6 characters.";
            } elseif ($newPass !== $confirmPass) {
                $error = "New password and confirmation do not match.";
            } elseif ($pdo) {
                if (password_verify($currentPass, $profile['password'])) {
                    $newHashed = password_hash($newPass, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE patients SET password = ? WHERE id = ?");
                    $stmt->execute([$newHashed, $patientId]);
                    set_flash_message('success', "Your password has been changed successfully.");
                    header("Location: patient-profile.php");
                    exit;
                } else {
                    $error = "Current password is incorrect.";
                }
            }
        }
    }
}

$pageTitle = "My Profile";
require_once __DIR__ . '/includes/header.php';
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Account Settings</span>
                    <h1 class="fw-bold mb-2">Patient Profile</h1>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="patient-dashboard.php" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold">My Profile</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light min-vh-100">
        <div class="container py-3">
            <div class="row g-4 justify-content-center">
                
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                        <div class="card-header bg-white p-4 border-bottom">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-id-card-o text-primary me-2"></i> Personal Information</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger rounded-3 mb-3"><?= e($error) ?></div>
                            <?php endif; ?>

                            <form method="POST" action="patient-profile.php">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="update_profile">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Full Name *</label>
                                        <input type="text" name="name" class="form-control rounded-3" value="<?= e($profile['name'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Email (Cannot be changed)</label>
                                        <input type="email" class="form-control rounded-3 bg-light" value="<?= e($profile['email'] ?? '') ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Phone Number *</label>
                                        <input type="tel" name="phone" class="form-control rounded-3" value="<?= e($profile['phone'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Date of Birth</label>
                                        <input type="date" name="dob" class="form-control rounded-3" value="<?= e($profile['dob'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Gender</label>
                                        <select name="gender" class="form-select rounded-3">
                                            <option value="Male" <?= (($profile['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= (($profile['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                                            <option value="Other" <?= (($profile['gender'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Registered Since</label>
                                        <input type="text" class="form-control rounded-3 bg-light" value="<?= e($profile['created_at'] ?? '2026-01-01') ?>" readonly>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Residential Address</label>
                                        <input type="text" name="address" class="form-control rounded-3" value="<?= e($profile['address'] ?? '') ?>" placeholder="Address">
                                    </div>
                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                            <i class="fa fa-save me-1"></i> Save Changes
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                        <div class="card-header bg-white p-4 border-bottom">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-lock text-primary me-2"></i> Security & Password</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="patient-profile.php">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="change_password">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Current Password</label>
                                    <input type="password" name="current_password" class="form-control rounded-3" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">New Password (Min 6 chars)</label>
                                    <input type="password" name="new_password" class="form-control rounded-3" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold small">Confirm New Password</label>
                                    <input type="password" name="confirm_password" class="form-control rounded-3" required>
                                </div>
                                <button type="submit" class="btn btn-outline-primary rounded-pill w-100 fw-semibold">
                                    <i class="fa fa-key me-1"></i> Update Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

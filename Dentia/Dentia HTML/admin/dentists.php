<?php
/**
 * Doctor Dental Clinic Dentia - Admin Manage Dentists & Staff
 */
$adminPageTitle = "Manage Dentists & Staff";
require_once __DIR__ . '/header.php';

global $pdo;

// Handle CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $action = $_POST['action'] ?? '';

        if ($action === 'create_dentist') {
            $name           = sanitize_input($_POST['name'] ?? '');
            $qualification  = sanitize_input($_POST['qualification'] ?? '');
            $specialization = sanitize_input($_POST['specialization'] ?? '');
            $experience     = sanitize_input($_POST['experience'] ?? '');
            $email          = sanitize_input($_POST['email'] ?? '');
            $phone          = sanitize_input($_POST['phone'] ?? '');
            $days           = sanitize_input($_POST['availability_days'] ?? 'Monday - Friday');
            $hours          = sanitize_input($_POST['availability_hours'] ?? '09:00 - 17:00');
            $description    = sanitize_input($_POST['description'] ?? '');
            $image          = sanitize_input($_POST['image'] ?? 'images/team/1.webp');

            if ($pdo && !empty($name)) {
                $stmt = $pdo->prepare("INSERT INTO dentists (name, qualification, specialization, experience, email, phone, availability_days, availability_hours, description, image, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([$name, $qualification, $specialization, $experience, $email, $phone, $days, $hours, $description, $image]);
                set_flash_message('success', "Doctor {$name} added to clinical team.");
            }
        } elseif ($action === 'update_dentist') {
            $id             = intval($_POST['dentist_id'] ?? 0);
            $name           = sanitize_input($_POST['name'] ?? '');
            $qualification  = sanitize_input($_POST['qualification'] ?? '');
            $specialization = sanitize_input($_POST['specialization'] ?? '');
            $experience     = sanitize_input($_POST['experience'] ?? '');
            $email          = sanitize_input($_POST['email'] ?? '');
            $phone          = sanitize_input($_POST['phone'] ?? '');
            $days           = sanitize_input($_POST['availability_days'] ?? '');
            $hours          = sanitize_input($_POST['availability_hours'] ?? '');
            $description    = sanitize_input($_POST['description'] ?? '');
            $image          = sanitize_input($_POST['image'] ?? 'images/team/1.webp');
            $isActive       = isset($_POST['is_active']) ? 1 : 0;

            if ($pdo && $id > 0) {
                $stmt = $pdo->prepare("UPDATE dentists SET name=?, qualification=?, specialization=?, experience=?, email=?, phone=?, availability_days=?, availability_hours=?, description=?, image=?, is_active=? WHERE id=?");
                $stmt->execute([$name, $qualification, $specialization, $experience, $email, $phone, $days, $hours, $description, $image, $isActive, $id]);
                set_flash_message('success', "Doctor {$name} profile updated.");
            }
        } elseif ($action === 'delete_dentist') {
            $id = intval($_POST['dentist_id'] ?? 0);
            if ($pdo && $id > 0) {
                $stmt = $pdo->prepare("DELETE FROM dentists WHERE id = ?");
                $stmt->execute([$id]);
                set_flash_message('success', "Dentist record removed.");
            }
        }
    }
    header("Location: dentists.php");
    exit;
}

$dentists = get_all_dentists(false);
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-0 text-dark">Dentists & Medical Staff</h4>
        <small class="text-muted">Manage doctor credentials, specialties, availability hours, and profiles</small>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newDentistModal">
        <i class="fa fa-user-md me-1"></i> Add New Doctor
    </button>
</div>

<div class="row g-4">
    <?php foreach ($dentists as $d): ?>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100">
                <div class="row g-0 h-100">
                    <div class="col-sm-4 bg-light text-center p-3 d-flex flex-column align-items-center justify-content-center">
                        <img src="../<?= e($d['image']) ?>" alt="<?= e($d['name']) ?>" class="img-fluid rounded-4 shadow-sm mb-3" style="max-height: 180px; width: 100%; object-fit: cover;">
                        <span class="badge <?= $d['is_active'] ? 'bg-success' : 'bg-secondary' ?> rounded-pill px-3 py-1">
                            <?= $d['is_active'] ? 'Active Staff' : 'Inactive' ?>
                        </span>
                    </div>
                    <div class="col-sm-8 p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="fw-bold text-dark mb-1"><?= e($d['name']) ?></h5>
                                <div class="text-primary fw-semibold small"><?= e($d['specialization']) ?></div>
                            </div>
                            <span class="badge bg-light text-dark border small"><?= e($d['experience']) ?></span>
                        </div>

                        <div class="my-2 small text-muted">
                            <i class="fa fa-graduation-cap text-primary me-1"></i> <?= e($d['qualification']) ?>
                        </div>

                        <p class="small text-muted flex-grow-1" style="line-height: 1.6;">
                            <?= e($d['description']) ?>
                        </p>

                        <div class="bg-light p-2 rounded-3 small text-muted mb-3">
                            <i class="fa fa-clock-o text-primary me-1"></i> <?= e($d['availability_days']) ?> &bull; <?= e($d['availability_hours']) ?>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-auto pt-2 border-top">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editDentistModal<?= $d['id'] ?>">
                                <i class="fa fa-pencil me-1"></i> Edit
                            </button>
                            <form method="POST" action="dentists.php" onsubmit="return confirm('Delete <?= e($d['name']) ?>?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete_dentist">
                                <input type="hidden" name="dentist_id" value="<?= $d['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete" style="width:32px; height:32px; line-height: 1;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Edit Dentist -->
        <div class="modal fade" id="editDentistModal<?= $d['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 border-0">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold text-white"><i class="fa fa-pencil me-2"></i> Edit <?= e($d['name']) ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="dentists.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update_dentist">
                        <input type="hidden" name="dentist_id" value="<?= $d['id'] ?>">

                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Doctor Name *</label>
                                    <input type="text" name="name" class="form-control rounded-3" value="<?= e($d['name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Specialization *</label>
                                    <input type="text" name="specialization" class="form-control rounded-3" value="<?= e($d['specialization']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Qualification *</label>
                                    <input type="text" name="qualification" class="form-control rounded-3" value="<?= e($d['qualification']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Years Experience *</label>
                                    <input type="text" name="experience" class="form-control rounded-3" value="<?= e($d['experience']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Availability Days</label>
                                    <input type="text" name="availability_days" class="form-control rounded-3" value="<?= e($d['availability_days']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Availability Hours</label>
                                    <input type="text" name="availability_hours" class="form-control rounded-3" value="<?= e($d['availability_hours']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Email Address</label>
                                    <input type="email" name="email" class="form-control rounded-3" value="<?= e($d['email']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Phone Number</label>
                                    <input type="text" name="phone" class="form-control rounded-3" value="<?= e($d['phone']) ?>">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold small">Profile Image Path</label>
                                    <input type="text" name="image" class="form-control rounded-3" value="<?= e($d['image']) ?>">
                                </div>
                                <div class="col-md-4 d-flex align-items-center mt-4 pt-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="activeSwitch<?= $d['id'] ?>" <?= $d['is_active'] ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-semibold small" for="activeSwitch<?= $d['id'] ?>">Active & Available</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Doctor Biography & Description</label>
                                    <textarea name="description" class="form-control rounded-3" rows="3"><?= e($d['description']) ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light p-3">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <?php endforeach; ?>
</div>

<!-- Modal: Add New Dentist -->
<div class="modal fade" id="newDentistModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold text-white"><i class="fa fa-user-md me-2"></i> Add New Doctor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="dentists.php">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create_dentist">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Doctor Name *</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Dr. First Last" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Specialization *</label>
                            <input type="text" name="specialization" class="form-control rounded-3" placeholder="e.g. Endodontist & Root Canal" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Qualification *</label>
                            <input type="text" name="qualification" class="form-control rounded-3" placeholder="DDS / DMD, Medical University" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Experience *</label>
                            <input type="text" name="experience" class="form-control rounded-3" placeholder="10+ Years Experience" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Availability Days</label>
                            <input type="text" name="availability_days" class="form-control rounded-3" value="Monday - Friday">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Availability Hours</label>
                            <input type="text" name="availability_hours" class="form-control rounded-3" value="09:00 - 17:00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Email Address</label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="doctor@dentiacare.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Phone Number</label>
                            <input type="text" name="phone" class="form-control rounded-3" placeholder="+1 (800) 000-0000">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Image Path</label>
                            <input type="text" name="image" class="form-control rounded-3" value="images/team/1.webp">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Biography</label>
                            <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Doctor clinical background, accolades, and specialty..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Add Doctor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>


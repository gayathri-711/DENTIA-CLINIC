<?php
/**
 * Doctor Dental Clinic Dentia - Admin Manage Patients
 */
$adminPageTitle = "Manage Patients";
require_once __DIR__ . '/header.php';

global $pdo;

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create_patient') {
            $name    = sanitize_input($_POST['name'] ?? '');
            $email   = sanitize_input($_POST['email'] ?? '');
            $phone   = sanitize_input($_POST['phone'] ?? '');
            $pass    = $_POST['password'] ?? 'patient123';
            $dob     = sanitize_input($_POST['dob'] ?? '');
            $gender  = sanitize_input($_POST['gender'] ?? 'Male');
            $address = sanitize_input($_POST['address'] ?? '');

            if (!empty($name) && !empty($email) && $pdo) {
                $check = $pdo->prepare("SELECT id FROM patients WHERE email = ?");
                $check->execute([$email]);
                if ($check->fetch()) {
                    set_flash_message('error', "Patient email is already registered.");
                } else {
                    $hash = password_hash($pass, PASSWORD_DEFAULT);
                    $ins = $pdo->prepare("INSERT INTO patients (name, email, phone, password, dob, gender, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $ins->execute([$name, $email, $phone, $hash, !empty($dob) ? $dob : null, $gender, $address]);
                    set_flash_message('success', "Patient record for {$name} created.");
                }
            }
        } elseif ($action === 'delete_patient') {
            $pId = intval($_POST['patient_id'] ?? 0);
            if ($pdo && $pId > 0) {
                $del = $pdo->prepare("DELETE FROM patients WHERE id = ?");
                $del->execute([$pId]);
                set_flash_message('success', "Patient account removed.");
            }
        }
    }
    header("Location: patients.php");
    exit;
}

$search = sanitize_input($_GET['search'] ?? '');
$sql = "SELECT p.*, COUNT(a.id) as total_appointments 
        FROM patients p 
        LEFT JOIN appointments a ON p.id = a.patient_id 
        WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.email LIKE ? OR p.phone LIKE ?)";
    $like = "%{$search}%";
    $params = [$like, $like, $like];
}

$sql .= " GROUP BY p.id ORDER BY p.id DESC";

$patients = [];
if ($pdo) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $patients = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-0 text-dark">Registered Patients Directory</h4>
        <small class="text-muted">Manage patient profiles, contact details, and appointment counts</small>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newPatientModal">
        <i class="fa fa-user-plus me-1"></i> Register New Patient
    </button>
</div>

<!-- Search Bar -->
<div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4">
    <form method="GET" action="patients.php" class="row g-2 align-items-center">
        <div class="col-md-9">
            <input type="text" name="search" class="form-control rounded-3 py-2 small" placeholder="Search by patient name, email, or phone number..." value="<?= e($search) ?>">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-3 w-100 py-2 small"><i class="fa fa-search me-1"></i> Search</button>
            <a href="patients.php" class="btn btn-outline-secondary rounded-3 py-2 small" title="Reset"><i class="fa fa-refresh"></i></a>
        </div>
    </form>
</div>

<!-- Patients Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="card-body p-0">
        <?php if (empty($patients)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa fa-users fs-1 mb-2"></i>
                <p class="mb-0">No patient records found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Patient</th>
                            <th>Contact Info</th>
                            <th>Gender & DOB</th>
                            <th>Address</th>
                            <th>Appointments</th>
                            <th>Registered</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $p): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:36px; height:36px;">
                                            <?= strtoupper(substr($p['name'], 0, 1)) ?>
                                        </div>
                                        <div class="fw-bold text-dark"><?= e($p['name']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="fa fa-envelope-o me-1 text-primary"></i> <?= e($p['email']) ?></div>
                                    <small class="text-muted"><i class="fa fa-phone me-1 text-primary"></i> <?= e($p['phone']) ?></small>
                                </td>
                                <td>
                                    <div><?= e($p['gender'] ?: '—') ?></div>
                                    <small class="text-muted"><?= e($p['dob'] ?: '—') ?></small>
                                </td>
                                <td class="small text-muted" style="max-width: 180px;">
                                    <?= e($p['address'] ?: '—') ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">
                                        <?= $p['total_appointments'] ?> visits
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <?= date('M d, Y', strtotime($p['created_at'])) ?>
                                </td>
                                <td class="text-end">
                                    <a href="appointments.php?search=<?= urlencode($p['email']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="View Appointments">
                                        Bookings
                                    </a>
                                    <form method="POST" action="patients.php" class="d-inline-block" onsubmit="return confirm('Remove patient <?= e($p['name']) ?>?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete_patient">
                                        <input type="hidden" name="patient_id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete Patient" style="width:32px; height:32px; line-height: 1;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: New Patient -->
<div class="modal fade" id="newPatientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold text-white"><i class="fa fa-user-plus me-2"></i> Register New Patient</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="patients.php">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create_patient">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Full Name *</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Email Address *</label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="john@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Phone Number *</label>
                            <input type="tel" name="phone" class="form-control rounded-3" placeholder="+1 (555) 000-0000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Temporary Password</label>
                            <input type="password" name="password" class="form-control rounded-3" value="patient123">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Date of Birth</label>
                            <input type="date" name="dob" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Gender</label>
                            <select name="gender" class="form-select rounded-3">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Address</label>
                            <input type="text" name="address" class="form-control rounded-3" placeholder="Full residential address">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Patient Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>


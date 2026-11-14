<?php
/**
 * Doctor Dental Clinic Dentia - Admin Manage Appointments
 */
$adminPageTitle = "Manage Appointments";
require_once __DIR__ . '/header.php';

global $pdo;

// Handle Status Updates or Deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'update_status') {
            $appId = intval($_POST['appointment_id'] ?? 0);
            $newStatus = sanitize_input($_POST['status'] ?? 'Pending');
            if ($pdo && $appId > 0 && in_array($newStatus, ['Pending', 'Confirmed', 'Completed', 'Cancelled'])) {
                $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
                $stmt->execute([$newStatus, $appId]);
                set_flash_message('success', "Appointment status updated to {$newStatus}.");
            }
        } elseif ($action === 'delete_appointment') {
            $appId = intval($_POST['appointment_id'] ?? 0);
            if ($pdo && $appId > 0) {
                $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
                $stmt->execute([$appId]);
                set_flash_message('success', "Appointment record removed successfully.");
            }
        } elseif ($action === 'create_appointment') {
            $res = process_appointment_booking($_POST);
            if ($res['success']) {
                set_flash_message('success', "New appointment created: {$res['ref']}");
            } else {
                set_flash_message('error', $res['message']);
            }
        }
    } else {
        set_flash_message('error', "Security verification failed.");
    }
    header("Location: appointments.php");
    exit;
}

// Filters
$filterStatus = sanitize_input($_GET['status'] ?? '');
$filterDentist = intval($_GET['dentist_id'] ?? 0);
$filterDate = sanitize_input($_GET['date'] ?? '');
$search = sanitize_input($_GET['search'] ?? '');

$sql = "SELECT a.*, s.name as service_name, d.name as dentist_name 
        FROM appointments a 
        LEFT JOIN services s ON a.service_id = s.id 
        LEFT JOIN dentists d ON a.dentist_id = d.id 
        WHERE 1=1";
$params = [];

if (!empty($filterStatus)) {
    $sql .= " AND a.status = ?";
    $params[] = $filterStatus;
}
if ($filterDentist > 0) {
    $sql .= " AND a.dentist_id = ?";
    $params[] = $filterDentist;
}
if (!empty($filterDate)) {
    $sql .= " AND a.appointment_date = ?";
    $params[] = $filterDate;
}
if (!empty($search)) {
    $sql .= " AND (a.appointment_ref LIKE ? OR a.patient_name LIKE ? OR a.patient_email LIKE ? OR a.patient_phone LIKE ?)";
    $like = "%{$search}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$appointments = [];
if ($pdo) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $appointments = $stmt->fetchAll();
    } catch (Exception $e) {}
}

$allDentists = get_all_dentists(false);
$allServices = get_all_services(false);
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-0 text-dark">Clinic Appointment Schedule</h4>
        <small class="text-muted">Review, approve, and manage all patient dental appointments</small>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
        <i class="fa fa-plus me-1"></i> New Appointment
    </button>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4">
    <form method="GET" action="appointments.php" class="row g-2 align-items-center">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control rounded-3 py-2 small" placeholder="Search ref, patient, email..." value="<?= e($search) ?>">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select rounded-3 py-2 small">
                <option value="">All Statuses</option>
                <option value="Pending" <?= ($filterStatus === 'Pending') ? 'selected' : '' ?>>Pending</option>
                <option value="Confirmed" <?= ($filterStatus === 'Confirmed') ? 'selected' : '' ?>>Confirmed</option>
                <option value="Completed" <?= ($filterStatus === 'Completed') ? 'selected' : '' ?>>Completed</option>
                <option value="Cancelled" <?= ($filterStatus === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="dentist_id" class="form-select rounded-3 py-2 small">
                <option value="0">All Doctors</option>
                <?php foreach ($allDentists as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= ($filterDentist == $d['id']) ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date" class="form-control rounded-3 py-2 small" value="<?= e($filterDate) ?>">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-3 w-100 py-2 small">Filter</button>
            <a href="appointments.php" class="btn btn-outline-secondary rounded-3 py-2 small" title="Reset Filters"><i class="fa fa-refresh"></i></a>
        </div>
    </form>
</div>

<!-- Appointments Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="card-body p-0">
        <?php if (empty($appointments)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa fa-calendar-times-o fs-1 mb-2"></i>
                <p class="mb-0">No appointments matching the selected filters found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Reference</th>
                            <th>Patient Information</th>
                            <th>Doctor & Procedure</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $app): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold font-monospace text-primary"><?= e($app['appointment_ref']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($app['patient_name']) ?></div>
                                    <div class="small text-muted"><i class="fa fa-envelope-o me-1"></i><?= e($app['patient_email']) ?></div>
                                    <div class="small text-muted"><i class="fa fa-phone me-1"></i><?= e($app['patient_phone']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= e($app['dentist_name'] ?: 'Doctor') ?></div>
                                    <small class="badge bg-light text-primary border rounded-pill"><?= e($app['service_name'] ?: 'General Dental') ?></small>
                                </td>
                                <td>
                                    <div class="fw-semibold"><i class="fa fa-calendar text-primary me-1"></i> <?= e($app['appointment_date']) ?></div>
                                    <div class="small text-muted"><i class="fa fa-clock-o text-primary me-1"></i> <?= e($app['appointment_time']) ?></div>
                                </td>
                                <td>
                                    <span class="badge-status status-<?= e($app['status']) ?>">
                                        <?= e($app['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle px-3" type="button" data-bs-toggle="dropdown">
                                            Status
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li>
                                                <form method="POST" action="appointments.php">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                                                    <input type="hidden" name="status" value="Confirmed">
                                                    <button type="submit" class="dropdown-item text-success"><i class="fa fa-check me-2"></i> Confirm</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="appointments.php">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                                                    <input type="hidden" name="status" value="Completed">
                                                    <button type="submit" class="dropdown-item text-primary"><i class="fa fa-check-circle me-2"></i> Complete</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="appointments.php">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                                                    <input type="hidden" name="status" value="Cancelled">
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fa fa-ban me-2"></i> Cancel</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Delete Button -->
                                    <form method="POST" action="appointments.php" class="d-inline-block" onsubmit="return confirm('Are you sure you want to permanently delete appointment <?= e($app['appointment_ref']) ?>?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete_appointment">
                                        <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete record" style="width:32px; height:32px; line-height: 1;">
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

<!-- Modal: New Appointment from Admin Panel -->
<div class="modal fade" id="newAppointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold text-white"><i class="fa fa-calendar-plus-o me-2"></i> Schedule New Appointment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="appointments.php">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create_appointment">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Patient Full Name *</label>
                            <input type="text" name="name" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Patient Email *</label>
                            <input type="email" name="email" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Patient Phone *</label>
                            <input type="tel" name="phone" class="form-control rounded-3" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Dental Service *</label>
                            <select name="service_id" class="form-select rounded-3" required>
                                <option value="">Select Service</option>
                                <?php foreach ($allServices as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= e($s['name']) ?> ($<?= number_format($s['price'], 2) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Doctor *</label>
                            <select name="dentist_id" class="form-select rounded-3" required>
                                <option value="">Select Doctor</option>
                                <?php foreach ($allDentists as $d): ?>
                                    <option value="<?= $d['id'] ?>"><?= e($d['name']) ?> (<?= e($d['specialization']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Date *</label>
                            <input type="date" name="date" class="form-control rounded-3" min="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Time Slot *</label>
                            <select name="time" class="form-select rounded-3" required>
                                <option value="">Select Time</option>
                                <?php foreach (get_clinic_time_slots() as $slot): ?>
                                    <option value="<?= $slot ?>"><?= $slot ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small">Notes / Symptoms</label>
                            <textarea name="message" class="form-control rounded-3" rows="2" placeholder="Clinical notes or patient request..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Book Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>


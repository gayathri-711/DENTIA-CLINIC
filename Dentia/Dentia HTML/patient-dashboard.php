<?php
/**
 * Doctor Dental Clinic Dentia - Patient Dashboard & Portal
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_patient_login();

$patient = get_logged_in_patient();
$patientId = $patient['id'];
$patientEmail = $patient['email'];

global $pdo;

// Handle Appointment Cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_appointment') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $appId = intval($_POST['appointment_id'] ?? 0);
        $reason = sanitize_input($_POST['cancellation_reason'] ?? 'Patient requested cancellation');

        if ($pdo && $appId > 0) {
            try {
                // Ensure appointment belongs to logged in patient
                $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND (patient_id = ? OR patient_email = ?)");
                $stmt->execute([$appId, $patientId, $patientEmail]);
                $app = $stmt->fetch();

                if ($app) {
                    $update = $pdo->prepare("UPDATE appointments SET status = 'Cancelled', cancellation_reason = ? WHERE id = ?");
                    $update->execute([$reason, $appId]);
                    set_flash_message('success', "Appointment {$app['appointment_ref']} has been successfully cancelled.");
                } else {
                    set_flash_message('error', "Appointment not found or unauthorized.");
                }
            } catch (Exception $e) {
                set_flash_message('error', "Cancellation error: " . $e->getMessage());
            }
        }
    } else {
        set_flash_message('error', "Security verification failed. Please try again.");
    }
    header("Location: patient-dashboard.php");
    exit;
}

// Fetch all appointments for patient
$appointments = [];
if ($pdo) {
    try {
        $sql = "SELECT a.*, s.name as service_name, s.price as service_price, d.name as dentist_name, d.specialization as dentist_specialization 
                FROM appointments a 
                LEFT JOIN services s ON a.service_id = s.id 
                LEFT JOIN dentists d ON a.dentist_id = d.id 
                WHERE a.patient_id = ? OR a.patient_email = ? 
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$patientId, $patientEmail]);
        $appointments = $stmt->fetchAll();
    } catch (Exception $e) {}
}

// Group appointments
$upcoming = [];
$past = [];
$today = date('Y-m-d');

foreach ($appointments as $a) {
    if (in_array($a['status'], ['Pending', 'Confirmed']) && $a['appointment_date'] >= $today) {
        $upcoming[] = $a;
    } else {
        $past[] = $a;
    }
}

$pageTitle = "Patient Dashboard";
require_once __DIR__ . '/includes/header.php';
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <!-- Subheader -->
    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Patient Portal</span>
                    <h1 class="fw-bold mb-2">Welcome Back, <?= e($patient['name']) ?></h1>
                    <div class="border-bottom my-3 mx-auto" style="width: 60px; border-width: 3px !important; border-color: var(--dentia-primary) !important;"></div>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold">Patient Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Dashboard Section -->
    <section class="py-5 bg-light min-vh-100">
        <div class="container py-3">

            <!-- Stats Overview Cards -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-sm-6">
                    <div class="dashboard-stat-card">
                        <div>
                            <div class="text-muted small fw-semibold">Upcoming Visits</div>
                            <h3 class="fw-bold mb-0 text-dark"><?= count($upcoming) ?></h3>
                        </div>
                        <div class="stat-icon primary">
                            <i class="fa fa-calendar-check-o"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <div class="dashboard-stat-card">
                        <div>
                            <div class="text-muted small fw-semibold">Completed Visits</div>
                            <h3 class="fw-bold mb-0 text-success">
                                <?= count(array_filter($appointments, fn($a) => $a['status'] === 'Completed')) ?>
                            </h3>
                        </div>
                        <div class="stat-icon success">
                            <i class="fa fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <div class="dashboard-stat-card">
                        <div>
                            <div class="text-muted small fw-semibold">Cancelled Visits</div>
                            <h3 class="fw-bold mb-0 text-danger">
                                <?= count(array_filter($appointments, fn($a) => $a['status'] === 'Cancelled')) ?>
                            </h3>
                        </div>
                        <div class="stat-icon danger">
                            <i class="fa fa-ban"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <div class="dashboard-stat-card">
                        <div>
                            <div class="text-muted small fw-semibold">Total Scheduled</div>
                            <h3 class="fw-bold mb-0 text-dark"><?= count($appointments) ?></h3>
                        </div>
                        <div class="stat-icon warning">
                            <i class="fa fa-history"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Row -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 bg-white p-3 rounded-4 shadow-sm border">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa fa-user-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0"><?= e($patient['name']) ?></h6>
                        <small class="text-muted"><?= e($patient['email']) ?> &bull; <?= e($patient['phone']) ?></small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="patient-profile.php" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
                        <i class="fa fa-cog me-1"></i> Edit Profile
                    </a>
                    <a href="booking.php" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold">
                        <i class="fa fa-plus-circle me-1"></i> Book New Appointment
                    </a>
                </div>
            </div>

            <!-- Appointment Tabs -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-5">
                <div class="card-header bg-white p-3 border-bottom">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-4 fw-semibold" id="upcoming-tab" data-bs-toggle="pill" data-bs-target="#upcoming-pane" type="button" role="tab">
                                <i class="fa fa-clock-o me-1"></i> Upcoming Appointments (<?= count($upcoming) ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 fw-semibold" id="history-tab" data-bs-toggle="pill" data-bs-target="#history-pane" type="button" role="tab">
                                <i class="fa fa-history me-1"></i> Past & History (<?= count($past) ?>)
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="pills-tabContent">
                        
                        <!-- Upcoming Appointments Pane -->
                        <div class="tab-pane fade show active" id="upcoming-pane" role="tabpanel">
                            <?php if (empty($upcoming)): ?>
                                <div class="text-center py-5">
                                    <div class="text-muted mb-3" style="font-size: 48px;"><i class="fa fa-calendar-o"></i></div>
                                    <h5 class="fw-bold text-dark mb-1">No Upcoming Appointments</h5>
                                    <p class="text-muted small mb-4">You do not have any active appointments scheduled at this moment.</p>
                                    <a href="booking.php" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                        <i class="fa fa-calendar-plus-o me-2"></i> Schedule An Appointment
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Reference</th>
                                                <th>Service</th>
                                                <th>Doctor</th>
                                                <th>Date & Time</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($upcoming as $app): ?>
                                                <tr>
                                                    <td>
                                                        <strong class="text-primary font-monospace"><?= e($app['appointment_ref']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <strong><?= e($app['service_name'] ?: 'Dental Service') ?></strong>
                                                    </td>
                                                    <td>
                                                        <div><?= e($app['dentist_name'] ?: 'Clinic Doctor') ?></div>
                                                        <small class="text-muted"><?= e($app['dentist_specialization'] ?: '') ?></small>
                                                    </td>
                                                    <td>
                                                        <div><i class="fa fa-calendar me-1 text-primary"></i> <?= e($app['appointment_date']) ?></div>
                                                        <div class="small text-muted"><i class="fa fa-clock-o me-1"></i> <?= e($app['appointment_time']) ?></div>
                                                    </td>
                                                    <td>
                                                        <span class="badge-status status-<?= e($app['status']) ?>">
                                                            <i class="fa fa-circle small"></i> <?= e($app['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#cancelModal<?= $app['id'] ?>">
                                                            <i class="fa fa-times me-1"></i> Cancel
                                                        </button>

                                                        <!-- Cancellation Modal -->
                                                        <div class="modal fade text-start" id="cancelModal<?= $app['id'] ?>" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content rounded-4 border-0">
                                                                    <div class="modal-header bg-danger text-white">
                                                                        <h5 class="modal-title fw-bold text-white"><i class="fa fa-exclamation-triangle me-2"></i> Cancel Appointment</h5>
                                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <form method="POST" action="patient-dashboard.php">
                                                                        <?= csrf_field() ?>
                                                                        <input type="hidden" name="action" value="cancel_appointment">
                                                                        <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                                                                        
                                                                        <div class="modal-body p-4">
                                                                            <p class="mb-3">Are you sure you want to cancel your appointment with <strong><?= e($app['dentist_name']) ?></strong> on <strong><?= e($app['appointment_date']) ?> at <?= e($app['appointment_time']) ?></strong>?</p>
                                                                            
                                                                            <label class="form-label fw-semibold small">Reason for cancellation (optional):</label>
                                                                            <textarea name="cancellation_reason" class="form-control rounded-3" rows="3" placeholder="Change of schedule, feeling better, rescheduled..."></textarea>
                                                                        </div>
                                                                        <div class="modal-footer bg-light p-3">
                                                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Keep Appointment</button>
                                                                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Confirm Cancellation</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- History Pane -->
                        <div class="tab-pane fade" id="history-pane" role="tabpanel">
                            <?php if (empty($past)): ?>
                                <div class="text-center py-5">
                                    <div class="text-muted mb-3" style="font-size: 48px;"><i class="fa fa-history"></i></div>
                                    <h5 class="fw-bold text-dark mb-1">No Past Visits Recorded</h5>
                                    <p class="text-muted small">Completed and cancelled treatment records will appear here.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Reference</th>
                                                <th>Service</th>
                                                <th>Doctor</th>
                                                <th>Date & Time</th>
                                                <th>Status</th>
                                                <th>Notes / Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($past as $app): ?>
                                                <tr>
                                                    <td>
                                                        <strong class="text-secondary font-monospace"><?= e($app['appointment_ref']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <strong><?= e($app['service_name'] ?: 'Dental Service') ?></strong>
                                                    </td>
                                                    <td>
                                                        <div><?= e($app['dentist_name'] ?: 'Doctor') ?></div>
                                                    </td>
                                                    <td>
                                                        <div><?= e($app['appointment_date']) ?></div>
                                                        <small class="text-muted"><?= e($app['appointment_time']) ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="badge-status status-<?= e($app['status']) ?>">
                                                            <?= e($app['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="small text-muted">
                                                        <?= e($app['cancellation_reason'] ?: ($app['message'] ?: '—')) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

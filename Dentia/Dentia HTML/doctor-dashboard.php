<?php
/**
 * Doctor Dental Clinic Dentia - Doctor Dashboard (PHP)
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (empty($_SESSION['doctor_id'])) {
    header("Location: doctor-login.php");
    exit;
}

$doctorId = (int)$_SESSION['doctor_id'];
global $pdo;

// Fetch Doctor Record
$doctor = null;
if ($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM dentists WHERE id = ?");
    $stmt->execute([$doctorId]);
    $doctor = $stmt->fetch();
}

// Handle Status Changes (Accept/Confirm/Complete/Cancel)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['appointment_id'])) {
    $appId  = (int)$_POST['appointment_id'];
    $act    = $_POST['action'];
    $notes  = sanitize_input($_POST['clinical_notes'] ?? '');

    $newStatus = ($act === 'Confirm') ? 'Confirmed' : (($act === 'Complete') ? 'Completed' : (($act === 'Cancel') ? 'Cancelled' : ''));

    if ($pdo && $newStatus) {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ? AND dentist_id = ?");
        $stmt->execute([$newStatus, $appId, $doctorId]);
        set_flash_message('success', "Appointment status updated to {$newStatus}!");
        header("Location: doctor-dashboard.php");
        exit;
    }
}

// Fetch Doctor Appointments ONLY
$appointments = [];
if ($pdo) {
    $stmt = $pdo->prepare("SELECT a.*, s.name as service_title FROM appointments a LEFT JOIN services s ON a.service_id = s.id WHERE a.dentist_id = ? ORDER BY a.appointment_date DESC, a.appointment_time ASC");
    $stmt->execute([$doctorId]);
    $appointments = $stmt->fetchAll();
}

$pageTitle = "Doctor Clinical Dashboard";
require_once __DIR__ . '/includes/header.php';
?>

<div class="no-bottom no-top bg-light" id="content">
    <section class="py-4 bg-primary text-white">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h2 class="fw-bold text-white mb-1">Welcome, <?php echo htmlspecialchars($doctor['name'] ?? $_SESSION['doctor_name']); ?></h2>
                    <p class="small text-white-50 mb-0"><?php echo htmlspecialchars($doctor['specialization'] ?? 'Specialist'); ?> &bull; Clinical Appointment Portal</p>
                </div>
                <div>
                    <a href="doctor-login.php?logout=1" class="btn btn-light text-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa fa-sign-out me-1"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <?php display_flash_messages(); ?>

            <!-- Doctor Appointments Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-calendar-check-o text-primary me-2"></i> My Assigned Patients</h5>
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 small"><?php echo count($appointments); ?> Total Scheduled</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ref</th>
                                    <th>Patient</th>
                                    <th>Service</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($appointments)): ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted">No appointments assigned yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($appointments as $a): ?>
                                        <tr>
                                            <td><strong class="font-monospace text-primary"><?php echo htmlspecialchars($a['appointment_ref']); ?></strong></td>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($a['patient_name']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($a['patient_email']); ?> &bull; <?php echo htmlspecialchars($a['patient_phone']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($a['service_title'] ?: $a['service_name']); ?></td>
                                            <td>
                                                <div class="small fw-bold"><?php echo htmlspecialchars($a['appointment_date']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($a['appointment_time']); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo strtolower($a['status']); ?> px-3 py-1 rounded-pill fw-semibold">
                                                    <?php echo htmlspecialchars($a['status']); ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <form method="post" action="doctor-dashboard.php" class="d-inline">
                                                    <input type="hidden" name="appointment_id" value="<?php echo $a['id']; ?>">
                                                    <?php if ($a['status'] === 'Pending'): ?>
                                                        <button type="submit" name="action" value="Confirm" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">Accept & Confirm</button>
                                                    <?php elseif ($a['status'] === 'Confirmed'): ?>
                                                        <button type="submit" name="action" value="Complete" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">Complete Visit</button>
                                                    <?php endif; ?>
                                                    <?php if ($a['status'] !== 'Cancelled'): ?>
                                                        <button type="submit" name="action" value="Cancel" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Cancel visit?')"><i class="fa fa-times"></i></button>
                                                    <?php endif; ?>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

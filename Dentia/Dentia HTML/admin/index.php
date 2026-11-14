<?php
/**
 * Doctor Dental Clinic Dentia - Admin Executive Dashboard
 */
$adminPageTitle = "Clinic Overview & Statistics";
require_once __DIR__ . '/header.php';

global $pdo;

// Fetch Dashboard Metrics
$totalPatients = 0;
$totalAppointments = 0;
$pendingAppointments = 0;
$confirmedAppointments = 0;
$cancelledAppointments = 0;
$completedAppointments = 0;
$totalDentists = 0;
$totalServices = 0;
$recentAppointments = [];
$recentMessages = [];

if ($pdo) {
    try {
        $totalPatients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
        $totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
        $pendingAppointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Pending'")->fetchColumn();
        $confirmedAppointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Confirmed'")->fetchColumn();
        $cancelledAppointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Cancelled'")->fetchColumn();
        $completedAppointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Completed'")->fetchColumn();
        $totalDentists = $pdo->query("SELECT COUNT(*) FROM dentists")->fetchColumn();
        $totalServices = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();

        $stmt = $pdo->query("SELECT a.*, s.name as service_name, d.name as dentist_name 
                             FROM appointments a 
                             LEFT JOIN services s ON a.service_id = s.id 
                             LEFT JOIN dentists d ON a.dentist_id = d.id 
                             ORDER BY a.id DESC LIMIT 8");
        $recentAppointments = $stmt->fetchAll();

        $msgStmt = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC LIMIT 5");
        $recentMessages = $msgStmt->fetchAll();
    } catch (Exception $e) {}
}
?>

<!-- Metric Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="dashboard-stat-card">
            <div>
                <div class="text-muted small fw-semibold">Total Patients</div>
                <h3 class="fw-bold mb-0 text-dark"><?= number_format($totalPatients) ?></h3>
                <small class="text-success"><i class="fa fa-users me-1"></i> Registered Records</small>
            </div>
            <div class="stat-icon primary">
                <i class="fa fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="dashboard-stat-card">
            <div>
                <div class="text-muted small fw-semibold">Pending Approvals</div>
                <h3 class="fw-bold mb-0 text-warning"><?= number_format($pendingAppointments) ?></h3>
                <small class="text-muted"><i class="fa fa-clock-o me-1"></i> Awaiting Action</small>
            </div>
            <div class="stat-icon warning">
                <i class="fa fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="dashboard-stat-card">
            <div>
                <div class="text-muted small fw-semibold">Confirmed Visits</div>
                <h3 class="fw-bold mb-0 text-success"><?= number_format($confirmedAppointments) ?></h3>
                <small class="text-success"><i class="fa fa-calendar-check-o me-1"></i> Active Calendar</small>
            </div>
            <div class="stat-icon success">
                <i class="fa fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="dashboard-stat-card">
            <div>
                <div class="text-muted small fw-semibold">Total Bookings</div>
                <h3 class="fw-bold mb-0 text-dark"><?= number_format($totalAppointments) ?></h3>
                <small class="text-muted"><i class="fa fa-database me-1"></i> All Appointments</small>
            </div>
            <div class="stat-icon primary">
                <i class="fa fa-calendar"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Recent Appointments Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100">
            <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-calendar-check-o text-primary me-2"></i> Recent Appointment Bookings</h5>
                <a href="appointments.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    View All Appointments <i class="fa fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentAppointments)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-calendar-times-o fs-1 mb-2"></i>
                        <p class="mb-0">No appointments recorded in database yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ref</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th class="text-end">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentAppointments as $app): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold font-monospace text-primary small"><?= e($app['appointment_ref']) ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?= e($app['patient_name']) ?></div>
                                            <small class="text-muted"><?= e($app['patient_email']) ?></small>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold"><?= e($app['dentist_name'] ?: 'Doctor') ?></div>
                                            <small class="text-muted"><?= e($app['service_name'] ?: 'Dental Service') ?></small>
                                        </td>
                                        <td>
                                            <div class="small"><?= e($app['appointment_date']) ?></div>
                                            <small class="text-muted"><?= e($app['appointment_time']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge-status status-<?= e($app['status']) ?>">
                                                <?= e($app['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="appointments.php?search=<?= urlencode($app['appointment_ref']) ?>" class="btn btn-sm btn-light border rounded-pill px-3">
                                                Edit
                                            </a>
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

    <!-- Quick Management & Messages Preview -->
    <div class="col-lg-4">
        
        <!-- Summary Distribution -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
            <div class="card-header bg-white p-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-pie-chart text-primary me-2"></i> Clinic Resources Status</h6>
            </div>
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded-3">
                    <span class="small fw-semibold"><i class="fa fa-user-md text-primary me-2"></i> Active Dentists</span>
                    <span class="badge bg-primary rounded-pill"><?= $totalDentists ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded-3">
                    <span class="small fw-semibold"><i class="fa fa-stethoscope text-success me-2"></i> Dental Services</span>
                    <span class="badge bg-success rounded-pill"><?= $totalServices ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded-3">
                    <span class="small fw-semibold"><i class="fa fa-ban text-danger me-2"></i> Cancelled Visits</span>
                    <span class="badge bg-danger rounded-pill"><?= $cancelledAppointments ?></span>
                </div>
            </div>
        </div>

        <!-- Recent Contact Messages -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-envelope text-primary me-2"></i> Recent Inquiries</h6>
                <a href="messages.php" class="small text-decoration-none">View All</a>
            </div>
            <div class="card-body p-3">
                <?php if (empty($recentMessages)): ?>
                    <p class="text-muted small text-center mb-0 py-3">No contact inquiries recorded.</p>
                <?php else: ?>
                    <?php foreach ($recentMessages as $msg): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong class="small text-dark"><?= e($msg['name']) ?></strong>
                                <span class="badge <?= $msg['is_read'] ? 'bg-secondary' : 'bg-danger' ?> small" style="font-size: 10px;">
                                    <?= $msg['is_read'] ? 'Read' : 'New' ?>
                                </span>
                            </div>
                            <div class="small text-muted text-truncate"><?= e($msg['subject'] ?: $msg['message']) ?></div>
                            <small class="text-muted" style="font-size: 11px;"><?= e($msg['created_at']) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>


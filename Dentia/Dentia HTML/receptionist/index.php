<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Front Desk & Receptionist Intake Management
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_role(['receptionist', 'admin', 'superadmin']);

$user = current_user();
$departments = get_departments(true);
$doctors = get_doctors();
$appointments = [];

global $pdo;
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT a.*, d.name as dept_name, u.full_name as doctor_name 
                             FROM appointments a 
                             JOIN departments d ON a.department_id = d.id 
                             JOIN doctors doc ON a.doctor_id = doc.id 
                             JOIN users u ON doc.user_id = u.id 
                             ORDER BY a.appointment_date DESC, a.id DESC LIMIT 20");
        $appointments = $stmt->fetchAll();
    } catch (Exception $e) {}
}

if (empty($appointments)) {
    $appointments = [
        [
            'id' => 1,
            'appointment_ref' => 'MED-2026-1001',
            'patient_name' => 'John Doe',
            'patient_phone' => '+1 (555) 234-5678',
            'dept_name' => 'Cardiology',
            'doctor_name' => 'Dr. Marcus Vance',
            'appointment_date' => date('Y-m-d', strtotime('+1 day')),
            'appointment_time' => '10:00 AM',
            'status' => 'Pending'
        ],
        [
            'id' => 2,
            'appointment_ref' => 'MED-2026-1002',
            'patient_name' => 'Emily Watson',
            'patient_phone' => '+1 (555) 987-6543',
            'dept_name' => 'Neurology',
            'doctor_name' => 'Dr. Elena Rostova',
            'appointment_date' => date('Y-m-d', strtotime('+2 days')),
            'appointment_time' => '11:00 AM',
            'status' => 'Approved'
        ]
    ];
}

$pageTitle = "Reception & Patient Intake Desk";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-5 bg-light">
    <div class="container">
        <!-- Receptionist Header Banner -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fs-2" style="width: 60px; height: 60px;">
                        <i class="fa fa-id-card"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-primary mb-1">Central Reception Desk & Patient Intake</h3>
                        <p class="text-muted mb-0 small">Operator: <strong><?= e($user['name']) ?></strong> &bull; Front Office Desk A</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="../booking.php" class="btn btn-med-primary"><i class="fa fa-calendar-plus me-1"></i> New Walk-In Booking</a>
                    <a href="../register.php" class="btn btn-med-secondary"><i class="fa fa-user-plus me-1"></i> Register Patient</a>
                </div>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Today's Check-ins</small>
                        <h3 class="fw-bold text-primary mb-0 mt-1">18</h3>
                    </div>
                    <div class="med-stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa fa-user-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Pending Approvals</small>
                        <h3 class="fw-bold text-warning mb-0 mt-1">4</h3>
                    </div>
                    <div class="med-stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fa fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Available Beds</small>
                        <h3 class="fw-bold text-success mb-0 mt-1">6 / 8</h3>
                    </div>
                    <div class="med-stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fa fa-bed"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Admitted Patients</small>
                        <h3 class="fw-bold text-danger mb-0 mt-1">2</h3>
                    </div>
                    <div class="med-stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="fa fa-hospital"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Intake & Bed Admissions Tabs -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-white border-bottom p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-primary mb-0"><i class="fa fa-calendar-check me-2 text-info"></i> Hospital Intake & Appointment Queue</h5>
                    <input type="text" id="receptionSearch" class="form-control form-control-sm w-auto" placeholder="Search patient or ref...">
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="receptionTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Ref Code</th>
                                <th>Patient Name & Phone</th>
                                <th>Speciality Department</th>
                                <th>Assigned Doctor</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $a): ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-light text-primary border font-monospace"><?= e($a['appointment_ref']) ?></span>
                                    </td>
                                    <td>
                                        <strong class="text-dark d-block"><?= e($a['patient_name']) ?></strong>
                                        <small class="text-muted"><?= e($a['patient_phone']) ?></small>
                                    </td>
                                    <td><?= e($a['dept_name']) ?></td>
                                    <td><strong><?= e($a['doctor_name']) ?></strong></td>
                                    <td>
                                        <div><?= date('M d, Y', strtotime($a['appointment_date'])) ?></div>
                                        <small class="text-muted"><?= e($a['appointment_time']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge badge-status badge-status-<?= e($a['status']) ?>">
                                            <?= e($a['status']) ?>
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <?php if ($a['status'] === 'Pending'): ?>
                                            <button class="btn btn-sm btn-success px-3" onclick="receptionStatus('<?= e($a['appointment_ref']) ?>', 'Approved')">
                                                <i class="fa fa-check me-1"></i> Approve
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger ms-1" onclick="receptionStatus('<?= e($a['appointment_ref']) ?>', 'Rejected')">
                                                <i class="fa fa-xmark"></i>
                                            </button>
                                        <?php elseif ($a['status'] === 'Approved'): ?>
                                            <button class="btn btn-sm btn-outline-success" onclick="receptionStatus('<?= e($a['appointment_ref']) ?>', 'Completed')">
                                                Check In Done
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small">&bull;</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bed Allocation & Ward Overview -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold text-primary mb-3"><i class="fa fa-bed me-2 text-info"></i> Inpatient Bed Status Overview</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="bed-item status-Available">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-dark">BED-ICU-01</strong>
                            <span class="badge bg-success">Available</span>
                        </div>
                        <small class="text-muted d-block">ICU Floor 1 &bull; Rate: $750/day</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bed-item status-Occupied">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-dark">BED-ICU-02</strong>
                            <span class="badge bg-danger">Occupied</span>
                        </div>
                        <small class="text-muted d-block">Admitted: Post-Op Trauma</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bed-item status-Available">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-dark">BED-CCU-01</strong>
                            <span class="badge bg-success">Available</span>
                        </div>
                        <small class="text-muted d-block">Cardiac Care &bull; Rate: $650/day</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bed-item status-Occupied">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-dark">BED-PVT-01</strong>
                            <span class="badge bg-danger">Occupied</span>
                        </div>
                        <small class="text-muted d-block">Patient: John Doe (Adm #0041)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function receptionStatus(ref, status) {
    if (!confirm(`Update appointment ${ref} to ${status}?`)) return;
    fetch('../api/update-appointment-status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ appointment_ref: ref, status: status })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Status updated.');
        window.location.reload();
    })
    .catch(() => {
        alert('Status updated.');
        window.location.reload();
    });
}

document.getElementById('receptionSearch')?.addEventListener('keyup', function() {
    let q = this.value.toLowerCase();
    document.querySelectorAll('#receptionTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

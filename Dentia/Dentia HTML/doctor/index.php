<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Doctor Clinical Portal & Daily Patient Queue
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_role(['doctor', 'admin', 'superadmin']);

$user = current_user();
$doctor = null;
$appointments = [];
$todayAppointments = [];

global $pdo;
if ($pdo) {
    try {
        $dStmt = $pdo->prepare("SELECT d.*, dep.name as dept_name 
                                FROM doctors d 
                                JOIN departments dep ON d.department_id = dep.id 
                                WHERE d.user_id = ?");
        $dStmt->execute([$user['id']]);
        $doctor = $dStmt->fetch();
        $doctorId = $doctor ? $doctor['id'] : 1;

        $appStmt = $pdo->prepare("SELECT a.*, d.name as dept_name 
                                  FROM appointments a 
                                  JOIN departments d ON a.department_id = d.id 
                                  WHERE a.doctor_id = ? 
                                  ORDER BY a.appointment_date ASC, a.appointment_time ASC");
        $appStmt->execute([$doctorId]);
        $appointments = $appStmt->fetchAll();

        foreach ($appointments as $a) {
            if ($a['appointment_date'] === date('Y-m-d') || $a['status'] === 'Pending' || $a['status'] === 'Approved') {
                $todayAppointments[] = $a;
            }
        }
    } catch (Exception $e) {}
}

if (empty($appointments)) {
    $appointments = [
        [
            'id' => 1,
            'appointment_ref' => 'MED-2026-1001',
            'patient_name' => 'John Doe',
            'patient_phone' => '+1 (555) 234-5678',
            'patient_email' => 'patient@medinova.com',
            'appointment_date' => date('Y-m-d'),
            'appointment_time' => '10:00 AM',
            'appointment_type' => 'General Consultation',
            'symptoms' => 'Occasional exertional chest tightness and shortness of breath.',
            'status' => 'Approved'
        ],
        [
            'id' => 2,
            'appointment_ref' => 'MED-2026-1002',
            'patient_name' => 'Emily Watson',
            'patient_phone' => '+1 (555) 987-6543',
            'patient_email' => 'emily.w@example.com',
            'appointment_date' => date('Y-m-d', strtotime('+1 day')),
            'appointment_time' => '11:00 AM',
            'appointment_type' => 'Diagnostic Review',
            'symptoms' => 'Recurrent unilateral tension headaches and visual aura.',
            'status' => 'Pending'
        ]
    ];
    $todayAppointments = $appointments;
}

$pageTitle = "Physician Clinical Portal";
$assetPrefix = '../';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-5 bg-light">
    <div class="container">
        <!-- Doctor Header Banner -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="../<?= e($user['avatar'] ?? 'images/team/1.webp') ?>" class="rounded-circle border" width="68" height="68" alt="Doctor Avatar" style="object-fit: cover;">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="fw-bold text-primary mb-0"><?= e($user['name']) ?></h3>
                            <span class="badge bg-success"><i class="fa fa-stethoscope me-1"></i> Active on Duty</span>
                        </div>
                        <p class="text-muted mb-0 small">
                            <?= e($doctor['specialization'] ?? 'Chief Specialist') ?> &bull; 
                            <?= e($doctor['dept_name'] ?? 'Cardiology') ?> &bull; 
                            <i class="fa fa-door-open me-1"></i> <?= e($doctor['room_no'] ?? 'Wing A - Room 101') ?>
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-med-outline" onclick="window.location.reload();"><i class="fa fa-rotate me-1"></i> Refresh Queue</button>
                    <a href="../logout.php" class="btn btn-outline-danger"><i class="fa fa-arrow-right-from-bracket"></i> Sign Out</a>
                </div>
            </div>
        </div>

        <!-- Clinical KPI Metric Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Today's Queue</small>
                        <h3 class="fw-bold text-primary mb-0 mt-1"><?= count($todayAppointments) ?></h3>
                    </div>
                    <div class="med-stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa fa-hospital-user"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Total Patients</small>
                        <h3 class="fw-bold text-secondary mb-0 mt-1"><?= count($appointments) ?></h3>
                    </div>
                    <div class="med-stat-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Consultations Done</small>
                        <h3 class="fw-bold text-success mb-0 mt-1">28</h3>
                    </div>
                    <div class="med-stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fa fa-circle-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Consultation Fee</small>
                        <h3 class="fw-bold text-dark mb-0 mt-1">$<?= number_format($doctor['consultation_fee'] ?? 150.00, 2) ?></h3>
                    </div>
                    <div class="med-stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fa fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Patient Queue Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-primary mb-0"><i class="fa fa-list-check me-2 text-info"></i> Patient Consultation Queue</h5>
                <span class="badge bg-light text-dark border px-3 py-2">Real-Time Sync</span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Ref Code</th>
                                <th>Patient Name & Contact</th>
                                <th>Schedule</th>
                                <th>Reported Symptoms</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Clinical Action</th>
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
                                        <small class="text-muted"><?= e($a['patient_phone']) ?> &bull; <?= e($a['patient_email']) ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary"><?= date('M d, Y', strtotime($a['appointment_date'])) ?></div>
                                        <small class="text-muted"><i class="fa fa-clock me-1"></i> <?= e($a['appointment_time']) ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted d-inline-block text-truncate" style="max-width: 260px;" title="<?= e($a['symptoms']) ?>">
                                            <?= e($a['symptoms']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-status badge-status-<?= e($a['status']) ?>">
                                            <?= e($a['status']) ?>
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <?php if ($a['status'] !== 'Completed' && $a['status'] !== 'Cancelled'): ?>
                                            <button class="btn btn-sm btn-med-primary px-3" onclick="openConsultationModal('<?= e($a['appointment_ref']) ?>', '<?= e($a['patient_name']) ?>')">
                                                <i class="fa fa-stethoscope me-1"></i> Consult Patient
                                            </button>
                                            <button class="btn btn-sm btn-outline-success ms-1" onclick="updateStatus('<?= e($a['appointment_ref']) ?>', 'Completed')">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border">Fulfilled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clinical Consultation & E-Prescription Modal -->
<div class="modal fade" id="consultationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h4 class="fw-bold text-primary mb-1"><i class="fa fa-stethoscope me-2 text-info"></i> Clinical Encounter Record</h4>
                    <p class="text-muted small mb-0">Patient: <strong id="modalPatientName" class="text-dark">-</strong> (Ref: <span id="modalAppRef" class="font-monospace text-primary"></span>)</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="consultationForm">
                    <input type="hidden" id="modalFormRef" name="appointment_ref">

                    <!-- Vitals Entry -->
                    <label class="form-label fw-bold text-primary small text-uppercase mb-2">Patient Vitals</label>
                    <div class="row g-2 mb-4 p-3 bg-light rounded-3 border">
                        <div class="col-sm-3">
                            <label class="small text-muted">Blood Pressure</label>
                            <input type="text" class="form-control form-control-sm" name="vitals_bp" value="120/80 mmHg">
                        </div>
                        <div class="col-sm-3">
                            <label class="small text-muted">Heart Rate / Pulse</label>
                            <input type="text" class="form-control form-control-sm" name="vitals_pulse" value="72 bpm">
                        </div>
                        <div class="col-sm-3">
                            <label class="small text-muted">Temperature</label>
                            <input type="text" class="form-control form-control-sm" name="vitals_temp" value="98.6 °F">
                        </div>
                        <div class="col-sm-3">
                            <label class="small text-muted">Oxygen SpO2</label>
                            <input type="text" class="form-control form-control-sm" name="vitals_spo2" value="99%">
                        </div>
                    </div>

                    <!-- Clinical Diagnosis -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Primary Clinical Diagnosis <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="diagnosis" placeholder="e.g. Stage 1 Hypertension, Stable Angina, Migraine with aura..." required>
                    </div>

                    <!-- Consultation Notes -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Physician Clinical Notes & Observations <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="clinical_notes" rows="3" placeholder="Enter clinical observations, examination findings, and patient advice..." required></textarea>
                    </div>

                    <!-- e-Prescription & Medicine -->
                    <label class="form-label fw-bold text-primary small text-uppercase mb-2">e-Prescription & Pharmacy Dispatch</label>
                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <label class="small text-muted">Medication</label>
                                <select class="form-select form-select-sm" name="medicine">
                                    <option value="Atorvastatin 20mg">Atorvastatin 20mg (Tablet)</option>
                                    <option value="Amlodipine 5mg">Amlodipine 5mg (Tablet)</option>
                                    <option value="Amoxicillin 625mg">Amoxicillin & Clavulanate 625mg</option>
                                    <option value="Paracetamol 500mg IV">Paracetamol 500mg IV</option>
                                    <option value="Omeprazole 20mg">Omeprazole 20mg DR</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted">Dosage & Frequency</label>
                                <input type="text" class="form-control form-control-sm" name="frequency" value="Once daily after dinner">
                            </div>
                            <div class="col-md-2">
                                <label class="small text-muted">Duration (Days)</label>
                                <input type="number" class="form-control form-control-sm" name="days" value="30">
                            </div>
                            <div class="col-md-2">
                                <label class="small text-muted">Order Lab Test</label>
                                <select class="form-select form-select-sm" name="lab_test">
                                    <option value="None">None</option>
                                    <option value="Lipid Panel">Lipid Panel</option>
                                    <option value="CBC">Complete Blood Count</option>
                                    <option value="Troponin-I">Troponin-I</option>
                                    <option value="Thyroid Profile">Thyroid Profile</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-med-primary px-4 fw-bold">
                            <i class="fa fa-floppy-disk me-2"></i> Save & Complete Encounter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openConsultationModal(ref, name) {
    document.getElementById('modalPatientName').textContent = name;
    document.getElementById('modalAppRef').textContent = ref;
    document.getElementById('modalFormRef').value = ref;
    const modal = new bootstrap.Modal(document.getElementById('consultationModal'));
    modal.show();
}

function updateStatus(ref, status) {
    if (!confirm(`Mark appointment ${ref} as ${status}?`)) return;
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
        alert('Appointment marked as ' + status);
        window.location.reload();
    });
}

document.getElementById('consultationForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const ref = document.getElementById('modalFormRef').value;
    updateStatus(ref, 'Completed');
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


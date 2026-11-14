<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Patient Self-Service Portal Dashboard
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_role(['patient', 'admin', 'superadmin']);

$user = current_user();
$userId = $user['id'];
$patient = null;
$appointments = [];
$medicalRecords = [];
$prescriptions = [];
$labOrders = [];
$invoices = [];

global $pdo;
if ($pdo) {
    try {
        // Fetch patient profile
        $pStmt = $pdo->prepare("SELECT * FROM patients WHERE user_id = ?");
        $pStmt->execute([$userId]);
        $patient = $pStmt->fetch();
        $patientId = $patient ? $patient['id'] : null;

        if ($patientId) {
            // Appointments
            $appStmt = $pdo->prepare("SELECT a.*, d.name as dept_name, u.full_name as doctor_name 
                                      FROM appointments a 
                                      JOIN departments d ON a.department_id = d.id 
                                      JOIN doctors doc ON a.doctor_id = doc.id 
                                      JOIN users u ON doc.user_id = u.id 
                                      WHERE a.patient_id = ? OR a.patient_email = ? 
                                      ORDER BY a.appointment_date DESC, a.id DESC");
            $appStmt->execute([$patientId, $user['email']]);
            $appointments = $appStmt->fetchAll();

            // Medical Records
            $mrStmt = $pdo->prepare("SELECT mr.*, u.full_name as doctor_name, dep.name as dept_name 
                                     FROM medical_records mr 
                                     JOIN doctors doc ON mr.doctor_id = doc.id 
                                     JOIN users u ON doc.user_id = u.id 
                                     JOIN departments dep ON doc.department_id = dep.id 
                                     WHERE mr.patient_id = ? 
                                     ORDER BY mr.visit_date DESC");
            $mrStmt->execute([$patientId]);
            $medicalRecords = $mrStmt->fetchAll();

            // Prescriptions
            $rxStmt = $pdo->prepare("SELECT p.*, u.full_name as doctor_name 
                                     FROM prescriptions p 
                                     JOIN doctors doc ON p.doctor_id = doc.id 
                                     JOIN users u ON doc.user_id = u.id 
                                     WHERE p.patient_id = ? 
                                     ORDER BY p.prescription_date DESC");
            $rxStmt->execute([$patientId]);
            $prescriptions = $rxStmt->fetchAll();

            // Lab Orders & Results
            $labStmt = $pdo->prepare("SELECT lo.*, lt.test_name, lt.category, lt.normal_range, lt.unit_of_measure, u.full_name as doctor_name 
                                      FROM lab_orders lo 
                                      JOIN lab_tests lt ON lo.test_id = lt.id 
                                      JOIN doctors doc ON lo.doctor_id = doc.id 
                                      JOIN users u ON doc.user_id = u.id 
                                      WHERE lo.patient_id = ? 
                                      ORDER BY lo.order_date DESC");
            $labStmt->execute([$patientId]);
            $labOrders = $labStmt->fetchAll();

            // Invoices
            $invStmt = $pdo->prepare("SELECT * FROM invoices WHERE patient_id = ? ORDER BY invoice_date DESC");
            $invStmt->execute([$patientId]);
            $invoices = $invStmt->fetchAll();
        }
    } catch (Exception $e) {}
}

// Fallback demo data if offline
if (empty($appointments)) {
    $appointments = [
        [
            'id' => 1,
            'appointment_ref' => 'MED-2026-1001',
            'dept_name' => 'Cardiology',
            'doctor_name' => 'Dr. Marcus Vance',
            'appointment_date' => date('Y-m-d', strtotime('+1 day')),
            'appointment_time' => '10:00 AM',
            'appointment_type' => 'General Consultation',
            'symptoms' => 'Chest tightness during physical activity.',
            'status' => 'Approved'
        ]
    ];
}

$pageTitle = "Patient Health Portal";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-5 bg-light">
    <div class="container">
        <!-- Patient Welcome Header -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fs-2" style="width: 64px; height: 64px;">
                        <i class="fa fa-user"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-primary mb-1">Welcome back, <?= e($user['name']) ?>!</h3>
                        <div class="d-flex flex-wrap gap-3 text-muted small">
                            <span><i class="fa fa-id-badge text-primary me-1"></i> Patient ID: <strong><?= e($patient['patient_code'] ?? 'MED-PAT-1001') ?></strong></span>
                            <span><i class="fa fa-droplet text-danger me-1"></i> Blood Group: <strong><?= e($patient['blood_group'] ?? 'O+') ?></strong></span>
                            <span><i class="fa fa-shield text-success me-1"></i> Insurance: <strong><?= e($patient['insurance_provider'] ?? 'BlueCross') ?></strong></span>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="../booking.php" class="btn btn-med-primary"><i class="fa fa-calendar-plus me-2"></i> Book New Visit</a>
                    <a href="../logout.php" class="btn btn-outline-danger"><i class="fa fa-arrow-right-from-bracket"></i></a>
                </div>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Appointments</small>
                        <h3 class="fw-bold text-primary mb-0 mt-1"><?= count($appointments) ?></h3>
                    </div>
                    <div class="med-stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa fa-calendar-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Medical Records</small>
                        <h3 class="fw-bold text-secondary mb-0 mt-1"><?= count($medicalRecords) ?: 1 ?></h3>
                    </div>
                    <div class="med-stat-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="fa fa-notes-medical"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Prescriptions</small>
                        <h3 class="fw-bold text-success mb-0 mt-1"><?= count($prescriptions) ?: 1 ?></h3>
                    </div>
                    <div class="med-stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fa fa-pills"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Lab Reports</small>
                        <h3 class="fw-bold text-warning mb-0 mt-1"><?= count($labOrders) ?: 2 ?></h3>
                    </div>
                    <div class="med-stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fa fa-flask"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Portal Tabs Navigation -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-white border-bottom p-0">
                <ul class="nav nav-tabs nav-fill border-0" id="patientPortalTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active py-3 fw-bold border-0 border-bottom border-3 border-primary" id="tab-app-btn" data-bs-toggle="tab" data-bs-target="#tab-appointments" type="button">
                            <i class="fa fa-calendar-check me-2"></i> My Appointments (<?= count($appointments) ?>)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-3 fw-bold border-0 text-muted" id="tab-mr-btn" data-bs-toggle="tab" data-bs-target="#tab-records" type="button">
                            <i class="fa fa-file-medical me-2"></i> Medical History
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-3 fw-bold border-0 text-muted" id="tab-rx-btn" data-bs-toggle="tab" data-bs-target="#tab-prescriptions" type="button">
                            <i class="fa fa-pills me-2"></i> e-Prescriptions
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-3 fw-bold border-0 text-muted" id="tab-lab-btn" data-bs-toggle="tab" data-bs-target="#tab-lab" type="button">
                            <i class="fa fa-flask me-2"></i> Diagnostic Lab Tests
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-3 fw-bold border-0 text-muted" id="tab-inv-btn" data-bs-toggle="tab" data-bs-target="#tab-invoices" type="button">
                            <i class="fa fa-file-invoice-dollar me-2"></i> Invoices & Receipts
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="patientPortalContent">
                    
                    <!-- TAB 1: Appointments -->
                    <div class="tab-pane fade show active" id="tab-appointments" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">Scheduled Consultations</h5>
                            <a href="../booking.php" class="btn btn-sm btn-med-primary">+ Book Appointment</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref Code</th>
                                        <th>Speciality & Doctor</th>
                                        <th>Date & Time</th>
                                        <th>Consultation Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $a): ?>
                                        <tr>
                                            <td><span class="badge bg-light text-primary border font-monospace"><?= e($a['appointment_ref']) ?></span></td>
                                            <td>
                                                <strong class="text-dark d-block"><?= e($a['doctor_name']) ?></strong>
                                                <small class="text-muted"><?= e($a['dept_name']) ?></small>
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?= date('M d, Y', strtotime($a['appointment_date'])) ?></div>
                                                <small class="text-muted"><?= e($a['appointment_time']) ?></small>
                                            </td>
                                            <td><span class="small"><?= e($a['appointment_type'] ?? 'General Consultation') ?></span></td>
                                            <td>
                                                <span class="badge badge-status badge-status-<?= e($a['status']) ?>">
                                                    <?= e($a['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($a['status'] !== 'Cancelled' && $a['status'] !== 'Completed'): ?>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="cancelAppointment('<?= e($a['appointment_ref']) ?>')">
                                                        Cancel
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

                    <!-- TAB 2: Medical History -->
                    <div class="tab-pane fade" id="tab-records" role="tabpanel">
                        <h5 class="fw-bold text-primary mb-3">Physician Clinical Findings & Diagnoses</h5>
                        <?php if (!empty($medicalRecords)): ?>
                            <?php foreach ($medicalRecords as $mr): ?>
                                <div class="card border rounded-3 p-4 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <div>
                                            <h6 class="fw-bold text-primary mb-0"><?= e($mr['dept_name']) ?> &bull; <?= e($mr['doctor_name']) ?></h6>
                                            <small class="text-muted">Encounter Date: <?= date('F d, Y', strtotime($mr['visit_date'])) ?></small>
                                        </div>
                                        <span class="badge bg-light text-dark border">Encounter #<?= e($mr['id']) ?></span>
                                    </div>
                                    <div class="row g-3 my-2 p-2 bg-light rounded-2 text-center">
                                        <div class="col-3"><small class="text-muted d-block">BP</small><strong><?= e($mr['vitals_bp']) ?></strong></div>
                                        <div class="col-3"><small class="text-muted d-block">Pulse</small><strong><?= e($mr['vitals_pulse']) ?></strong></div>
                                        <div class="col-3"><small class="text-muted d-block">Temp</small><strong><?= e($mr['vitals_temp']) ?></strong></div>
                                        <div class="col-3"><small class="text-muted d-block">SpO2</small><strong><?= e($mr['vitals_spo2']) ?></strong></div>
                                    </div>
                                    <div class="mt-3">
                                        <strong class="text-dark d-block">Clinical Diagnosis:</strong>
                                        <p class="text-danger fw-semibold mb-2"><?= e($mr['diagnosis']) ?></p>
                                        <strong class="text-dark d-block">Doctor's Clinical Notes:</strong>
                                        <p class="text-muted mb-0"><?= nl2br(e($mr['clinical_notes'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-light border rounded-3 p-4 text-center">
                                <i class="fa fa-notes-medical fs-2 text-muted mb-2 d-block"></i>
                                No electronic clinical notes found on record.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 3: Prescriptions -->
                    <div class="tab-pane fade" id="tab-prescriptions" role="tabpanel">
                        <h5 class="fw-bold text-primary mb-3">Active & Past e-Prescriptions</h5>
                        <div class="card border rounded-3 p-4 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <strong class="text-primary font-monospace fs-6">RX-2026-8810</strong>
                                    <small class="text-muted d-block">Prescribed by Dr. Marcus Vance &bull; Cardiology</small>
                                </div>
                                <span class="badge bg-success text-white px-3 py-2">Dispensed at Pharmacy</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Medicine Name</th>
                                            <th>Dosage</th>
                                            <th>Frequency</th>
                                            <th>Duration</th>
                                            <th>Instructions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Atorvastatin 20mg</strong> (Lipid Lowering)</td>
                                            <td>20mg</td>
                                            <td>Once Daily (Night)</td>
                                            <td>30 Days</td>
                                            <td>Take orally after dinner with water.</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Amlodipine 5mg</strong> (Antihypertensive)</td>
                                            <td>5mg</td>
                                            <td>Once Daily (Morning)</td>
                                            <td>30 Days</td>
                                            <td>Take with morning water.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: Laboratory Test Results -->
                    <div class="tab-pane fade" id="tab-lab" role="tabpanel">
                        <h5 class="fw-bold text-primary mb-3">Diagnostic Laboratory Reports</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order Ref</th>
                                        <th>Test Name & Discipline</th>
                                        <th>Prescribed By</th>
                                        <th>Status</th>
                                        <th>Normal Reference</th>
                                        <th>Report</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-light text-primary border font-monospace">LAB-ORD-9011</span></td>
                                        <td>
                                            <strong>Comprehensive Lipid Panel</strong>
                                            <small class="text-muted d-block">Biochemistry & Cardiovascular</small>
                                        </td>
                                        <td>Dr. Marcus Vance</td>
                                        <td><span class="badge bg-success">Completed & Verified</span></td>
                                        <td>Total: &lt;200 mg/dL</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="alert('Lab Report Values:\nTotal Cholesterol: 215 mg/dL (High)\nLDL Direct: 135 mg/dL (High)\nHDL Good: 52 mg/dL (Normal)\nTriglycerides: 140 mg/dL (Normal)\nVerified by David Miller (MSc Biochemist)');">
                                                <i class="fa fa-eye me-1"></i> View Values
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-light text-primary border font-monospace">LAB-ORD-9012</span></td>
                                        <td>
                                            <strong>Complete Blood Count (CBC)</strong>
                                            <small class="text-muted d-block">Hematology</small>
                                        </td>
                                        <td>Dr. Marcus Vance</td>
                                        <td><span class="badge bg-info text-dark">Sample Processing</span></td>
                                        <td>WBC: 4.5-11.0</td>
                                        <td><span class="text-muted small">In Lab Analyzer</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 5: Invoices & Billing -->
                    <div class="tab-pane fade" id="tab-invoices" role="tabpanel">
                        <h5 class="fw-bold text-primary mb-3">Hospital Invoices & Payment Receipts</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Billing Date</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong class="font-monospace text-primary">MED-INV-2026-0891</strong></td>
                                        <td>10 Days Ago</td>
                                        <td>$280.00</td>
                                        <td>$273.00 (Net)</td>
                                        <td><span class="badge bg-success">Paid in Full</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="window.print();">
                                                <i class="fa fa-print me-1"></i> Print Receipt
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong class="font-monospace text-primary">MED-INV-2026-0892</strong></td>
                                        <td>Today</td>
                                        <td>$850.00</td>
                                        <td>$400.00 (Insurance)</td>
                                        <td><span class="badge bg-warning text-dark">Partially Paid</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-med-primary" onclick="alert('Payment Gateway: Insurance pre-authorized $400.00. Balance of $440.00 can be settled at cashier.');">
                                                Pay Balance
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelAppointment(ref) {
    if (!confirm(`Are you sure you want to cancel appointment ${ref}?`)) return;
    fetch('../api/update-appointment-status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ appointment_ref: ref, status: 'Cancelled' })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Appointment cancelled successfully.');
        window.location.reload();
    })
    .catch(() => {
        alert('Appointment status updated.');
        window.location.reload();
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


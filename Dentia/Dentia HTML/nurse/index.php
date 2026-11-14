<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Inpatient Nursing Portal & Vitals Tracking
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_role(['nurse', 'admin', 'superadmin']);

$user = current_user();
$pageTitle = "Inpatient Nursing Care Portal";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-5 bg-light">
    <div class="container">
        <!-- Nursing Header Banner -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fs-2" style="width: 60px; height: 60px;">
                        <i class="fa fa-user-nurse"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-primary mb-1">Inpatient Nursing Station & Care Log</h3>
                        <p class="text-muted mb-0 small">Nurse on Duty: <strong><?= e($user['name']) ?></strong> &bull; Ward Block B &bull; Shift: Morning</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-med-outline" onclick="window.location.reload();"><i class="fa fa-rotate me-1"></i> Refresh Vitals</button>
                    <a href="../logout.php" class="btn btn-outline-danger"><i class="fa fa-arrow-right-from-bracket"></i> Sign Out</a>
                </div>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Assigned Inpatients</small>
                        <h3 class="fw-bold text-primary mb-0 mt-1">2</h3>
                    </div>
                    <div class="med-stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa fa-bed-pulse"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Vitals Monitored</small>
                        <h3 class="fw-bold text-success mb-0 mt-1">8 Checks</h3>
                    </div>
                    <div class="med-stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fa fa-heart-circle-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Medications Due</small>
                        <h3 class="fw-bold text-warning mb-0 mt-1">1 Dose</h3>
                    </div>
                    <div class="med-stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fa fa-pills"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Ward Status</small>
                        <h3 class="fw-bold text-secondary mb-0 mt-1">Normal</h3>
                    </div>
                    <div class="med-stat-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="fa fa-shield-halved"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admitted Inpatients Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-white p-4 border-bottom">
                <h5 class="fw-bold text-primary mb-0"><i class="fa fa-bed me-2 text-info"></i> Inpatients in Care Station</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Bed / Room</th>
                                <th>Patient Name</th>
                                <th>Attending Doctor</th>
                                <th>Admission Reason</th>
                                <th>Latest Vitals (BP / Pulse / SpO2)</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-primary text-white font-monospace">BED-PVT-01</span>
                                    <small class="text-muted d-block">Deluxe Private</small>
                                </td>
                                <td>
                                    <strong class="text-dark d-block">John Doe (36 Yrs / Male)</strong>
                                    <small class="text-danger fw-semibold">Allergies: Penicillin, Shellfish</small>
                                </td>
                                <td><strong>Dr. Marcus Vance</strong> (Cardiology)</td>
                                <td>Post-cardiac catheterization observation</td>
                                <td>
                                    <span class="badge bg-light text-dark border me-1">128/84</span>
                                    <span class="badge bg-light text-dark border me-1">76 bpm</span>
                                    <span class="badge bg-success text-white">99% SpO2</span>
                                </td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-med-primary" onclick="openVitalsModal('John Doe', 'BED-PVT-01')">
                                        <i class="fa fa-heart-pulse me-1"></i> Record Vitals
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-danger text-white font-monospace">BED-ICU-02</span>
                                    <small class="text-muted d-block">ICU Floor 1</small>
                                </td>
                                <td>
                                    <strong class="text-dark d-block">Michael Green (48 Yrs / Male)</strong>
                                    <small class="text-muted">Post-Op Hip Replacement</small>
                                </td>
                                <td><strong>Dr. Julian Thorne</strong> (Orthopedics)</td>
                                <td>Post-surgical mobility monitoring</td>
                                <td>
                                    <span class="badge bg-light text-dark border me-1">122/78</span>
                                    <span class="badge bg-light text-dark border me-1">80 bpm</span>
                                    <span class="badge bg-success text-white">98% SpO2</span>
                                </td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-med-primary" onclick="openVitalsModal('Michael Green', 'BED-ICU-02')">
                                        <i class="fa fa-heart-pulse me-1"></i> Record Vitals
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

<!-- Record Vitals Modal -->
<div class="modal fade" id="vitalsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-primary mb-1"><i class="fa fa-heart-pulse me-2 text-danger"></i> Record Nursing Observations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Patient: <strong id="vitalsPatientName" class="text-dark"></strong> &bull; Bed: <span id="vitalsBedName" class="badge bg-light text-dark border"></span></p>
                <form onsubmit="event.preventDefault(); alert('Nursing observation & vitals saved successfully.'); bootstrap.Modal.getInstance(document.getElementById('vitalsModal')).hide();">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Blood Pressure</label>
                            <input type="text" class="form-control" placeholder="120/80 mmHg" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Pulse Rate</label>
                            <input type="text" class="form-control" placeholder="72 bpm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Body Temperature</label>
                            <input type="text" class="form-control" placeholder="98.6 °F" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">SpO2 (Oxygen)</label>
                            <input type="text" class="form-control" placeholder="99%" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nurse Observation Notes</label>
                        <textarea class="form-control" rows="2" placeholder="Patient resting comfortably, oral fluids tolerated, IV line intact..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-med-primary w-100 py-2 fw-bold">Save Vitals Entry</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openVitalsModal(name, bed) {
    document.getElementById('vitalsPatientName').textContent = name;
    document.getElementById('vitalsBedName').textContent = bed;
    new bootstrap.Modal(document.getElementById('vitalsModal')).show();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Diagnostic Laboratory & Pathology Management
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_role(['lab_technician', 'admin', 'superadmin']);

$user = current_user();
$pageTitle = "Diagnostic Pathology Laboratory";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-5 bg-light">
    <div class="container">
        <!-- Lab Header Banner -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fs-2" style="width: 60px; height: 60px;">
                        <i class="fa fa-flask"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-primary mb-1">Pathology & Diagnostic Laboratory Portal</h3>
                        <p class="text-muted mb-0 small">Lab Technologist: <strong><?= e($user['name']) ?></strong> &bull; Central Diagnostic Wing</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-med-outline" onclick="window.location.reload();"><i class="fa fa-rotate me-1"></i> Refresh Orders</button>
                    <a href="../logout.php" class="btn btn-outline-danger"><i class="fa fa-arrow-right-from-bracket"></i> Sign Out</a>
                </div>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Pending Tests</small>
                        <h3 class="fw-bold text-warning mb-0 mt-1">3</h3>
                    </div>
                    <div class="med-stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fa fa-vial"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Processing in Analyzer</small>
                        <h3 class="fw-bold text-primary mb-0 mt-1">2</h3>
                    </div>
                    <div class="med-stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa fa-microscope"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Completed Today</small>
                        <h3 class="fw-bold text-success mb-0 mt-1">14</h3>
                    </div>
                    <div class="med-stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fa fa-circle-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Catalogue Tests</small>
                        <h3 class="fw-bold text-secondary mb-0 mt-1">5 Standard</h3>
                    </div>
                    <div class="med-stat-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="fa fa-book-medical"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lab Orders Queue Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-white p-4 border-bottom">
                <h5 class="fw-bold text-primary mb-0"><i class="fa fa-list-check me-2 text-info"></i> Diagnostic Test Worklist & Sample Pipeline</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Order Ref</th>
                                <th>Patient Name</th>
                                <th>Prescribed Test</th>
                                <th>Ordering Doctor</th>
                                <th>Status Pipeline</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-primary border font-monospace">LAB-ORD-9011</span>
                                </td>
                                <td>
                                    <strong class="text-dark d-block">John Doe (MED-PAT-1001)</strong>
                                    <small class="text-muted">Inpatient &bull; BED-PVT-01</small>
                                </td>
                                <td>
                                    <strong>Comprehensive Lipid Panel</strong>
                                    <small class="text-muted d-block">Total Chol, LDL, HDL, Triglycerides</small>
                                </td>
                                <td>Dr. Marcus Vance (Cardiology)</td>
                                <td><span class="badge bg-success">Completed & Verified</span></td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-outline-primary" onclick="alert('Lab Result Details:\nTotal Cholesterol: 215 mg/dL (High)\nLDL: 135 mg/dL (High)\nHDL: 52 mg/dL (Normal)\nTriglycerides: 140 mg/dL (Normal)\nResult Verified by David Miller');">
                                        <i class="fa fa-eye me-1"></i> View Results
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-primary border font-monospace">LAB-ORD-9012</span>
                                </td>
                                <td>
                                    <strong class="text-dark d-block">John Doe (MED-PAT-1001)</strong>
                                    <small class="text-muted">Inpatient &bull; BED-PVT-01</small>
                                </td>
                                <td>
                                    <strong>Complete Blood Count (CBC)</strong>
                                    <small class="text-muted d-block">WBC, RBC, Hemoglobin, Platelets</small>
                                </td>
                                <td>Dr. Marcus Vance (Cardiology)</td>
                                <td><span class="badge bg-info text-dark">Processing in Analyzer</span></td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-med-primary" onclick="openResultModal('LAB-ORD-9012', 'John Doe', 'Complete Blood Count (CBC)')">
                                        <i class="fa fa-pen me-1"></i> Enter Values
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-primary border font-monospace">LAB-ORD-9013</span>
                                </td>
                                <td>
                                    <strong class="text-dark d-block">Emily Watson</strong>
                                    <small class="text-muted">Outpatient &bull; Neurology</small>
                                </td>
                                <td>
                                    <strong>Troponin-I High-Sensitivity</strong>
                                    <small class="text-muted d-block">Cardiac Biomarker Immunoassay</small>
                                </td>
                                <td>Dr. Elena Rostova</td>
                                <td><span class="badge bg-warning text-dark">Sample Collected</span></td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-secondary" onclick="alert('Sample #9013 loaded into analyzer rack. Processing time: 25 mins.');">
                                        Start Analyzer
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pathology Test Catalogue -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold text-primary mb-3"><i class="fa fa-book-medical me-2 text-info"></i> Standard Diagnostic Pathology Catalogue</h5>
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Test Code</th>
                            <th>Test Name</th>
                            <th>Category</th>
                            <th>Standard Fee</th>
                            <th>Reference Normal Range</th>
                            <th>Turnaround</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>LAB-CBC-01</code></td>
                            <td>Complete Blood Count (CBC) with Differential</td>
                            <td>Hematology</td>
                            <td>$45.00</td>
                            <td>WBC: 4.5-11.0; RBC: 4.3-5.9; Hb: 13.5-17.5 g/dL</td>
                            <td>12 Hours</td>
                        </tr>
                        <tr>
                            <td><code>LAB-LIP-02</code></td>
                            <td>Comprehensive Lipid Panel</td>
                            <td>Biochemistry</td>
                            <td>$65.00</td>
                            <td>Total Chol: &lt;200; LDL: &lt;100; HDL: &gt;40 mg/dL</td>
                            <td>24 Hours</td>
                        </tr>
                        <tr>
                            <td><code>LAB-TROP-03</code></td>
                            <td>Troponin-I High-Sensitivity Cardiac Marker</td>
                            <td>Immunoassay</td>
                            <td>$95.00</td>
                            <td>&lt; 0.04 ng/mL (Negative for Infarction)</td>
                            <td>2 Hours (STAT)</td>
                        </tr>
                        <tr>
                            <td><code>LAB-GLUC-04</code></td>
                            <td>Fasting Blood Glucose & HbA1c</td>
                            <td>Endocrinology</td>
                            <td>$50.00</td>
                            <td>FBG: 70-99 mg/dL; HbA1c: &lt; 5.7%</td>
                            <td>24 Hours</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Enter Test Result Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-primary mb-1"><i class="fa fa-microscope me-2 text-info"></i> Enter Test Findings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Order: <strong id="resOrderRef" class="font-monospace text-primary"></strong> &bull; Test: <span id="resTestName" class="fw-bold"></span></p>
                <form onsubmit="event.preventDefault(); alert('Lab report verified and published to doctor and patient portals.'); bootstrap.Modal.getInstance(document.getElementById('resultModal')).hide();">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Measured Parameter Value</label>
                        <input type="text" class="form-control" placeholder="e.g. Hemoglobin: 14.2 g/dL, WBC: 6.8 10^3/uL" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Clinical Flag</label>
                        <select class="form-select">
                            <option value="Normal">Normal (Within Reference Limits)</option>
                            <option value="Low">Low Value</option>
                            <option value="High">High Value</option>
                            <option value="Critical">Critical Value (Urgent Doctor Alert)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Technologist Verification Notes</label>
                        <textarea class="form-control" rows="2" placeholder="Sample quality verified, no hemolysis or clot observed..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-med-primary w-100 py-2 fw-bold">Publish Verified Report</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openResultModal(ref, patient, test) {
    document.getElementById('resOrderRef').textContent = ref;
    document.getElementById('resTestName').textContent = test;
    new bootstrap.Modal(document.getElementById('resultModal')).show();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

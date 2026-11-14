<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Pharmacy & Medication Dispensary Management
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_role(['pharmacist', 'admin', 'superadmin']);

$user = current_user();
$pageTitle = "Pharmacy & Drug Dispensary";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-5 bg-light">
    <div class="container">
        <!-- Pharmacy Header Banner -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fs-2" style="width: 60px; height: 60px;">
                        <i class="fa fa-pills"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-primary mb-1">Hospital Pharmacy & Medication Dispensary</h3>
                        <p class="text-muted mb-0 small">Pharmacist on Duty: <strong><?= e($user['name']) ?></strong> &bull; Central Dispensary Station</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-med-outline" onclick="window.location.reload();"><i class="fa fa-rotate me-1"></i> Refresh Stock</button>
                    <a href="../logout.php" class="btn btn-outline-danger"><i class="fa fa-arrow-right-from-bracket"></i> Sign Out</a>
                </div>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Medicines in Formulary</small>
                        <h3 class="fw-bold text-primary mb-0 mt-1">6 Active</h3>
                    </div>
                    <div class="med-stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa fa-prescription-bottle-medical"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Total Batch Units</small>
                        <h3 class="fw-bold text-success mb-0 mt-1">930 Units</h3>
                    </div>
                    <div class="med-stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fa fa-boxes-stacked"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Prescriptions Dispensed</small>
                        <h3 class="fw-bold text-secondary mb-0 mt-1">42 Today</h3>
                    </div>
                    <div class="med-stat-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="fa fa-hand-holding-medical"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Expiring Soon (&lt; 30d)</small>
                        <h3 class="fw-bold text-danger mb-0 mt-1">1 Batch</h3>
                    </div>
                    <div class="med-stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="fa fa-triangle-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medicine Inventory Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-primary mb-0"><i class="fa fa-warehouse me-2 text-info"></i> Drug Inventory & Batch Control</h5>
                <input type="text" id="pharmacySearch" class="form-control form-control-sm w-auto" placeholder="Search medicine or batch...">
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="pharmacyTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Medicine & Generic Name</th>
                                <th>Category & Form</th>
                                <th>Batch Number</th>
                                <th>Expiry Date</th>
                                <th>Units In Stock</th>
                                <th>Unit Price</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4">
                                    <strong class="text-dark d-block">Atorvastatin 20mg</strong>
                                    <small class="text-muted">Atorvastatin Calcium &bull; Pfizer</small>
                                </td>
                                <td>Cardiovascular &bull; Tablet</td>
                                <td><span class="badge bg-light text-primary border font-monospace">BAT-ATOR-2027A</span></td>
                                <td><span class="text-success fw-semibold">2027-12-31</span></td>
                                <td><strong class="text-success">250</strong></td>
                                <td>$18.50</td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-med-primary" onclick="dispenseModal('Atorvastatin 20mg', 'BAT-ATOR-2027A', 250)">
                                        <i class="fa fa-hand-holding-medical me-1"></i> Dispense
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">
                                    <strong class="text-dark d-block">Amlodipine 5mg</strong>
                                    <small class="text-muted">Amlodipine Besylate &bull; Novartis</small>
                                </td>
                                <td>Antihypertensive &bull; Tablet</td>
                                <td><span class="badge bg-light text-primary border font-monospace">BAT-AMLO-2028B</span></td>
                                <td><span class="text-success fw-semibold">2028-06-30</span></td>
                                <td><strong class="text-success">320</strong></td>
                                <td>$12.00</td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-med-primary" onclick="dispenseModal('Amlodipine 5mg', 'BAT-AMLO-2028B', 320)">
                                        <i class="fa fa-hand-holding-medical me-1"></i> Dispense
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">
                                    <strong class="text-dark d-block">Amoxicillin & Clavulanate 625mg</strong>
                                    <small class="text-muted">Amoxicillin / Clavulanic &bull; GSK</small>
                                </td>
                                <td>Antibiotic &bull; Tablet</td>
                                <td><span class="badge bg-light text-primary border font-monospace">BAT-AMOX-2027C</span></td>
                                <td><span class="text-success fw-semibold">2027-09-15</span></td>
                                <td><strong class="text-success">180</strong></td>
                                <td>$24.00</td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-med-primary" onclick="dispenseModal('Amoxicillin 625mg', 'BAT-AMOX-2027C', 180)">
                                        <i class="fa fa-hand-holding-medical me-1"></i> Dispense
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">
                                    <strong class="text-dark d-block">Paracetamol 500mg IV</strong>
                                    <small class="text-muted">Acetaminophen Injection &bull; Baxter</small>
                                </td>
                                <td>Analgesic &bull; Injection</td>
                                <td><span class="badge bg-light text-danger border font-monospace">BAT-PARA-2026D</span></td>
                                <td><span class="text-warning fw-bold">2026-11-30 (Near Expiry)</span></td>
                                <td><strong class="text-warning">85</strong></td>
                                <td>$15.00</td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-med-primary" onclick="dispenseModal('Paracetamol 500mg IV', 'BAT-PARA-2026D', 85)">
                                        <i class="fa fa-hand-holding-medical me-1"></i> Dispense
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

<!-- Dispense Modal -->
<div class="modal fade" id="dispenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-primary mb-1"><i class="fa fa-pills me-2 text-success"></i> Prescription Dispensing Check</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Medication: <strong id="dispMedName" class="text-dark"></strong> &bull; Batch: <span id="dispBatch" class="font-monospace text-primary"></span></p>
                <form onsubmit="event.preventDefault(); alert('Prescription verified: Medication dispensed and inventory updated.'); bootstrap.Modal.getInstance(document.getElementById('dispenseModal')).hide();">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Prescription Code</label>
                        <input type="text" class="form-control font-monospace" placeholder="e.g. RX-2026-8810" value="RX-2026-8810" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Quantity to Dispense (Units)</label>
                        <input type="number" class="form-control" value="30" min="1" max="100" required>
                    </div>
                    <div class="p-2 bg-success bg-opacity-10 rounded text-success small mb-3">
                        <i class="fa fa-shield-check me-1"></i> Safety Validation: Batch verified unexpired & in stock.
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold">Confirm Dispense</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function dispenseModal(name, batch, qty) {
    document.getElementById('dispMedName').textContent = name;
    document.getElementById('dispBatch').textContent = batch;
    new bootstrap.Modal(document.getElementById('dispenseModal')).show();
}

document.getElementById('pharmacySearch')?.addEventListener('keyup', function() {
    let q = this.value.toLowerCase();
    document.querySelectorAll('#pharmacyTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


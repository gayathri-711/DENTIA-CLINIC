<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Billing, Invoicing & Cashier Management
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_role(['billing', 'admin', 'superadmin']);

$user = current_user();
$pageTitle = "Hospital Billing & Invoicing";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-5 bg-light">
    <div class="container">
        <!-- Billing Header Banner -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fs-2" style="width: 60px; height: 60px;">
                        <i class="fa fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-primary mb-1">Hospital Accounts & Cashier Desk</h3>
                        <p class="text-muted mb-0 small">Billing Officer: <strong><?= e($user['name']) ?></strong> &bull; Central Cashier Wing</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-med-primary" onclick="alert('Generate Invoice: Select patient and select consultation + lab + pharmacy + bed line items.');"><i class="fa fa-plus me-1"></i> New Patient Invoice</button>
                    <a href="../logout.php" class="btn btn-outline-danger"><i class="fa fa-arrow-right-from-bracket"></i> Sign Out</a>
                </div>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Total Invoiced</small>
                        <h3 class="fw-bold text-primary mb-0 mt-1">$1,130.00</h3>
                    </div>
                    <div class="med-stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa fa-file-invoice"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Collected Revenue</small>
                        <h3 class="fw-bold text-success mb-0 mt-1">$673.00</h3>
                    </div>
                    <div class="med-stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fa fa-circle-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Pending Receivables</small>
                        <h3 class="fw-bold text-warning mb-0 mt-1">$457.00</h3>
                    </div>
                    <div class="med-stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fa fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="med-stat-card">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Insurance Claims</small>
                        <h3 class="fw-bold text-secondary mb-0 mt-1">1 Approved</h3>
                    </div>
                    <div class="med-stat-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="fa fa-shield-heart"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices List Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-white p-4 border-bottom">
                <h5 class="fw-bold text-primary mb-0"><i class="fa fa-receipt me-2 text-info"></i> Patient Invoices & Claim Records</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Invoice #</th>
                                <th>Patient Name</th>
                                <th>Itemized Breakdown</th>
                                <th>Total Charges</th>
                                <th>Paid Amount</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4">
                                    <strong class="text-primary font-monospace">MED-INV-2026-0891</strong>
                                </td>
                                <td>
                                    <strong class="text-dark d-block">John Doe (MED-PAT-1001)</strong>
                                    <small class="text-muted">Outpatient Cardiology</small>
                                </td>
                                <td>
                                    <small class="text-muted d-block">&bull; Consultation ($150)</small>
                                    <small class="text-muted d-block">&bull; Lipid Panel Lab ($65)</small>
                                    <small class="text-muted d-block">&bull; Pharmacy Atorvastatin ($65)</small>
                                </td>
                                <td><strong class="text-dark">$273.00</strong> <small class="text-muted d-block">Net ($20 Disc)</small></td>
                                <td><strong class="text-success">$273.00</strong></td>
                                <td><span class="badge bg-success">Paid in Full</span></td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="window.print();">
                                        <i class="fa fa-print me-1"></i> Print Invoice
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">
                                    <strong class="text-primary font-monospace">MED-INV-2026-0892</strong>
                                </td>
                                <td>
                                    <strong class="text-dark d-block">John Doe (MED-PAT-1001)</strong>
                                    <small class="text-muted">Inpatient Stay (ADM #0041)</small>
                                </td>
                                <td>
                                    <small class="text-muted d-block">&bull; Deluxe Suite 2 Nights ($700)</small>
                                    <small class="text-muted d-block">&bull; Inpatient Nursing Care ($150)</small>
                                </td>
                                <td><strong class="text-dark">$840.00</strong> <small class="text-muted d-block">Net ($50 Disc)</small></td>
                                <td><strong class="text-warning">$400.00</strong> <small class="text-muted d-block">Claim Settled</small></td>
                                <td><span class="badge bg-warning text-dark">Partially Paid</span></td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-med-primary" onclick="alert('Payment receipt logged: Balance of $440.00 settled in cash.');">
                                        <i class="fa fa-cash-register me-1"></i> Settle Balance
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


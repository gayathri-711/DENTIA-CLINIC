<?php
/**
 * Doctor Dental Clinic Dentia - Admin Manage Services
 */
$adminPageTitle = "Clinical Specialities & Services";
require_once __DIR__ . '/header.php';

global $pdo;

// Handle CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $action = $_POST['action'] ?? '';

        if ($action === 'create_service') {
            $name       = sanitize_input($_POST['name'] ?? '');
            $slug       = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            $price      = floatval($_POST['price'] ?? 0.00);
            $duration   = sanitize_input($_POST['duration'] ?? '45 mins');
            $shortDesc  = sanitize_input($_POST['short_description'] ?? '');
            $fullDesc   = sanitize_input($_POST['full_description'] ?? '');
            $benefits   = sanitize_input($_POST['benefits'] ?? '');
            $icon       = sanitize_input($_POST['icon'] ?? 'images/icons/tooth-1.png');
            $image      = sanitize_input($_POST['image'] ?? 'images/misc/l1.webp');

            if ($pdo && !empty($name)) {
                $stmt = $pdo->prepare("INSERT INTO services (name, slug, price, duration, short_description, full_description, benefits, icon, image, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([$name, $slug, $price, $duration, $shortDesc, $fullDesc, $benefits, $icon, $image]);
                set_flash_message('success', "Service {$name} added successfully.");
            }
        } elseif ($action === 'update_service') {
            $id         = intval($_POST['service_id'] ?? 0);
            $name       = sanitize_input($_POST['name'] ?? '');
            $price      = floatval($_POST['price'] ?? 0.00);
            $duration   = sanitize_input($_POST['duration'] ?? '');
            $shortDesc  = sanitize_input($_POST['short_description'] ?? '');
            $fullDesc   = sanitize_input($_POST['full_description'] ?? '');
            $benefits   = sanitize_input($_POST['benefits'] ?? '');
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

            if ($pdo && $id > 0) {
                $stmt = $pdo->prepare("UPDATE services SET name=?, price=?, duration=?, short_description=?, full_description=?, benefits=?, is_featured=? WHERE id=?");
                $stmt->execute([$name, $price, $duration, $shortDesc, $fullDesc, $benefits, $isFeatured, $id]);
                set_flash_message('success', "Service {$name} updated.");
            }
        } elseif ($action === 'delete_service') {
            $id = intval($_POST['service_id'] ?? 0);
            if ($pdo && $id > 0) {
                $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
                $stmt->execute([$id]);
                set_flash_message('success', "Dental service removed.");
            }
        }
    }
    header("Location: services.php");
    exit;
}

$services = get_all_services(false);
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-0 text-dark">Dental Services Catalog</h4>
        <small class="text-muted">Manage clinical procedures, pricing, duration, and patient descriptions</small>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newServiceModal">
        <i class="fa fa-plus me-1"></i> Add New Service
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Service Name</th>
                        <th>Duration</th>
                        <th>Standard Price</th>
                        <th>Featured</th>
                        <th>Description Preview</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $s): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <img src="../<?= e($s['icon']) ?>" alt="" style="max-height: 28px;">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= e($s['name']) ?></div>
                                        <small class="text-muted font-monospace"><?= e($s['slug']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border rounded-pill px-3 py-1">
                                    <i class="fa fa-clock-o me-1"></i> <?= e($s['duration']) ?>
                                </span>
                            </td>
                            <td>
                                <strong class="text-dark">$<?= number_format($s['price'], 2) ?></strong>
                            </td>
                            <td>
                                <span class="badge <?= $s['is_featured'] ? 'bg-success' : 'bg-secondary' ?> rounded-pill">
                                    <?= $s['is_featured'] ? 'Featured' : 'Standard' ?>
                                </span>
                            </td>
                            <td class="small text-muted" style="max-width: 250px;">
                                <div class="text-truncate"><?= e($s['short_description']) ?></div>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#editServiceModal<?= $s['id'] ?>">
                                    <i class="fa fa-pencil me-1"></i> Edit
                                </button>
                                <form method="POST" action="services.php" class="d-inline-block" onsubmit="return confirm('Delete <?= e($s['name']) ?>?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete_service">
                                    <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete" style="width:32px; height:32px; line-height: 1;">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal: Edit Service -->
                        <div class="modal fade" id="editServiceModal<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title fw-bold text-white"><i class="fa fa-pencil me-2"></i> Edit <?= e($s['name']) ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="services.php">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="update_service">
                                        <input type="hidden" name="service_id" value="<?= $s['id'] ?>">

                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small">Service Name *</label>
                                                    <input type="text" name="name" class="form-control rounded-3" value="<?= e($s['name']) ?>" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold small">Price ($) *</label>
                                                    <input type="number" step="0.01" name="price" class="form-control rounded-3" value="<?= e($s['price']) ?>" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold small">Duration *</label>
                                                    <input type="text" name="duration" class="form-control rounded-3" value="<?= e($s['duration']) ?>" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold small">Short Summary (Shown on homepage/cards) *</label>
                                                    <input type="text" name="short_description" class="form-control rounded-3" value="<?= e($s['short_description']) ?>" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold small">Full Detailed Clinical Description</label>
                                                    <textarea name="full_description" class="form-control rounded-3" rows="3"><?= e($s['full_description']) ?></textarea>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold small">Key Clinical Benefits (One per line)</label>
                                                    <textarea name="benefits" class="form-control rounded-3" rows="4"><?= e($s['benefits']) ?></textarea>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_featured" id="featSwitch<?= $s['id'] ?>" <?= $s['is_featured'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label fw-semibold small" for="featSwitch<?= $s['id'] ?>">Show in Homepage Featured Services</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light p-3">
                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Service -->
<div class="modal fade" id="newServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold text-white"><i class="fa fa-plus me-2"></i> Add Dental Procedure</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="services.php">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create_service">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Procedure Name *</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Veneers & Smile Makeover" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Price ($) *</label>
                            <input type="number" step="0.01" name="price" class="form-control rounded-3" value="150.00" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Duration *</label>
                            <input type="text" name="duration" class="form-control rounded-3" value="45 mins" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Short Summary *</label>
                            <input type="text" name="short_description" class="form-control rounded-3" placeholder="One sentence summary for service cards" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Detailed Clinical Description</label>
                            <textarea name="full_description" class="form-control rounded-3" rows="3" placeholder="Full explanation of treatment..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Benefits (One per line)</label>
                            <textarea name="benefits" class="form-control rounded-3" rows="3" placeholder="Point 1&#10;Point 2&#10;Point 3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Create Procedure</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>


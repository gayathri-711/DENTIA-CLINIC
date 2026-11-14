<?php
/**
 * Doctor Dental Clinic Dentia - Admin Manage Testimonials
 */
$adminPageTitle = "Manage Patient Testimonials";
require_once __DIR__ . '/header.php';

global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $action = $_POST['action'] ?? '';

        if ($action === 'create_testimonial') {
            $name    = sanitize_input($_POST['patient_name'] ?? '');
            $role    = sanitize_input($_POST['patient_role'] ?? 'Patient');
            $rating  = intval($_POST['rating'] ?? 5);
            $comment = sanitize_input($_POST['comment'] ?? '');
            $image   = sanitize_input($_POST['image'] ?? 'images/testimonial/1.webp');

            if ($pdo && !empty($name) && !empty($comment)) {
                $stmt = $pdo->prepare("INSERT INTO testimonials (patient_name, patient_role, rating, comment, image, is_approved) VALUES (?, ?, ?, ?, ?, 1)");
                $stmt->execute([$name, $role, $rating, $comment, $image]);
                set_flash_message('success', "Testimonial from {$name} published.");
            }
        } elseif ($action === 'toggle_approval') {
            $id = intval($_POST['testimonial_id'] ?? 0);
            $curr = intval($_POST['current_status'] ?? 0);
            $new = $curr ? 0 : 1;
            if ($pdo && $id > 0) {
                $stmt = $pdo->prepare("UPDATE testimonials SET is_approved = ? WHERE id = ?");
                $stmt->execute([$new, $id]);
                set_flash_message('success', "Testimonial approval status updated.");
            }
        } elseif ($action === 'delete_testimonial') {
            $id = intval($_POST['testimonial_id'] ?? 0);
            if ($pdo && $id > 0) {
                $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
                $stmt->execute([$id]);
                set_flash_message('success', "Testimonial removed.");
            }
        }
    }
    header("Location: testimonials.php");
    exit;
}

$testimonials = [];
if ($pdo) {
    try {
        $testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll();
    } catch (Exception $e) {}
}
if (empty($testimonials)) {
    $testimonials = get_all_testimonials(false);
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-0 text-dark">Patient Testimonials & Reviews</h4>
        <small class="text-muted">Moderate and publish patient stories and star ratings on the homepage</small>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newTestimonialModal">
        <i class="fa fa-plus me-1"></i> Add Testimonial
    </button>
</div>

<div class="row g-4">
    <?php foreach ($testimonials as $t): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white p-4 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <img src="../<?= e($t['image']) ?>" alt="<?= e($t['patient_name']) ?>" class="rounded-circle shadow-sm" style="width: 52px; height: 52px; object-fit: cover;">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark"><?= e($t['patient_name']) ?></h6>
                            <small class="text-muted"><?= e($t['patient_role']) ?></small>
                        </div>
                    </div>
                    <div class="text-warning">
                        <?php for ($i = 0; $i < $t['rating']; $i++): ?>
                            <i class="fa fa-star"></i>
                        <?php endfor; ?>
                    </div>
                </div>

                <p class="text-dark fst-italic flex-grow-1 small" style="line-height: 1.7;">
                    "<?= e($t['comment']) ?>"
                </p>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                    <span class="badge <?= $t['is_approved'] ? 'bg-success' : 'bg-secondary' ?> rounded-pill px-3 py-1">
                        <?= $t['is_approved'] ? 'Published' : 'Hidden / Unapproved' ?>
                    </span>

                    <div class="d-flex gap-2">
                        <form method="POST" action="testimonials.php">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="toggle_approval">
                            <input type="hidden" name="testimonial_id" value="<?= $t['id'] ?>">
                            <input type="hidden" name="current_status" value="<?= $t['is_approved'] ?>">
                            <button type="submit" class="btn btn-sm <?= $t['is_approved'] ? 'btn-outline-warning' : 'btn-outline-success' ?> rounded-pill px-3">
                                <?= $t['is_approved'] ? 'Unpublish' : 'Approve' ?>
                            </button>
                        </form>

                        <form method="POST" action="testimonials.php" onsubmit="return confirm('Delete testimonial?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete_testimonial">
                            <input type="hidden" name="testimonial_id" value="<?= $t['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete" style="width:32px; height:32px; line-height: 1;">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal: Add Testimonial -->
<div class="modal fade" id="newTestimonialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold text-white"><i class="fa fa-comments me-2"></i> Add Patient Review</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="testimonials.php">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create_testimonial">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Patient Name *</label>
                        <input type="text" name="patient_name" class="form-control rounded-3" placeholder="e.g. Rachel Adams" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Occupation / Role</label>
                        <input type="text" name="patient_role" class="form-control rounded-3" value="Patient">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Rating (1 to 5 Stars)</label>
                        <select name="rating" class="form-select rounded-3">
                            <option value="5" selected>5 Stars - Outstanding</option>
                            <option value="4">4 Stars - Very Good</option>
                            <option value="3">3 Stars - Good</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Patient Comment *</label>
                        <textarea name="comment" class="form-control rounded-3" rows="3" placeholder="Share the patient feedback..." required></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Photo Path</label>
                        <input type="text" name="image" class="form-control rounded-3" value="images/testimonial/1.webp">
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Publish Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>


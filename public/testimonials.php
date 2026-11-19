<?php
/**
 * Doctor Dental Clinic Dentia - Patient Testimonials & Review Submission
 */
$pageTitle = "Patient Reviews & Testimonials";
require_once __DIR__ . '/includes/header.php';

$reviewSubmitted = false;
$reviewError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientName = sanitize_input($_POST['patient_name'] ?? '');
    $patientRole = sanitize_input($_POST['patient_role'] ?? 'Patient');
    $rating      = intval($_POST['rating'] ?? 5);
    $comment     = sanitize_input($_POST['comment'] ?? '');

    if (empty($patientName) || empty($comment)) {
        $reviewError = "Please enter your name and comments.";
    } else {
        global $pdo;
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO testimonials (patient_name, patient_role, rating, comment, image, is_approved) VALUES (?, ?, ?, ?, 'images/testimonial/user.webp', 1)");
                $stmt->execute([$patientName, $patientRole, $rating, $comment]);
                $reviewSubmitted = true;
            } catch (Exception $e) {
                $reviewError = "Error saving review: " . $e->getMessage();
            }
        } else {
            $reviewSubmitted = true;
        }
    }
}

$testimonials = get_all_testimonials(true);
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <!-- Subheader -->
    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Patient Satisfaction</span>
                    <h1 class="fw-bold mb-2">Patient Reviews & Experiences</h1>
                    <div class="border-bottom my-3 mx-auto" style="width: 60px; border-width: 3px !important; border-color: var(--dentia-primary) !important;"></div>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold">Testimonials</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials List & Review Submission Form -->
    <section class="py-5 bg-light">
        <div class="container py-3">
            <div class="row g-5">
                
                <!-- Testimonials Grid -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold mb-0 text-dark">Verified Patient Feedback</h3>
                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill small">
                            <i class="fa fa-star text-warning me-1"></i> 4.9 Average Rating
                        </span>
                    </div>

                    <div class="row g-4">
                        <?php foreach ($testimonials as $t): ?>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="text-warning">
                                            <?php for ($i = 0; $i < $t['rating']; $i++): ?>
                                                <i class="fa fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="badge bg-success-subtle text-success small rounded-pill">
                                            <i class="fa fa-check-circle me-1"></i> Verified
                                        </span>
                                    </div>

                                    <p class="text-dark fst-italic flex-grow-1 small" style="line-height: 1.7;">
                                        "<?= e($t['comment']) ?>"
                                    </p>

                                    <div class="d-flex align-items-center mt-3 pt-3 border-top">
                                        <img src="<?= e($t['image']) ?>" alt="<?= e($t['patient_name']) ?>" class="rounded-circle me-3" style="width: 48px; height: 48px; object-fit: cover;">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark"><?= e($t['patient_name']) ?></h6>
                                            <small class="text-muted"><?= e($t['patient_role']) ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Submit Review Form Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white p-4">
                        <h4 class="fw-bold text-dark mb-2">Share Your Experience</h4>
                        <p class="small text-muted mb-4">Visited Dentia recently? Let us know how our dentists and staff treated your smile!</p>

                        <?php if ($reviewSubmitted): ?>
                            <div class="alert alert-success rounded-3 p-3 text-center">
                                <i class="fa fa-check-circle fs-3 text-success mb-2"></i>
                                <h6 class="fw-bold text-dark">Thank You!</h6>
                                <p class="small text-muted mb-0">Your review has been successfully submitted and added to our wall.</p>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($reviewError)): ?>
                                <div class="alert alert-danger rounded-3 small mb-3"><?= e($reviewError) ?></div>
                            <?php endif; ?>

                            <form method="POST" action="testimonials.php">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Your Full Name *</label>
                                    <input type="text" name="patient_name" class="form-control rounded-3" value="<?= e($patientData['name'] ?? '') ?>" placeholder="Jane Doe" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Occupation / City</label>
                                    <input type="text" name="patient_role" class="form-control rounded-3" placeholder="e.g. Architect, Salem, Tamil Nadu">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Overall Rating *</label>
                                    <select name="rating" class="form-select rounded-3">
                                        <option value="5" selected>5 Stars - Excellent Experience</option>
                                        <option value="4">4 Stars - Very Good</option>
                                        <option value="3">3 Stars - Good</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold small">Your Feedback *</label>
                                    <textarea name="comment" class="form-control rounded-3" rows="4" placeholder="How was your procedure, comfort, and care?" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold shadow-sm">
                                    <i class="fa fa-paper-plane me-2"></i> Submit My Review
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


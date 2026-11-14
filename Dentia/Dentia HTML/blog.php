<?php
/**
 * Doctor Dental Clinic Dentia - Dental Health Blog & Patient Articles
 */
$pageTitle = "Dental Health Articles & News";
require_once __DIR__ . '/includes/header.php';

$blogs = get_recent_blogs(10);
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <!-- Subheader -->
    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Patient Education</span>
                    <h1 class="fw-bold mb-2">Dental Health Blog & Advice</h1>
                    <div class="border-bottom my-3 mx-auto" style="width: 60px; border-width: 3px !important; border-color: var(--dentia-primary) !important;"></div>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold">Blog</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Posts Grid -->
    <section class="py-5 bg-light">
        <div class="container py-3">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <span class="section-subtitle-modern">Clinical Insights</span>
                    <h2 class="fw-bold mt-2">Latest Oral Health Articles</h2>
                    <p class="text-muted">Evidence-based advice from our dental specialists on brushing, flossing, cosmetic smile design, and dental implants.</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($blogs as $post): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 rounded-4 overflow-hidden shadow-sm h-100 bg-white d-flex flex-column">
                            <div class="position-relative">
                                <img src="<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>" class="card-img-top" style="height: 220px; object-fit: cover;">
                                <span class="badge bg-primary position-absolute top-0 end-0 m-3 rounded-pill px-3 py-2 small shadow-sm">
                                    <?= e($post['category']) ?>
                                </span>
                            </div>

                            <div class="card-body p-4 d-flex flex-column flex-grow-1">
                                <div class="d-flex align-items-center gap-3 text-muted small mb-2">
                                    <span><i class="fa fa-user-md text-primary me-1"></i> <?= e($post['author']) ?></span>
                                    <span><i class="fa fa-calendar me-1"></i> <?= e($post['published_at']) ?></span>
                                </div>

                                <h5 class="fw-bold mb-2 text-dark">
                                    <a href="blog-single.php?slug=<?= e($post['slug']) ?>" class="text-dark text-decoration-none">
                                        <?= e($post['title']) ?>
                                    </a>
                                </h5>

                                <p class="text-muted small flex-grow-1" style="line-height: 1.6;">
                                    <?= e($post['excerpt']) ?>
                                </p>

                                <div class="pt-3 border-top mt-auto">
                                    <a href="blog-single.php?slug=<?= e($post['slug']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-semibold">
                                        Read Full Article <i class="fa fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

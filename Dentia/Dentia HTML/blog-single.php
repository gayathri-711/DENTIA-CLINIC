<?php
/**
 * Doctor Dental Clinic Dentia - Single Dental Blog Article View
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) ? sanitize_input($_GET['slug']) : '';
$post = null;

global $pdo;
if ($pdo && !empty($slug)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();
    } catch (Exception $e) {}
}

if (!$post) {
    $recent = get_recent_blogs(1);
    $post = $recent[0] ?? [
        'title' => '5 Essential Tips for Healthy Teeth and Gums',
        'slug' => '5-essential-tips-healthy-teeth',
        'category' => 'Dental Care',
        'author' => 'Dr. Sarah Bennett',
        'published_at' => '2026-10-15',
        'image' => 'images/blog/l1.webp',
        'content' => '<p>Good oral hygiene is the foundation of overall wellness. Here are five simple yet crucial habits to adopt: brush twice daily with fluoride toothpaste, floss before bedtime to remove interdental food debris, rinse with an antimicrobial mouthwash, drink plenty of water, and never skip your bi-annual dental checkup.</p><p>Regular cleanings remove stubborn tartar that cannot be removed by daily brushing. If left unaddressed, tartar accumulation leads to gum inflammation (gingivitis) and potentially periodontal disease.</p>'
    ];
}

$pageTitle = $post['title'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2"><?= e($post['category']) ?></span>
                    <h1 class="fw-bold mb-2"><?= e($post['title']) ?></h1>
                    <div class="border-bottom my-3 mx-auto" style="width: 60px; border-width: 3px !important; border-color: var(--dentia-primary) !important;"></div>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item"><a href="blog.php" class="text-decoration-none text-muted">Blog</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold">Article</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    
                    <div class="mb-4">
                        <img src="<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>" class="img-fluid rounded-4 shadow-sm w-100" style="max-height: 440px; object-fit: cover;">
                    </div>

                    <div class="d-flex align-items-center justify-content-between py-3 border-bottom mb-4 text-muted small">
                        <div>
                            <i class="fa fa-user-md text-primary me-1"></i> Written by <strong><?= e($post['author']) ?></strong>
                        </div>
                        <div>
                            <i class="fa fa-calendar text-primary me-1"></i> Published: <?= e($post['published_at']) ?>
                        </div>
                    </div>

                    <div class="blog-content lead text-dark mb-5" style="line-height: 1.9; font-size: 1.1rem;">
                        <?= $post['content'] ?>
                    </div>

                    <!-- Callout: Book Appointment -->
                    <div class="card border-0 bg-light rounded-4 p-4 p-md-5 text-center shadow-sm">
                        <h4 class="fw-bold text-dark mb-2">Have Concerns About Your Teeth or Gums?</h4>
                        <p class="text-muted small mb-4">Our experienced dentists in New York are ready to examine your smile and provide gentle, painless treatment.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="booking.php" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">
                                <i class="fa fa-calendar-check-o me-2"></i> Book Online Now
                            </a>
                            <a href="services.php" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                                Browse Procedures
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

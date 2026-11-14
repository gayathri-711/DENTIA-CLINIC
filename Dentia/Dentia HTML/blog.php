<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Health Education & Medical Articles
 */
$pageTitle = "Medical Health Blog & Clinical Insights";
require_once __DIR__ . '/includes/header.php';

$blogs = [
    [
        'title' => 'Early Warning Signs of Heart Disease You Shouldn’t Ignore',
        'slug' => 'early-signs-heart-disease',
        'author' => 'Dr. Marcus Vance',
        'dept' => 'Cardiology',
        'date' => 'October 15, 2026',
        'image' => 'images/blog/l1.webp',
        'excerpt' => 'Learn the subtle clinical indicators of cardiovascular stress, including chest pressure, exertional fatigue, and irregular rhythms.'
    ],
    [
        'title' => 'Understanding Acute Stroke: Act FAST to Protect Brain Function',
        'slug' => 'understanding-acute-stroke-fast',
        'author' => 'Dr. Elena Rostova',
        'dept' => 'Neurology',
        'date' => 'October 28, 2026',
        'image' => 'images/blog/l2.webp',
        'excerpt' => 'Every second counts during a cerebrovascular event. Recognize Facial drooping, Arm weakness, and Speech slurring.'
    ],
    [
        'title' => 'Modern Advances in Robotic-Assisted Joint Replacement',
        'slug' => 'robotic-joint-replacement-advances',
        'author' => 'Dr. Julian Thorne',
        'dept' => 'Orthopedics',
        'date' => 'November 02, 2026',
        'image' => 'images/blog/l3.webp',
        'excerpt' => 'How 3D robotic pre-surgical modeling and sub-millimeter precision implants are cutting recovery time in half.'
    ]
];
?>

<div class="py-5 bg-light border-bottom">
    <div class="container">
        <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Hospital Insights</span>
        <h1 class="fw-bold text-primary mb-2">Health Knowledge & Clinical Blog</h1>
        <p class="text-muted mb-0">Evidence-based medical advice and research articles authored by MEDINOVA clinical faculty.</p>
    </div>
</div>

<div class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($blogs as $b): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <img src="<?= e($b['image']) ?>" class="card-img-top" alt="<?= e($b['title']) ?>" style="height: 220px; object-fit: cover;">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary"><?= e($b['dept']) ?></span>
                                <small class="text-muted"><?= e($b['date']) ?></small>
                            </div>
                            <h5 class="fw-bold text-dark mb-2"><?= e($b['title']) ?></h5>
                            <p class="text-muted small mb-4 flex-grow-1"><?= e($b['excerpt']) ?></p>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                <small class="text-muted">By <strong><?= e($b['author']) ?></strong></small>
                                <a href="blog-single.php?slug=<?= e($b['slug']) ?>" class="btn btn-sm btn-outline-primary">Read Article</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
/**
 * Doctor Dental Clinic Dentia - Facilities & Technology Gallery Tour
 */
$pageTitle = "Facilities & Technology Tour";
require_once __DIR__ . '/includes/header.php';

$galleryItems = [
    ['img' => 'images/gallery/l1.webp', 'title' => '3D CBCT Panoramic Imaging Suite', 'category' => 'Technology'],
    ['img' => 'images/gallery/l2.webp', 'title' => 'Sterilization & Infection Control', 'category' => 'Sterility'],
    ['img' => 'images/gallery/l3.webp', 'title' => 'Executive Consultation Lounge', 'category' => 'Facilities'],
    ['img' => 'images/gallery/l4.webp', 'title' => 'Ergonomic Dental Operatory Room', 'category' => 'Operatory'],
    ['img' => 'images/gallery/l5.webp', 'title' => 'Pediatric Smile Studio', 'category' => 'Pediatric'],
    ['img' => 'images/gallery/l6.webp', 'title' => 'Advanced Laser Whitening Station', 'category' => 'Cosmetic'],
    ['img' => 'images/gallery/l7.webp', 'title' => 'Precision Dental Microscopy', 'category' => 'Technology'],
    ['img' => 'images/gallery/l8.webp', 'title' => 'Patient Relaxation Suite', 'category' => 'Facilities'],
    ['img' => 'images/gallery/l9.webp', 'title' => 'Orthodontic 3D Scan Bar', 'category' => 'Technology'],
];
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <!-- Subheader -->
    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Virtual Tour</span>
                    <h1 class="fw-bold mb-2">Clinic Facilities & Technology</h1>
                    <div class="border-bottom my-3 mx-auto" style="width: 60px; border-width: 3px !important; border-color: var(--dentia-primary) !important;"></div>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold">Facilities Tour</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Grid Section -->
    <section class="py-5 bg-light">
        <div class="container py-3">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <span class="section-subtitle-modern">Designed For Comfort</span>
                    <h2 class="fw-bold mt-2">A Welcoming, High-Tech Environment</h2>
                    <p class="text-muted">Take a visual tour of our modern New York clinic featuring state-of-the-art diagnostic instruments and relaxing treatment rooms.</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($galleryItems as $item): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 rounded-4 overflow-hidden shadow-sm h-100 bg-white">
                            <div class="position-relative overflow-hidden">
                                <img src="<?= e($item['img']) ?>" alt="<?= e($item['title']) ?>" class="card-img-top object-fit-cover" style="height: 250px; transition: transform 0.4s ease;">
                                <span class="badge bg-dark bg-opacity-75 position-absolute top-0 end-0 m-3 rounded-pill px-3 py-1 small">
                                    <?= e($item['category']) ?>
                                </span>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-0 text-dark"><?= e($item['title']) ?></h6>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-5 pt-3">
                <a href="booking.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow">
                    <i class="fa fa-calendar-check-o me-2"></i> Book an In-Person Consultation
                </a>
            </div>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


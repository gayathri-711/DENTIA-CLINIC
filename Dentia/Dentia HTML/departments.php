<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Comprehensive 12 Departments Directory
 */
$pageTitle = "Medical Departments & Centres of Excellence";
require_once __DIR__ . '/includes/header.php';
$departments = get_departments(true);
?>

<!-- Breadcrumb Header -->
<div class="py-5 bg-light border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Tertiary Care Specialities</span>
                <h1 class="fw-bold text-primary mb-2">Our Medical Departments</h1>
                <p class="text-muted mb-0">Delivering multidisciplinary clinical excellence with 12 world-class specialized divisions, state-of-the-art diagnostic suites, and compassionate patient care.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <a href="booking.php" class="btn btn-med-primary btn-lg"><i class="fa fa-calendar-check me-2"></i> Book an Appointment</a>
            </div>
        </div>
    </div>
</div>

<!-- Department Grid Section -->
<div class="py-5">
    <div class="container">
        <!-- Live Search Bar -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-white border-0 ps-4"><i class="fa fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="deptSearchInput" class="form-control border-0 ps-2" placeholder="Search departments, clinical conditions, or procedures (e.g. Cardiology, MRI, Ortho)...">
                    <button class="btn btn-med-secondary px-4 fw-bold" type="button">Search</button>
                </div>
            </div>
        </div>

        <div class="row g-4" id="departmentsContainer">
            <?php foreach ($departments as $dept): ?>
                <div class="col-lg-4 col-md-6 dept-card-item">
                    <div class="med-card d-flex flex-column p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="med-card-icon">
                                <i class="fa <?= e($dept['icon']) ?>"></i>
                            </div>
                            <span class="badge bg-light text-primary border px-3 py-2 fw-bold"><?= e($dept['code']) ?></span>
                        </div>
                        <h4 class="fw-bold mb-2">
                            <a href="department-details.php?slug=<?= e($dept['slug']) ?>" class="text-dark text-decoration-none hover-teal">
                                <?= e($dept['name']) ?>
                            </a>
                        </h4>
                        <p class="text-muted flex-grow-1 mb-4"><?= e($dept['short_desc']) ?></p>
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="department-details.php?slug=<?= e($dept['slug']) ?>" class="text-primary fw-bold text-decoration-none">
                                Department Overview <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                            <a href="booking.php?department_id=<?= e($dept['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Book Doctor
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('deptSearchInput')?.addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let cards = document.querySelectorAll('.dept-card-item');
    cards.forEach(card => {
        let text = card.textContent.toLowerCase();
        card.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


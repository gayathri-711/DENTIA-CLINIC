<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Single Health Blog Article
 */
$pageTitle = "Medical Health Article";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-light border-bottom">
    <div class="container" style="max-width: 860px;">
        <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Cardiology &bull; Clinical Guide</span>
        <h1 class="fw-bold text-primary mb-3">Early Warning Signs of Heart Disease You Shouldn’t Ignore</h1>
        <div class="d-flex align-items-center gap-3 text-muted small">
            <span>By <strong>Dr. Marcus Vance</strong> (MD, FACC)</span>
            <span>&bull;</span>
            <span>Published October 15, 2026</span>
            <span>&bull;</span>
            <span>5 Min Read</span>
        </div>
    </div>
</div>

<div class="py-5">
    <div class="container" style="max-width: 860px;">
        <img src="images/blog/l1.webp" class="img-fluid rounded-4 shadow-sm w-100 mb-4" alt="Heart Disease Guidance" style="max-height: 420px; object-fit: cover;">
        
        <div class="fs-5 lh-lg text-muted mb-4">
            <p>Cardiovascular disease remains the leading cause of mortality worldwide. Yet, with contemporary risk stratification and early therapeutic intervention, up to 80% of premature cardiac events are preventable.</p>
            
            <h3 class="fw-bold text-primary mt-4 mb-3">1. Exertional Angina and Chest Pressure</h3>
            <p>Unlike movie depictions of crushing chest pain, myocardial ischemia frequently presents as a subtle heaviness, tightness, or burning sensation centered beneath the sternum. It often radiates to the left shoulder, neck, or jaw and typically subsides with resting.</p>

            <h3 class="fw-bold text-primary mt-4 mb-3">2. Unexplained Breathlessness</h3>
            <p>If routine activities such as climbing a single flight of stairs or walking a short distance induce unexpected shortness of breath, it may indicate reduced cardiac output or left ventricular diastolic dysfunction.</p>

            <h3 class="fw-bold text-primary mt-4 mb-3">3. When to Seek Emergency Evaluation</h3>
            <p>Any sudden chest discomfort accompanied by diaphoresis (cold sweats), lightheadedness, or nausea requires immediate emergency evaluation. Contact our 24/7 Level-I Emergency Cardiac Team directly without delay.</p>
        </div>

        <div class="p-4 bg-light rounded-4 border d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-primary mb-1">Concerned About Your Heart Health?</h5>
                <p class="text-muted small mb-0">Consult Dr. Marcus Vance and our cardiology faculty today.</p>
            </div>
            <a href="booking.php?department_id=1" class="btn btn-med-primary px-4 fw-bold">Book Cardiac Checkup</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

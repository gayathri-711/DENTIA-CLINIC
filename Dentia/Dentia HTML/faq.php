<?php
/**
 * Doctor Dental Clinic Dentia - Frequently Asked Questions (FAQ)
 */
$pageTitle = "Frequently Asked Questions";
require_once __DIR__ . '/includes/header.php';

$faqs = get_all_faqs();
?>

<div class="no-bottom no-top" id="content">
    <div id="top"></div>

    <!-- Subheader -->
    <section id="subheader" class="bg-color-op-1 text-center py-5" style="background: linear-gradient(135deg, #e8f2fc 0%, #d4e8fa 100%);">
        <div class="container relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Patient Knowledgebase</span>
                    <h1 class="fw-bold mb-2">Frequently Asked Questions</h1>
                    <div class="border-bottom my-3 mx-auto" style="width: 60px; border-width: 3px !important; border-color: var(--dentia-primary) !important;"></div>
                    <ul class="crumb list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="list-inline-item">/</li>
                        <li class="list-inline-item active text-primary fw-bold">FAQ</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion Section -->
    <section class="py-5 bg-light">
        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <div class="text-center mb-5">
                        <span class="section-subtitle-modern">Clear Answers</span>
                        <h2 class="fw-bold mt-2">Everything You Need To Know</h2>
                        <p class="text-muted">Find quick answers to common questions about appointments, procedures, anesthesia, and insurance.</p>
                    </div>

                    <div class="accordion accordion-flush bg-white rounded-4 shadow-sm p-4" id="dentiaFaqAccordion">
                        <?php foreach ($faqs as $idx => $f): ?>
                            <div class="accordion-item border-0 mb-3 border-bottom pb-3">
                                <h2 class="accordion-header" id="faqHeading<?= $idx ?>">
                                    <button class="accordion-button <?= ($idx === 0) ? '' : 'collapsed' ?> bg-transparent fw-bold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse<?= $idx ?>" aria-expanded="<?= ($idx === 0) ? 'true' : 'false' ?>">
                                        <i class="fa fa-question-circle text-primary me-2"></i> <?= e($f['question']) ?>
                                    </button>
                                </h2>
                                <div id="faqCollapse<?= $idx ?>" class="accordion-collapse collapse <?= ($idx === 0) ? 'show' : '' ?>" data-bs-parent="#dentiaFaqAccordion">
                                    <div class="accordion-body text-muted ps-4" style="line-height: 1.8;">
                                        <?= e($f['answer']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Still Have Questions Banner -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-center mt-5 bg-white">
                        <h4 class="fw-bold mb-2">Still Have Questions About Your Dental Visit?</h4>
                        <p class="text-muted small mb-4">Our friendly dental care coordinators are here to assist you anytime.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="contact.php" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="fa fa-envelope-o me-2"></i> Contact Reception
                            </a>
                            <a href="booking.php" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fa fa-calendar-check-o me-2"></i> Book An Appointment
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


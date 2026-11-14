<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Admin Manage Hospital Hours & Configuration
 */
$adminPageTitle = "Hospital Working Hours & Settings";
require_once __DIR__ . '/header.php';

global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $keys = [
            'clinic_name',
            'tagline',
            'phone',
            'emergency_phone',
            'email',
            'address',
            'opening_hours_weekday',
            'opening_hours_weekend',
            'google_map_embed'
        ];

        if ($pdo) {
            foreach ($keys as $k) {
                if (isset($_POST[$k])) {
                    $val = sanitize_input($_POST[$k]);
                    $stmt = $pdo->prepare("INSERT INTO clinic_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$k, $val, $val]);
                }
            }
            set_flash_message('success', "Clinic settings and working hours saved successfully.");
        }
    }
    header("Location: settings.php");
    exit;
}

$settings = get_clinic_settings();
?>

<div class="mb-4">
    <h4 class="fw-bold mb-0 text-dark">Clinic Configuration & Schedule</h4>
    <small class="text-muted">Configure clinic working hours, emergency hotlines, address, and metadata</small>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white p-4 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-clock-o text-primary me-2"></i> Working Hours & Practice Schedule</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="settings.php">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Weekday Hours (Monday - Friday) *</label>
                            <input type="text" name="opening_hours_weekday" class="form-control rounded-3" value="<?= e($settings['opening_hours_weekday']) ?>" required>
                            <div class="form-text small" style="font-size:11px;">Shown on website topbar, footer, and booking page.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Weekend Hours (Saturday - Sunday) *</label>
                            <input type="text" name="opening_hours_weekend" class="form-control rounded-3" value="<?= e($settings['opening_hours_weekend']) ?>" required>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-top pt-4 text-dark"><i class="fa fa-building-o text-primary me-2"></i> Clinic Information & Contact</h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Clinic Name *</label>
                            <input type="text" name="clinic_name" class="form-control rounded-3" value="<?= e($settings['clinic_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tagline</label>
                            <input type="text" name="tagline" class="form-control rounded-3" value="<?= e($settings['tagline']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Primary Phone</label>
                            <input type="text" name="phone" class="form-control rounded-3" value="<?= e($settings['phone']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-danger">24/7 Emergency Dental Hotline *</label>
                            <input type="text" name="emergency_phone" class="form-control rounded-3 border-danger-subtle" value="<?= e($settings['emergency_phone']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Notification Email</label>
                            <input type="email" name="email" class="form-control rounded-3" value="<?= e($settings['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Physical Clinic Address</label>
                            <input type="text" name="address" class="form-control rounded-3" value="<?= e($settings['address']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Google Maps Embed URL</label>
                            <input type="text" name="google_map_embed" class="form-control rounded-3" value="<?= e($settings['google_map_embed']) ?>">
                        </div>
                    </div>

                    <div class="text-end border-top pt-3">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                            <i class="fa fa-save me-2"></i> Save All Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-primary text-white p-4 mb-4">
            <h5 class="fw-bold text-white mb-2"><i class="fa fa-info-circle me-2"></i> Schedule Preview</h5>
            <p class="small text-white-50 mb-3">Live hours rendered across patient appointment picker:</p>
            <div class="bg-white text-dark p-3 rounded-3 small">
                <div class="mb-2"><strong>Weekdays:</strong><br><?= e($settings['opening_hours_weekday']) ?></div>
                <div class="mb-2"><strong>Weekends:</strong><br><?= e($settings['opening_hours_weekend']) ?></div>
                <div><strong>Emergency:</strong><br><span class="text-danger fw-bold"><?= e($settings['emergency_phone']) ?></span></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white p-4">
            <h6 class="fw-bold mb-2"><i class="fa fa-database text-primary me-2"></i> Quick Maintenance</h6>
            <p class="small text-muted mb-3">Reset demo data or re-run database setup wizard.</p>
            <a href="../install.php" target="_blank" class="btn btn-outline-secondary rounded-pill w-100 btn-sm">
                Open Database Installer
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>


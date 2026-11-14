<?php
/**
 * Doctor Dental Clinic Dentia - Admin View Contact Inquiries
 */
$adminPageTitle = "Contact Inquiries & Messages";
require_once __DIR__ . '/header.php';

global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $action = $_POST['action'] ?? '';

        if ($action === 'mark_read') {
            $msgId = intval($_POST['message_id'] ?? 0);
            if ($pdo && $msgId > 0) {
                $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
                $stmt->execute([$msgId]);
                set_flash_message('success', "Message marked as read.");
            }
        } elseif ($action === 'delete_message') {
            $msgId = intval($_POST['message_id'] ?? 0);
            if ($pdo && $msgId > 0) {
                $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
                $stmt->execute([$msgId]);
                set_flash_message('success', "Message inquiry deleted.");
            }
        }
    }
    header("Location: messages.php");
    exit;
}

$messages = [];
if ($pdo) {
    try {
        $messages = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC")->fetchAll();
    } catch (Exception $e) {}
}
?>

<div class="mb-4">
    <h4 class="fw-bold mb-0 text-dark">Patient Inquiries & Contact Submissions</h4>
    <small class="text-muted">Messages sent via the public website contact form</small>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="card-body p-0">
        <?php if (empty($messages)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa fa-envelope-open-o fs-1 mb-2"></i>
                <p class="mb-0">No contact messages received yet.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Status</th>
                            <th>Sender</th>
                            <th>Subject & Content</th>
                            <th>Date Received</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $m): ?>
                            <tr class="<?= $m['is_read'] ? '' : 'table-light fw-semibold' ?>">
                                <td>
                                    <span class="badge <?= $m['is_read'] ? 'bg-secondary' : 'bg-danger' ?> rounded-pill px-3 py-1">
                                        <?= $m['is_read'] ? 'Read' : 'New' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($m['name']) ?></div>
                                    <div class="small text-muted"><i class="fa fa-envelope-o me-1"></i><?= e($m['email']) ?></div>
                                    <?php if (!empty($m['phone'])): ?>
                                        <div class="small text-muted"><i class="fa fa-phone me-1"></i><?= e($m['phone']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-1"><?= e($m['subject'] ?: 'Inquiry') ?></div>
                                    <p class="small text-muted mb-0" style="max-width: 450px; line-height: 1.5;">
                                        <?= nl2br(e($m['message'])) ?>
                                    </p>
                                </td>
                                <td class="small text-muted">
                                    <?= e($m['created_at']) ?>
                                </td>
                                <td class="text-end">
                                    <a href="mailto:<?= e($m['email']) ?>?subject=Re: <?= urlencode($m['subject']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" title="Reply via Email">
                                        <i class="fa fa-reply me-1"></i> Reply
                                    </a>

                                    <?php if (!$m['is_read']): ?>
                                        <form method="POST" action="messages.php" class="d-inline-block">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="mark_read">
                                            <input type="hidden" name="message_id" value="<?= $m['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 me-1" title="Mark as Read">
                                                <i class="fa fa-check"></i> Read
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST" action="messages.php" class="d-inline-block" onsubmit="return confirm('Delete message from <?= e($m['name']) ?>?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete_message">
                                        <input type="hidden" name="message_id" value="<?= $m['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete" style="width:32px; height:32px; line-height: 1;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>


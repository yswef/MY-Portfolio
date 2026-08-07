<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';

start_secure_session();
require_login();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $id = (int) ($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'mark_read') {
        $pdo->prepare('UPDATE messages SET is_read = 1 WHERE id = :id')->execute(['id' => $id]);
    } elseif (($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare('DELETE FROM messages WHERE id = :id')->execute(['id' => $id]);
    }
}

$messages = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();

$activePage = 'messages';
$pageTitle = 'الرسائل | لوحة التحكم';
require __DIR__ . '/includes/layout_top.php';
?>

<h1>رسائل التواصل</h1>

<table>
    <thead><tr><th>الاسم</th><th>البريد</th><th>الرسالة</th><th>التاريخ</th><th>إجراءات</th></tr></thead>
    <tbody>
        <?php if (!$messages): ?>
            <tr><td colspan="5" style="text-align:center; color:var(--muted);">لا توجد رسائل بعد.</td></tr>
        <?php endif; ?>
        <?php foreach ($messages as $m): ?>
        <tr class="<?= $m['is_read'] ? '' : 'unread' ?>">
            <td><?= e($m['name']) ?></td>
            <td><?= e($m['email']) ?></td>
            <td style="max-width:300px;"><?= nl2br(e($m['message'])) ?></td>
            <td><?= e($m['created_at']) ?></td>
            <td style="white-space:nowrap;">
                <?php if (!$m['is_read']): ?>
                <form method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="mark_read">
                    <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                    <button type="submit" class="btn btn-secondary" style="padding:6px 10px; font-size:0.75rem;">تمت القراءة</button>
                </form>
                <?php endif; ?>
                <form method="post" style="display:inline;" class="js-confirm-delete" data-confirm="حذف الرسالة؟">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                    <button type="submit" class="btn btn-danger" style="padding:6px 10px; font-size:0.75rem;">حذف</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>

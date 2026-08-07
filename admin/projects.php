<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';

start_secure_session();
require_login();

$pdo = db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (csrf_verify()) {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT image FROM projects WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if ($row) {
            delete_uploaded_image($row['image']);
            $pdo->prepare('DELETE FROM projects WHERE id = :id')->execute(['id' => $id]);
            $message = 'تم حذف المشروع.';
        }
    }
}

$projects = $pdo->query('SELECT * FROM projects ORDER BY sort_order ASC, id DESC')->fetchAll();

$activePage = 'projects';
$pageTitle = 'المشاريع | لوحة التحكم';
require __DIR__ . '/includes/layout_top.php';
?>

<div class="top-bar">
    <h1>المشاريع</h1>
    <a href="project_form.php" class="btn btn-primary">+ إضافة مشروع</a>
</div>

<?php if ($message): ?><div class="alert-success"><?= e($message) ?></div><?php endif; ?>

<table>
    <thead>
        <tr><th>الصورة</th><th>العنوان</th><th>الحالة</th><th>الترتيب</th><th>إجراءات</th></tr>
    </thead>
    <tbody>
        <?php if (!$projects): ?>
            <tr><td colspan="5" style="text-align:center; color:var(--muted);">لا توجد مشاريع بعد.</td></tr>
        <?php endif; ?>
        <?php foreach ($projects as $p): ?>
        <tr>
            <td>
                <?php if ($p['image']): ?>
                    <img class="thumb" src="../assets/img/uploads/<?= e($p['image']) ?>" alt="">
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
            <td><?= e($p['title_ar']) ?></td>
            <td><?= $p['is_published'] ? '✅ منشور' : '🚫 مخفي' ?></td>
            <td><?= (int) $p['sort_order'] ?></td>
            <td style="white-space:nowrap;">
                <a href="project_form.php?id=<?= (int) $p['id'] ?>" class="btn btn-secondary" style="padding:6px 12px; font-size:0.8rem;">تعديل</a>
                <form method="post" style="display:inline;" class="js-confirm-delete" data-confirm="متأكد من الحذف؟">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button type="submit" class="btn btn-danger" style="padding:6px 12px; font-size:0.8rem;">حذف</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>

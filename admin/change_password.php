<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';

start_secure_session();
require_login();

$pdo = db();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'الجلسة انتهت، أعد المحاولة.';
    } else {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE id = :id');
        $stmt->execute(['id' => $_SESSION['admin_id']]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($current, $user['password_hash'])) {
            $error = 'كلمة المرور الحالية غير صحيحة.';
        } elseif (strlen($new) < 10) {
            $error = 'كلمة المرور الجديدة يجب أن تكون 10 أحرف على الأقل.';
        } elseif ($new !== $confirm) {
            $error = 'كلمتا المرور الجديدتان غير متطابقتين.';
        } else {
            $pdo->prepare('UPDATE admin_users SET password_hash = :h WHERE id = :id')
                ->execute(['h' => password_hash($new, PASSWORD_DEFAULT), 'id' => $_SESSION['admin_id']]);
            $success = 'تم تغيير كلمة المرور بنجاح.';
        }
    }
}

$activePage = 'password';
$pageTitle = 'تغيير كلمة المرور | لوحة التحكم';
require __DIR__ . '/includes/layout_top.php';
?>

<h1>تغيير كلمة المرور</h1>

<?php if ($success): ?><div class="alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert-error"><?= e($error) ?></div><?php endif; ?>

<form method="post" class="form-card">
    <?= csrf_field() ?>
    <label>كلمة المرور الحالية</label>
    <input type="password" name="current_password" required>
    <label>كلمة المرور الجديدة (10 أحرف على الأقل)</label>
    <input type="password" name="new_password" required minlength="10">
    <label>تأكيد كلمة المرور الجديدة</label>
    <input type="password" name="confirm_password" required minlength="10">
    <div class="actions-row">
        <button type="submit" class="btn btn-primary">تحديث</button>
    </div>
</form>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>

<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = db();
$existingCount = (int) $pdo->query('SELECT COUNT(*) AS c FROM admin_users')->fetch()['c'];

$error = '';
$done = false;

if ($existingCount > 0) {
    $error = 'يوجد حساب مدير مُسجَّل بالفعل. لأسباب أمنية، احذف ملف setup.php فورًا من الاستضافة الآن.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (strlen($username) < 3) {
        $error = 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل.';
    } elseif (strlen($password) < 10) {
        $error = 'كلمة المرور يجب أن تكون 10 أحرف على الأقل.';
    } elseif ($password !== $confirm) {
        $error = 'كلمتا المرور غير متطابقتين.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (:u, :p)');
        $stmt->execute(['u' => $username, 'p' => password_hash($password, PASSWORD_DEFAULT)]);
        $done = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>تهيئة لوحة التحكم</title>
<meta name="robots" content="noindex, nofollow">
<style>
    body { font-family: Tahoma, sans-serif; background:#0d0d14; color:#f2efe4; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
    .box { background:#17171f; border:1px solid #2c2c3a; border-radius:14px; padding:32px; max-width:420px; width:90%; }
    h1 { color:#e8b923; font-size:1.3rem; }
    input { width:100%; padding:10px; margin:8px 0; border-radius:8px; border:1px solid #2c2c3a; background:#0d0d14; color:#fff; box-sizing:border-box; }
    button { width:100%; padding:12px; background:#e8b923; color:#0d0d14; border:none; border-radius:8px; font-weight:bold; cursor:pointer; margin-top:10px; }
    .error { background:#3a1414; border:1px solid #7a2020; color:#ffb3b3; padding:12px; border-radius:8px; margin-bottom:14px; }
    .success { background:#12321c; border:1px solid #1f6b3a; color:#a8f0c0; padding:12px; border-radius:8px; }
    .warn { background:#3a2f14; border:1px solid #7a5f20; color:#ffe4a3; padding:12px; border-radius:8px; margin-top:14px; font-size:0.9rem; }
</style>
</head>
<body>
<div class="box">
    <h1>🔧 تهيئة حساب المدير الأول</h1>

    <?php if ($error): ?>
        <div class="error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($done): ?>
        <div class="success">تم إنشاء الحساب بنجاح! يمكنك تسجيل الدخول الآن من admin/</div>
        <div class="warn">⚠️ احذف ملف <code>setup.php</code> من الاستضافة فورًا — تركه قد يسمح لأي شخص بإعادة تشغيله.</div>
    <?php elseif ($existingCount === 0): ?>
        <form method="post">
            <input type="text" name="username" placeholder="اسم المستخدم" required minlength="3" autocomplete="off">
            <input type="password" name="password" placeholder="كلمة المرور (10 أحرف على الأقل)" required minlength="10">
            <input type="password" name="confirm" placeholder="تأكيد كلمة المرور" required minlength="10">
            <button type="submit">إنشاء الحساب</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>

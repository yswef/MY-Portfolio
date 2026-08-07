<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';

start_secure_session();

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'الجلسة انتهت، حاول مرة أخرى.';
    } elseif (is_rate_limited()) {
        $error = 'محاولات كثيرة فاشلة. حاول لاحقًا بعد ' . LOGIN_WINDOW_MINUTES . ' دقيقة.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (attempt_login($username, $password)) {
            redirect('dashboard.php');
        }
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>تسجيل الدخول | لوحة التحكم</title>
<link rel="icon" href="../assets/img/favicon.ico">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="login-body">
    <form class="login-box" method="post" autocomplete="off">
        <h1>🔐 لوحة تحكم يوسف</h1>
        <?php if ($error): ?>
            <div class="alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <?= csrf_field() ?>
        <label>اسم المستخدم</label>
        <input type="text" name="username" required autofocus>
        <label>كلمة المرور</label>
        <input type="password" name="password" required>
        <button type="submit">دخول</button>
    </form>
</body>
</html>

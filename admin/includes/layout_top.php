<?php
/** @var string $activePage */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= e($pageTitle ?? 'لوحة التحكم') ?></title>
<link rel="icon" href="../assets/img/favicon.ico">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="brand">🔐 لوحة يوسف</div>
        <a href="dashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">الرئيسية</a>
        <a href="projects.php" class="<?= $activePage === 'projects' ? 'active' : '' ?>">المشاريع</a>
        <a href="messages.php" class="<?= $activePage === 'messages' ? 'active' : '' ?>">الرسائل</a>
        <a href="settings.php" class="<?= $activePage === 'settings' ? 'active' : '' ?>">إعدادات السيرة</a>
        <a href="change_password.php" class="<?= $activePage === 'password' ? 'active' : '' ?>">تغيير كلمة المرور</a>
        <a href="../index.php" target="_blank">↗ عرض الموقع</a>
        <a href="logout.php" style="color:#e85555;">خروج</a>
    </aside>
    <main class="admin-main">

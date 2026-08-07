<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

start_secure_session();
require_login();

$pdo = db();
$projectCount = (int) $pdo->query('SELECT COUNT(*) c FROM projects')->fetch()['c'];
$publishedCount = (int) $pdo->query('SELECT COUNT(*) c FROM projects WHERE is_published = 1')->fetch()['c'];
$messageCount = (int) $pdo->query('SELECT COUNT(*) c FROM messages')->fetch()['c'];
$unreadCount = (int) $pdo->query('SELECT COUNT(*) c FROM messages WHERE is_read = 0')->fetch()['c'];
$viewCount = (int) $pdo->query('SELECT COUNT(*) c FROM page_views')->fetch()['c'];

$activePage = 'dashboard';
$pageTitle = 'الرئيسية | لوحة التحكم';
require __DIR__ . '/includes/layout_top.php';
?>

<h1>مرحبًا، <?= e($_SESSION['admin_username']) ?> 👋</h1>

<div class="stat-grid">
    <div class="stat-card"><div class="num"><?= $projectCount ?></div><div class="label">إجمالي المشاريع</div></div>
    <div class="stat-card"><div class="num"><?= $publishedCount ?></div><div class="label">مشاريع منشورة</div></div>
    <div class="stat-card"><div class="num"><?= $messageCount ?></div><div class="label">إجمالي الرسائل</div></div>
    <div class="stat-card"><div class="num"><?= $unreadCount ?></div><div class="label">رسائل غير مقروءة</div></div>
    <div class="stat-card"><div class="num"><?= $viewCount ?></div><div class="label">مشاهدات الصفحة الرئيسية</div></div>
</div>

<p style="color:var(--muted); font-size:0.9rem;">
    من هنا تقدر تضيف/تعدّل مشاريعك، تشوف رسائل التواصل الواردة، وتعدّل نص السيرة الذاتية — كل هذا بينعكس مباشرة على الموقع بدون لمس أي كود.
</p>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>

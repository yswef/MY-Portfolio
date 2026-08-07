<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';

start_secure_session();
require_login();

$langs = ['ar' => 'العربية', 'en' => 'English', 'es' => 'Español', 'fr' => 'Français', 'ja' => '日本語'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'الجلسة انتهت، أعد المحاولة.';
    } else {
        foreach (array_keys($langs) as $l) {
            set_setting("bio_intro_$l", trim($_POST["bio_intro_$l"] ?? ''));
            set_setting("bio_dream_$l", trim($_POST["bio_dream_$l"] ?? ''));
        }
        $success = 'تم حفظ التغييرات.';
    }
}

$values = [];
foreach (array_keys($langs) as $l) {
    $values["bio_intro_$l"] = get_setting("bio_intro_$l");
    $values["bio_dream_$l"] = get_setting("bio_dream_$l");
}

$activePage = 'settings';
$pageTitle = 'إعدادات السيرة | لوحة التحكم';
require __DIR__ . '/includes/layout_top.php';
?>

<h1>إعدادات السيرة الذاتية</h1>
<p style="color:var(--muted); font-size:0.9rem;">هذا النص يظهر في قسم "عني" بالصفحة الرئيسية، لكل لغة على حدة.</p>

<?php if ($success): ?><div class="alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert-error"><?= e($error) ?></div><?php endif; ?>

<form method="post" class="form-card">
    <?= csrf_field() ?>

    <div class="lang-tabs">
        <?php foreach ($langs as $code => $label): ?>
            <button type="button" data-lang="<?= $code ?>" class="lang-tab-btn <?= $code === 'ar' ? 'active' : '' ?>"><?= e($label) ?></button>
        <?php endforeach; ?>
    </div>

    <?php foreach ($langs as $code => $label): ?>
    <div class="lang-panel <?= $code === 'ar' ? 'active' : '' ?>" data-lang-panel="<?= $code ?>">
        <label>الفقرة التعريفية (<?= e($label) ?>)</label>
        <textarea name="bio_intro_<?= $code ?>" rows="4"><?= e($values["bio_intro_$code"]) ?></textarea>
        <label>فقرة الشغف/الهدف (<?= e($label) ?>)</label>
        <textarea name="bio_dream_<?= $code ?>" rows="3"><?= e($values["bio_dream_$code"]) ?></textarea>
    </div>
    <?php endforeach; ?>

    <div class="actions-row">
        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
    </div>
</form>


<?php require __DIR__ . '/includes/layout_bottom.php'; ?>

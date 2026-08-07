<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';

start_secure_session();
require_login();

$pdo = db();
$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$isEdit = $id > 0;
$error = '';

$langs = ['ar' => 'العربية', 'en' => 'English', 'es' => 'Español', 'fr' => 'Français', 'ja' => '日本語'];

$project = [
    'title_ar' => '', 'title_en' => '', 'title_es' => '', 'title_fr' => '', 'title_ja' => '',
    'desc_ar' => '', 'desc_en' => '', 'desc_es' => '', 'desc_fr' => '', 'desc_ja' => '',
    'image' => '', 'github_url' => '', 'download_url' => '', 'sort_order' => 0, 'is_published' => 1,
];

if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $found = $stmt->fetch();
    if (!$found) {
        redirect('projects.php');
    }
    $project = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    if (!csrf_verify()) {
        $error = 'الجلسة انتهت، أعد المحاولة.';
    } else {
        foreach (array_keys($langs) as $l) {
            $project["title_$l"] = trim($_POST["title_$l"] ?? '');
            $project["desc_$l"] = trim($_POST["desc_$l"] ?? '');
        }
        $project['github_url'] = trim($_POST['github_url'] ?? '');
        $project['download_url'] = trim($_POST['download_url'] ?? '');
        $project['sort_order'] = (int) ($_POST['sort_order'] ?? 0);
        $project['is_published'] = isset($_POST['is_published']) ? 1 : 0;

        if ($project['title_ar'] === '' || $project['desc_ar'] === '') {
            $error = 'العنوان والوصف بالعربية مطلوبان على الأقل.';
        } else {
            if (!empty($_FILES['image']['name'])) {
                $uploaded = handle_image_upload($_FILES['image']);
                if ($uploaded === null) {
                    $error = 'فشل رفع الصورة — تأكد أنها JPG/PNG/WEBP بحجم أقل من 4 ميجابايت.';
                } else {
                    if ($isEdit) {
                        delete_uploaded_image($project['image']);
                    }
                    $project['image'] = $uploaded;
                }
            }

            if (!$error) {
                if ($isEdit) {
                    $sql = "UPDATE projects SET
                        title_ar=:title_ar, title_en=:title_en, title_es=:title_es, title_fr=:title_fr, title_ja=:title_ja,
                        desc_ar=:desc_ar, desc_en=:desc_en, desc_es=:desc_es, desc_fr=:desc_fr, desc_ja=:desc_ja,
                        image=:image, github_url=:github_url, download_url=:download_url,
                        sort_order=:sort_order, is_published=:is_published, updated_at=CURRENT_TIMESTAMP
                        WHERE id=:id";
                    $project['id'] = $id;
                } else {
                    $sql = "INSERT INTO projects
                        (title_ar, title_en, title_es, title_fr, title_ja, desc_ar, desc_en, desc_es, desc_fr, desc_ja,
                         image, github_url, download_url, sort_order, is_published)
                        VALUES (:title_ar, :title_en, :title_es, :title_fr, :title_ja, :desc_ar, :desc_en, :desc_es, :desc_fr, :desc_ja,
                         :image, :github_url, :download_url, :sort_order, :is_published)";
                }
                $stmt = $pdo->prepare($sql);
                $params = $project;
                unset($params['created_at'], $params['updated_at']);
                if (!$isEdit) unset($params['id']);
                $stmt->execute($params);
                redirect('projects.php');
            }
        }
    }
}

$activePage = 'projects';
$pageTitle = ($isEdit ? 'تعديل' : 'إضافة') . ' مشروع | لوحة التحكم';
require __DIR__ . '/includes/layout_top.php';
?>

<h1><?= $isEdit ? 'تعديل مشروع' : 'إضافة مشروع جديد' ?></h1>

<?php if ($error): ?><div class="alert-error"><?= e($error) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data" class="form-card">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>

    <div class="lang-tabs">
        <?php foreach ($langs as $code => $label): ?>
            <button type="button" data-lang="<?= $code ?>" class="lang-tab-btn <?= $code === 'ar' ? 'active' : '' ?>"><?= e($label) ?></button>
        <?php endforeach; ?>
    </div>

    <?php foreach ($langs as $code => $label): ?>
    <div class="lang-panel <?= $code === 'ar' ? 'active' : '' ?>" data-lang-panel="<?= $code ?>">
        <label>العنوان (<?= e($label) ?>)<?= $code === 'ar' ? ' *' : '' ?></label>
        <input type="text" name="title_<?= $code ?>" value="<?= e($project["title_$code"]) ?>" <?= $code === 'ar' ? 'required' : '' ?>>
        <label>الوصف (<?= e($label) ?>)<?= $code === 'ar' ? ' *' : '' ?></label>
        <textarea name="desc_<?= $code ?>" <?= $code === 'ar' ? 'required' : '' ?>><?= e($project["desc_$code"]) ?></textarea>
    </div>
    <?php endforeach; ?>

    <label>صورة المشروع <?= $project['image'] ? '(اترك فارغًا للإبقاء على الصورة الحالية)' : '' ?></label>
    <?php if ($project['image']): ?>
        <img class="thumb" style="width:120px;height:80px;margin-bottom:8px;" src="../assets/img/uploads/<?= e($project['image']) ?>" alt="">
    <?php endif; ?>
    <input type="file" name="image" accept="image/png, image/jpeg, image/webp">

    <label>رابط GitHub</label>
    <input type="url" name="github_url" value="<?= e($project['github_url']) ?>" placeholder="https://github.com/...">

    <label>رابط التحميل</label>
    <input type="url" name="download_url" value="<?= e($project['download_url']) ?>" placeholder="https://...">

    <label>ترتيب الظهور (رقم أصغر = يظهر أولًا)</label>
    <input type="number" name="sort_order" value="<?= (int) $project['sort_order'] ?>">

    <label style="display:flex; align-items:center; gap:8px; margin-top:14px;">
        <input type="checkbox" name="is_published" style="width:auto;" <?= $project['is_published'] ? 'checked' : '' ?>>
        منشور (يظهر في الموقع)
    </label>

    <div class="actions-row">
        <button type="submit" class="btn btn-primary">حفظ</button>
        <a href="projects.php" class="btn btn-secondary">إلغاء</a>
    </div>
</form>


<?php require __DIR__ . '/includes/layout_bottom.php'; ?>

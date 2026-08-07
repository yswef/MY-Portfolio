<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');

$lang = $_GET['lang'] ?? 'ar';
$allowedLangs = ['ar', 'en', 'es', 'fr', 'ja'];
if (!in_array($lang, $allowedLangs, true)) {
    $lang = 'ar';
}

$stmt = db()->prepare("
    SELECT id, title_$lang AS title, title_ar AS title_fallback,
           desc_$lang AS description, desc_ar AS description_fallback,
           image, github_url, download_url
    FROM projects
    WHERE is_published = 1
    ORDER BY sort_order ASC, id DESC
");
$stmt->execute();
$rows = $stmt->fetchAll();

$projects = array_map(function ($row) {
    return [
        'id' => (int) $row['id'],
        'title' => $row['title'] !== '' && $row['title'] !== null ? $row['title'] : $row['title_fallback'],
        'description' => $row['description'] !== '' && $row['description'] !== null ? $row['description'] : $row['description_fallback'],
        'image' => $row['image'] ? 'assets/img/uploads/' . $row['image'] : null,
        'github_url' => $row['github_url'] ?: null,
        'download_url' => $row['download_url'] ?: null,
    ];
}, $rows);

echo json_encode(['projects' => $projects], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

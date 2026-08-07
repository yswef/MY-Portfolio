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

$intro = get_setting("bio_intro_$lang");
$dream = get_setting("bio_dream_$lang");

if ($intro === '') $intro = get_setting('bio_intro_ar');
if ($dream === '') $dream = get_setting('bio_dream_ar');

echo json_encode(['intro' => $intro, 'dream' => $dream], JSON_UNESCAPED_UNICODE);

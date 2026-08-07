<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

function json_out(array $data, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['ok' => false, 'error' => 'invalid_method'], 405);
}

// Honeypot: حقل مخفي بالـCSS، أي بوت يملأه يُرفض تلقائيًا
if (!empty($_POST['website'])) {
    json_out(['ok' => true]); // نرد بنجاح وهمي حتى لا يعرف البوت أنه اكتُشف
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($name === '' || mb_strlen($name) > 100) {
    json_out(['ok' => false, 'error' => 'invalid_name'], 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
    json_out(['ok' => false, 'error' => 'invalid_email'], 422);
}
if ($message === '' || mb_strlen($message) > 3000) {
    json_out(['ok' => false, 'error' => 'invalid_message'], 422);
}

// تحديد المعدّل: 3 رسائل كحد أقصى كل 10 دقائق لكل IP
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$pdo = db();
$pdo->exec("
    CREATE TABLE IF NOT EXISTS contact_rate_limit (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ip_address TEXT NOT NULL,
        submitted_at TEXT DEFAULT CURRENT_TIMESTAMP
    )
");

$stmt = $pdo->prepare("SELECT COUNT(*) c FROM contact_rate_limit WHERE ip_address = :ip AND submitted_at >= datetime('now', '-10 minutes')");
$stmt->execute(['ip' => $ip]);
if ((int) $stmt->fetch()['c'] >= 3) {
    json_out(['ok' => false, 'error' => 'rate_limited'], 429);
}

$pdo->prepare('INSERT INTO messages (name, email, message) VALUES (:n, :e, :m)')
    ->execute(['n' => $name, 'e' => $email, 'm' => $message]);

$pdo->prepare('INSERT INTO contact_rate_limit (ip_address) VALUES (:ip)')->execute(['ip' => $ip]);

json_out(['ok' => true]);

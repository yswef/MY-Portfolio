<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('portfolio_admin_sid');
    session_start();
}

const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_WINDOW_MINUTES = 15;

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function is_rate_limited(): bool
{
    $stmt = db()->prepare(
        "SELECT COUNT(*) AS c FROM login_attempts
         WHERE ip_address = :ip AND attempted_at >= datetime('now', :window)"
    );
    $stmt->execute(['ip' => client_ip(), 'window' => '-' . LOGIN_WINDOW_MINUTES . ' minutes']);
    $row = $stmt->fetch();
    return ((int) $row['c']) >= LOGIN_MAX_ATTEMPTS;
}

function record_login_attempt(): void
{
    $stmt = db()->prepare('INSERT INTO login_attempts (ip_address) VALUES (:ip)');
    $stmt->execute(['ip' => client_ip()]);
}

function clear_login_attempts(): void
{
    $stmt = db()->prepare('DELETE FROM login_attempts WHERE ip_address = :ip');
    $stmt->execute(['ip' => client_ip()]);
}

function attempt_login(string $username, string $password): bool
{
    if (is_rate_limited()) {
        return false;
    }

    $stmt = db()->prepare('SELECT * FROM admin_users WHERE username = :u');
    $stmt->execute(['u' => $username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        record_login_attempt();
        return false;
    }

    clear_login_attempts();
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $user['id'];
    $_SESSION['admin_username'] = $user['username'];
    return true;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

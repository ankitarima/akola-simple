<?php
/**
 * Session bootstrap, login guard and CSRF helpers for the admin panel.
 */

require_once __DIR__ . '/../../backend/config.php';
require_once __DIR__ . '/../../backend/lib/helpers.php';
require_once __DIR__ . '/../../backend/lib/Database.php';

// Coolify terminates TLS at its proxy and forwards plain HTTP to the app,
// so detect HTTPS via the forwarded header too (falls back to local http dev).
$isHttps = (($_SERVER['HTTPS'] ?? '') === 'on')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

session_name(ADMIN_SESSION_NAME);
session_set_cookie_params([
    'lifetime' => ADMIN_SESSION_LIFETIME,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => $isHttps,
]);
session_start();

function is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
    // Idle timeout on top of the cookie lifetime.
    if (!empty($_SESSION['last_active']) && (time() - $_SESSION['last_active']) > ADMIN_SESSION_LIFETIME) {
        session_unset();
        session_destroy();
        header('Location: login.php?timeout=1');
        exit;
    }
    $_SESSION['last_active'] = time();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return is_string($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function admin_username(): string
{
    return $_SESSION['admin_username'] ?? 'Admin';
}

function too_many_login_attempts(): bool
{
    $attempts = $_SESSION['login_attempts'] ?? [];
    $recent = array_filter($attempts, fn($t) => $t > time() - 900); // last 15 minutes
    $_SESSION['login_attempts'] = array_values($recent);
    return count($recent) >= 6;
}

function record_failed_login(): void
{
    $_SESSION['login_attempts'][] = time();
}

function clear_login_attempts(): void
{
    $_SESSION['login_attempts'] = [];
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

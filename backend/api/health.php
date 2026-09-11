<?php
/**
 * Health check endpoint for uptime monitors and Coolify's health check
 * probe. Public, unauthenticated, and deliberately cheap: a raw DB ping
 * with a short timeout, not the full Database::connection() bootstrap
 * (which would re-check migrations/seed the admin on every probe hit).
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helpers.php';

$checks = ['app' => true];

try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3,
    ]);
    $pdo->query('SELECT 1');
    $checks['database'] = true;
} catch (Throwable $e) {
    $checks['database'] = false;
}

$healthy = !in_array(false, $checks, true);

json_response([
    'status' => $healthy ? 'ok' : 'error',
    'checks' => $checks,
    'time' => date('c'),
], $healthy ? 200 : 503);

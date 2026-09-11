<?php
/**
 * Central configuration for the app. Values here should come from
 * environment variables in every real deployment (Coolify); the
 * db_credentials.php fallback exists only for local development.
 */

// ---- App identity ----
define('APP_NAME', getenv('APP_NAME') ?: 'Akola Simple');
define('ADMIN_SESSION_NAME', 'app_admin_session');
define('ADMIN_SESSION_LIFETIME', 60 * 60 * 4); // 4 hours

// ---- Paths ----
define('BASE_DIR', dirname(__DIR__));
define('DATA_DIR', __DIR__ . '/data'); // private runtime data, e.g. the one-time admin password file

// ---- Database credentials ----
// In production (Coolify), these come from real environment variables set in
// the Coolify app's Environment Variables tab — never committed to git.
// For local development, db_credentials.php (gitignored) is used instead.
if (getenv('DB_HOST') !== false) {
    define('DB_HOST', getenv('DB_HOST'));
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_NAME', getenv('DB_NAME'));
    define('DB_USER', getenv('DB_USER'));
    define('DB_PASS', getenv('DB_PASS'));
} else {
    require_once __DIR__ . '/db_credentials.php';
}

// ---- Environment ----
error_reporting(E_ALL);
ini_set('display_errors', '0'); // never leak errors to API responses
date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'UTC');

if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0775, true);
}

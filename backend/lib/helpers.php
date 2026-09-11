<?php
/**
 * Small shared helpers for API endpoints and the admin panel.
 */

function json_response(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function read_json_body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function clean_str($value, int $maxLength = 500): string
{
    if (!is_string($value) && !is_numeric($value)) {
        return '';
    }
    $value = trim((string) $value);
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value); // strip control chars
    return mb_substr($value, 0, $maxLength);
}

function is_valid_email(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_valid_phone(string $phone): bool
{
    return (bool) preg_match('/^[0-9+\-() ]{7,20}$/', $phone);
}

/** Canonical form of a phone number for de-duplication, regardless of how it
 * was typed (+91, spaces, dashes, leading 0, etc). Keeps the last 10 digits. */
function normalize_mobile(string $mobile): string
{
    $digits = preg_replace('/\D+/', '', $mobile);
    return substr($digits, -10);
}

function client_ip(): string
{
    return clean_str($_SERVER['REMOTE_ADDR'] ?? '', 45);
}

/** Random short token for reference codes, order IDs, etc. e.g. generate_token('ORD') -> "ORD-A1B2C3D4". */
function generate_token(string $prefix = ''): string
{
    $token = strtoupper(bin2hex(random_bytes(4)));
    return $prefix !== '' ? "{$prefix}-{$token}" : $token;
}

/** Allow the API to be called from the static frontend served on the same origin. */
function apply_cors_headers(): void
{
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

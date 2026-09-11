<?php
/**
 * Example public API endpoint — the pattern to copy for any new form.
 * Validates input with backend/lib/helpers.php, writes to the table created
 * by database/migrations/0001_create_contact_messages.sql, and always
 * returns JSON. See AGENT.md for the "adding a new form" checklist.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/Database.php';

apply_cors_headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$input = read_json_body();

// Honeypot: legitimate users never fill this hidden field.
if (!empty($input['website'])) {
    json_response(['success' => true, 'message' => 'Message received.']);
}

$fields = [
    'full_name' => clean_str($input['full_name'] ?? '', 120),
    'email'     => clean_str($input['email'] ?? '', 180),
    'phone'     => clean_str($input['phone'] ?? '', 30),
    'message'   => clean_str($input['message'] ?? '', 2000),
];

$errors = [];
if ($fields['full_name'] === '' || mb_strlen($fields['full_name']) < 2) {
    $errors['full_name'] = 'Please enter your name.';
}
if (!is_valid_email($fields['email'])) {
    $errors['email'] = 'Please enter a valid email address.';
}
if ($fields['phone'] !== '' && !is_valid_phone($fields['phone'])) {
    $errors['phone'] = 'Please enter a valid phone number.';
}
if ($fields['message'] === '' || mb_strlen($fields['message']) < 5) {
    $errors['message'] = 'Please enter a message.';
}

if (!empty($errors)) {
    json_response(['success' => false, 'message' => 'Please correct the highlighted fields.', 'errors' => $errors], 422);
}

try {
    $pdo = Database::connection();
    $phoneNormalized = $fields['phone'] !== '' ? normalize_mobile($fields['phone']) : null;

    $stmt = $pdo->prepare('
        INSERT INTO contact_messages (full_name, email, phone, phone_normalized, message, ip_address)
        VALUES (:full_name, :email, :phone, :phone_normalized, :message, :ip_address)
    ');
    $stmt->execute([
        'full_name' => $fields['full_name'],
        'email' => $fields['email'],
        'phone' => $fields['phone'] ?: null,
        'phone_normalized' => $phoneNormalized,
        'message' => $fields['message'],
        'ip_address' => client_ip(),
    ]);

    json_response(['success' => true, 'message' => 'Thanks — we will get back to you soon.']);
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => 'Something went wrong. Please try again later.'], 500);
}

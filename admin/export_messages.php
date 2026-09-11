<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = Database::connection();
$q = clean_str($_GET['q'] ?? '', 120);

$where = '';
$params = [];
if ($q !== '') {
    $where = 'WHERE full_name LIKE :q OR email LIKE :q OR message LIKE :q';
    $params[':q'] = "%{$q}%";
}

$stmt = $pdo->prepare("SELECT * FROM contact_messages {$where} ORDER BY created_at DESC");
$stmt->execute($params);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="messages_' . date('Ymd_His') . '.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Full Name', 'Email', 'Phone', 'Message', 'Received At']);

foreach ($stmt as $m) {
    fputcsv($out, [$m['full_name'], $m['email'], $m['phone'], $m['message'], $m['created_at']]);
}
fclose($out);
exit;

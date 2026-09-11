<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = Database::connection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !verify_csrf($_POST['csrf_token'] ?? null)) {
    $error = 'Your session expired. Please try again.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = (string) ($_POST['current_password'] ?? '');
    $new = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE id = ?');
    $stmt->execute([$_SESSION['admin_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current, $user['password_hash'])) {
        $error = 'Current password is incorrect.';
    } elseif (mb_strlen($new) < 8) {
        $error = 'New password must be at least 8 characters long.';
    } elseif ($new !== $confirm) {
        $error = 'New password and confirmation do not match.';
    } else {
        $stmt = $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?');
        $stmt->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);
        $success = 'Password updated successfully.';
    }
}

$initialPasswordFile = DATA_DIR . '/INITIAL_ADMIN_PASSWORD.txt';
$hasInitialFile = file_exists($initialPasswordFile);

$pageTitle = 'Settings';
$activeNav = 'settings';
require __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

<?php if ($hasInitialFile): ?>
<div class="panel">
  <div class="panel-head"><h3>⚠ Initial Password File Present</h3></div>
  <div class="panel-body">
    <p>A one-time file <code>backend/data/INITIAL_ADMIN_PASSWORD.txt</code> still exists on the server with your first generated password. After changing your password below, delete that file from the server.</p>
  </div>
</div>
<?php endif; ?>

<div class="panel" style="max-width:480px;">
  <div class="panel-head"><h3>Change Password</h3></div>
  <div class="panel-body">
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
      <div class="field-group" style="margin-bottom:14px;">
        <label for="current_password">Current Password</label>
        <input type="password" id="current_password" name="current_password" required style="width:100%; padding:11px 14px; border-radius:8px; border:1.5px solid var(--border); font-family:inherit;" />
      </div>
      <div class="field-group" style="margin-bottom:14px;">
        <label for="new_password">New Password (min. 8 characters)</label>
        <input type="password" id="new_password" name="new_password" required minlength="8" style="width:100%; padding:11px 14px; border-radius:8px; border:1.5px solid var(--border); font-family:inherit;" />
      </div>
      <div class="field-group" style="margin-bottom:18px;">
        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="8" style="width:100%; padding:11px 14px; border-radius:8px; border:1.5px solid var(--border); font-family:inherit;" />
      </div>
      <button type="submit" class="btn btn-primary btn-block">Update Password</button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>

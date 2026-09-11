<?php
/**
 * Admin user management — add/list/delete admin accounts. Self-delete and
 * last-admin-standing are both blocked so the panel can never be locked out.
 */
require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = Database::connection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $username = clean_str($_POST['username'] ?? '', 60);
            $password = (string) ($_POST['password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');

            if (!preg_match('/^[a-zA-Z0-9_.-]{3,60}$/', $username)) {
                $error = 'Username must be 3–60 characters: letters, numbers, dot, underscore, or dash only.';
            } elseif (mb_strlen($password) < 8) {
                $error = 'Password must be at least 8 characters long.';
            } elseif ($password !== $confirm) {
                $error = 'Password and confirmation do not match.';
            } else {
                $stmt = $pdo->prepare('SELECT 1 FROM admin_users WHERE username = ?');
                $stmt->execute([$username]);
                if ($stmt->fetchColumn()) {
                    $error = 'That username is already taken.';
                } else {
                    $stmt = $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
                    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
                    $success = "Admin user \"{$username}\" created.";
                }
            }
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            $total = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();

            if ($id === (int) $_SESSION['admin_id']) {
                $error = 'You cannot delete the account you are currently logged in as.';
            } elseif ($total <= 1) {
                $error = 'Cannot delete the last remaining admin user.';
            } else {
                $stmt = $pdo->prepare('DELETE FROM admin_users WHERE id = ?');
                $stmt->execute([$id]);
                $success = 'Admin user deleted.';
            }
        }
    }
}

$admins = $pdo->query('SELECT id, username, created_at FROM admin_users ORDER BY created_at ASC')->fetchAll();

$pageTitle = 'Admin Users';
$activeNav = 'users';
require __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

<div class="dashboard-grid">
  <div class="panel">
    <div class="panel-head"><h3>Admin Users (<?= count($admins) ?>)</h3></div>
    <div class="panel-body table-scroll">
      <table class="data-table">
        <thead><tr><th>Username</th><th>Created</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($admins as $a): ?>
          <tr>
            <td class="cell-strong"><?= e($a['username']) ?><?php if ((int) $a['id'] === (int) $_SESSION['admin_id']): ?> <span class="badge badge-blue">You</span><?php endif; ?></td>
            <td class="cell-muted"><?= e(date('d M Y', strtotime($a['created_at']))) ?></td>
            <td>
              <?php if ((int) $a['id'] !== (int) $_SESSION['admin_id'] && count($admins) > 1): ?>
              <form method="post" class="delete-form">
                <input type="hidden" name="action" value="delete" />
                <input type="hidden" name="id" value="<?= $a['id'] ?>" />
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Add Admin User</h3></div>
    <div class="panel-body">
      <form method="post">
        <input type="hidden" name="action" value="add" />
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
        <div class="field-group" style="margin-bottom:14px;">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required minlength="3" maxlength="60" style="width:100%; padding:11px 14px; border-radius:8px; border:1.5px solid var(--border); font-family:inherit;" />
        </div>
        <div class="field-group" style="margin-bottom:14px;">
          <label for="password">Password (min. 8 characters)</label>
          <input type="password" id="password" name="password" required minlength="8" style="width:100%; padding:11px 14px; border-radius:8px; border:1.5px solid var(--border); font-family:inherit;" />
        </div>
        <div class="field-group" style="margin-bottom:18px;">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required minlength="8" style="width:100%; padding:11px 14px; border-radius:8px; border:1.5px solid var(--border); font-family:inherit;" />
        </div>
        <button type="submit" class="btn btn-primary btn-block">Add Admin User</button>
      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (too_many_login_attempts()) {
        $error = 'Too many failed attempts. Please wait 15 minutes before trying again.';
    } elseif (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } else {
        $username = clean_str($_POST['username'] ?? '', 60);
        $password = (string) ($_POST['password'] ?? '');

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['last_active'] = time();
            clear_login_attempts();
            header('Location: index.php');
            exit;
        }

        record_failed_login();
        $error = 'Invalid username or password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Admin Login | <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="assets/admin.css" />
</head>
<body class="login-body">
  <div class="login-card">
    <div class="login-brand"><?= e(APP_NAME) ?></div>
    <h1>Admin Panel</h1>
    <p class="login-sub">Sign in to manage your site's data.</p>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['timeout'])): ?>
      <div class="alert alert-error">Your session timed out. Please sign in again.</div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
      <div class="field-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus />
      </div>
      <div class="field-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>
      <button type="submit" class="btn btn-primary btn-block">Sign In</button>
    </form>
    <p class="login-hint">First time here? Check <code>backend/data/INITIAL_ADMIN_PASSWORD.txt</code> on the server for your generated credentials, then change your password immediately.</p>
  </div>
</body>
</html>

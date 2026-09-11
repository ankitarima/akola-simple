<?php
/**
 * Shared admin layout header. Expects require_login() already called and
 * $pageTitle / $activeNav set by the including page.
 */
$activeNav = $activeNav ?? '';
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?> Admin</title>
<link rel="stylesheet" href="assets/admin.css" />
</head>
<body>
<div class="admin-shell">
  <aside class="admin-sidebar">
    <div class="sidebar-brand">
      <?= e(APP_NAME) ?>
    </div>
    <nav class="sidebar-nav">
      <a href="index.php" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>"><span class="nav-icon">▦</span> Dashboard</a>
      <a href="messages.php" class="<?= $activeNav === 'messages' ? 'active' : '' ?>"><span class="nav-icon">💬</span> Messages</a>
      <a href="users.php" class="<?= $activeNav === 'users' ? 'active' : '' ?>"><span class="nav-icon">👥</span> Admin Users</a>
      <a href="settings.php" class="<?= $activeNav === 'settings' ? 'active' : '' ?>"><span class="nav-icon">⚙</span> Settings</a>
    </nav>
    <div class="sidebar-footer">
      <a href="../index.html" target="_blank" class="view-site-link">↗ View Public Site</a>
    </div>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <h1><?= e($pageTitle) ?></h1>
      <div class="topbar-actions">
        <span class="admin-user">👤 <?= e(admin_username()) ?></span>
        <a href="logout.php" class="btn btn-outline btn-sm">Log Out</a>
      </div>
    </header>
    <main class="admin-content">

<?php
/**
 * Example admin list page — the pattern to copy for any new data table:
 * search + pagination + row delete + a detail modal. Pairs with
 * backend/api/contact.php and database/migrations/0001_create_contact_messages.sql.
 */
require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = Database::connection();
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (verify_csrf($_POST['csrf_token'] ?? null)) {
        $stmt = $pdo->prepare('DELETE FROM contact_messages WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
        $flash = 'Message deleted.';
    }
}

$q = clean_str($_GET['q'] ?? '', 120);
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;

$where = '';
$params = [];
if ($q !== '') {
    $where = 'WHERE full_name LIKE :q OR email LIKE :q OR message LIKE :q';
    $params[':q'] = "%{$q}%";
}

$total = (int) (function () use ($pdo, $where, $params) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_messages {$where}");
    $stmt->execute($params);
    return $stmt->fetchColumn();
})();

$totalPages = max(1, (int) ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM contact_messages {$where} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}");
$stmt->execute($params);
$rows = $stmt->fetchAll();

$pageTitle = 'Messages';
$activeNav = 'messages';
require __DIR__ . '/includes/header.php';
?>

<?php if ($flash): ?><div class="alert alert-success"><?= e($flash) ?></div><?php endif; ?>

<div class="panel">
  <div class="panel-head">
    <h3>All Messages (<?= $total ?>)</h3>
    <a href="export_messages.php<?= $q ? '?q=' . urlencode($q) : '' ?>" class="btn btn-outline btn-sm">⬇ Export CSV</a>
  </div>
  <div class="panel-body">
    <form method="get" class="table-toolbar">
      <input type="text" name="q" class="search-input" placeholder="Search name, email, message…" value="<?= e($q) ?>" />
      <button type="submit" class="btn btn-outline btn-sm">Search</button>
    </form>

    <?php if (!$rows): ?>
      <div class="empty-state"><div class="icon">💬</div>No messages found.</div>
    <?php else: ?>
    <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>Name</th><th>Contact</th><th>Message</th><th>Received</th><th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $m): ?>
        <tr>
          <td class="cell-strong"><?= e($m['full_name']) ?></td>
          <td><?= e($m['email']) ?><br><span class="cell-muted"><?= e($m['phone']) ?></span></td>
          <td><?= e(mb_substr($m['message'], 0, 60)) ?><?= mb_strlen($m['message']) > 60 ? '…' : '' ?></td>
          <td class="cell-muted"><?= e(date('d M Y, H:i', strtotime($m['created_at']))) ?></td>
          <td>
            <button type="button" class="btn btn-outline btn-sm" data-view-detail="detail-<?= $m['id'] ?>">View</button>
            <form method="post" class="delete-form" style="margin-top:6px;">
              <input type="hidden" name="action" value="delete" />
              <input type="hidden" name="id" value="<?= $m['id'] ?>" />
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
              <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <?php if ($p === $page): ?>
          <span class="current"><?= $p ?></span>
        <?php else: ?>
          <a href="?q=<?= urlencode($q) ?>&page=<?= $p ?>"><?= $p ?></a>
        <?php endif; ?>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<!-- Detail templates (hidden) -->
<?php foreach ($rows as $m): ?>
<template id="detail-<?= $m['id'] ?>">
  <button class="btn btn-outline btn-sm modal-close" data-close-modal>✕ Close</button>
  <h3><?= e($m['full_name']) ?></h3>
  <dl class="detail-grid">
    <dt>Email</dt><dd><?= e($m['email']) ?></dd>
    <dt>Phone</dt><dd><?= e($m['phone']) ?: '—' ?></dd>
    <dt>Message</dt><dd><?= nl2br(e($m['message'])) ?></dd>
    <dt>Received At</dt><dd><?= e(date('d M Y, H:i', strtotime($m['created_at']))) ?></dd>
    <dt>IP Address</dt><dd><?= e($m['ip_address']) ?></dd>
  </dl>
</template>
<?php endforeach; ?>

<div class="modal-overlay" id="detail-modal">
  <div class="modal-box" style="position:relative;"></div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>

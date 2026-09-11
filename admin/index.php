<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = Database::connection();

$totalMessages = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
$todayMessages = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) = CURDATE()')->fetchColumn();

// Messages for the last 7 days
$last7 = [];
$stmt = $pdo->prepare("SELECT DATE(created_at) d, COUNT(*) c FROM contact_messages WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 DAY) GROUP BY d");
$stmt->execute();
$byDate = array_column($stmt->fetchAll(), 'c', 'd');
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $last7[$date] = $byDate[$date] ?? 0;
}
$maxDay = max(1, max($last7));

// Recent messages
$recent = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 8')->fetchAll();

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
require __DIR__ . '/includes/header.php';
?>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon">💬</div>
    <div class="stat-value"><?= $totalMessages ?></div>
    <div class="stat-label">Total Messages</div>
    <div class="stat-delta up">+<?= $todayMessages ?> today</div>
  </div>
</div>

<div class="dashboard-grid">
  <div class="panel">
    <div class="panel-head">
      <h3>Messages — Last 7 Days</h3>
    </div>
    <div class="panel-body">
      <div class="bar-chart">
        <?php foreach ($last7 as $date => $count): ?>
          <div class="bar-col">
            <div class="bar-value"><?= $count ?></div>
            <div class="bar" style="height: <?= max(4, ($count / $maxDay) * 130) ?>px"></div>
            <div class="bar-label"><?= date('D', strtotime($date)) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h3>Recent Messages</h3>
      <a href="messages.php" class="btn btn-outline btn-sm">View All</a>
    </div>
    <div class="panel-body table-scroll">
      <?php if (!$recent): ?>
        <div class="empty-state"><div class="icon">💬</div>No messages yet.</div>
      <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Name</th><th>Email</th><th>Received</th></tr></thead>
        <tbody>
          <?php foreach ($recent as $m): ?>
          <tr>
            <td class="cell-strong"><?= e($m['full_name']) ?></td>
            <td><?= e($m['email']) ?></td>
            <td class="cell-muted"><?= e(date('d M, H:i', strtotime($m['created_at']))) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>

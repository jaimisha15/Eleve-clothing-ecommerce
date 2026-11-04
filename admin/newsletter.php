<?php
$pageTitle = 'Newsletter Subscribers';
require_once 'includes/header.php';

$db = getDB();

// Remove subscriber
if (isset($_GET['remove'])) {
    $db->prepare("UPDATE newsletter_subscribers SET is_active=0 WHERE id=?")->execute([(int)$_GET['remove']]);
    $message = 'Subscriber removed.';
}

$subscribers = $db->query("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC")->fetchAll();
$active = array_filter($subscribers, fn($s) => $s['is_active']);
?>
<?php if (isset($message)): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 280px;gap:1.25rem;margin-bottom:1.25rem">
  <div class="stat-card green">
    <div class="stat-icon">📧</div>
    <div class="stat-value"><?= count($active) ?></div>
    <div class="stat-label">Active Subscribers</div>
  </div>
  <div class="stat-card gold">
    <div class="stat-icon">📊</div>
    <div class="stat-value"><?= count($subscribers) ?></div>
    <div class="stat-label">Total Signups</div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Subscribers</div>
    <div style="display:flex;gap:.5rem">
      <input type="text" id="tableSearch" placeholder="🔍 Search..." style="padding:.4rem .75rem;background:var(--surface2);border:1px solid var(--border);border-radius:6px;color:var(--text);font-size:.8rem;outline:none;width:200px">
      <a href="#" onclick="exportCSV()" class="btn btn-sm btn-ghost">⬇️ Export CSV</a>
    </div>
  </div>
  <div class="table-wrap">
    <table id="subTable">
      <thead><tr><th>#</th><th>Email</th><th>Status</th><th>Subscribed</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($subscribers as $i => $sub): ?>
        <tr>
          <td style="color:var(--muted)"><?= $i+1 ?></td>
          <td><?= htmlspecialchars($sub['email']) ?></td>
          <td><span class="badge <?= $sub['is_active']?'badge-green':'badge-red' ?>"><?= $sub['is_active']?'Active':'Removed' ?></span></td>
          <td style="color:var(--muted);font-size:.8rem"><?= date('d M Y', strtotime($sub['subscribed_at'])) ?></td>
          <td>
            <?php if ($sub['is_active']): ?>
            <a href="newsletter.php?remove=<?= $sub['id'] ?>" class="btn btn-sm btn-danger" data-confirm="Remove subscriber?">Remove</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($subscribers)): ?>
        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:2rem">No subscribers yet</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function exportCSV() {
  const rows = [['Email','Status','Subscribed']];
  document.querySelectorAll('#subTable tbody tr').forEach(tr => {
    const cells = tr.querySelectorAll('td');
    if (cells.length >= 4) {
      rows.push([cells[1].textContent.trim(), cells[2].textContent.trim(), cells[3].textContent.trim()]);
    }
  });
  const csv = rows.map(r => r.map(c => '"' + c.replace(/"/g,'""') + '"').join(',')).join('\n');
  const blob = new Blob([csv], {type:'text/csv'});
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'eleve-subscribers.csv';
  a.click();
}
</script>

<?php require_once 'includes/footer.php'; ?>

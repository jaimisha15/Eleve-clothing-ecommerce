<?php
$pageTitle = 'Contact Messages';
require_once 'includes/header.php';

$db      = getDB();
$message = '';

// Mark as read
if (isset($_GET['read'])) {
    $db->prepare("UPDATE contact_messages SET status='read' WHERE id=?")->execute([(int)$_GET['read']]);
    $message = 'Message marked as read.';
}

// Delete
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM contact_messages WHERE id=?")->execute([(int)$_GET['delete']]);
    $message = 'Message deleted.';
}

// Mark all read
if (isset($_GET['mark_all'])) {
    $db->query("UPDATE contact_messages SET status='read' WHERE status='new'");
    $message = 'All messages marked as read.';
}

$view = (int) ($_GET['view'] ?? 0);
if ($view) {
    $msg = $db->query("SELECT * FROM contact_messages WHERE id=$view")->fetch();
    $db->prepare("UPDATE contact_messages SET status='read' WHERE id=?")->execute([$view]);
    ?>
    <div style="margin-bottom:1.25rem;display:flex;gap:.5rem">
      <a href="messages.php" class="btn btn-ghost">← Back</a>
    </div>
    <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <div class="card" style="max-width:700px">
      <div class="card-header">
        <div class="card-title">📩 <?= htmlspecialchars($msg['subject'] ?? 'No Subject') ?></div>
        <span style="font-size:.8rem;color:var(--muted)"><?= date('d M Y H:i', strtotime($msg['created_at'])) ?></span>
      </div>
      <div class="card-body">
        <div style="display:flex;gap:2rem;margin-bottom:1.25rem;font-size:.875rem">
          <div><span style="color:var(--muted)">From:</span> <strong><?= htmlspecialchars($msg['name']) ?></strong></div>
          <div><span style="color:var(--muted)">Email:</span> <a href="mailto:<?= htmlspecialchars($msg['email']) ?>" style="color:var(--accent)"><?= htmlspecialchars($msg['email']) ?></a></div>
        </div>
        <div style="background:var(--surface2);border-radius:8px;padding:1.25rem;line-height:1.8;font-size:.9rem;border:1px solid var(--border)">
          <?= nl2br(htmlspecialchars($msg['message'])) ?>
        </div>
        <div style="margin-top:1.25rem;display:flex;gap:.5rem">
          <a href="mailto:<?= htmlspecialchars($msg['email']) ?>?subject=Re: <?= urlencode($msg['subject'] ?? 'Your message') ?>" class="btn btn-primary">↩ Reply via Email</a>
          <a href="messages.php?delete=<?= $msg['id'] ?>" class="btn btn-danger" data-confirm="Delete this message?">🗑️ Delete</a>
        </div>
      </div>
    </div>
    <?php require_once 'includes/footer.php'; exit;
}

$messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<div class="card">
  <div class="card-header">
    <div class="card-title">All Messages (<?= count($messages) ?>)</div>
    <a href="messages.php?mark_all=1" class="btn btn-sm btn-ghost">✅ Mark All Read</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($messages as $msg): ?>
        <tr style="<?= $msg['status'] === 'new' ? 'background:rgba(201,169,110,.03)' : '' ?>">
          <td>
            <div style="font-weight:<?= $msg['status']==='new'?'600':'400' ?>">
              <?= $msg['status'] === 'new' ? '🔔 ' : '' ?><?= htmlspecialchars($msg['name']) ?>
            </div>
          </td>
          <td style="color:var(--muted);font-size:.82rem"><?= htmlspecialchars($msg['email']) ?></td>
          <td style="font-size:.875rem"><?= htmlspecialchars($msg['subject'] ?? '—') ?></td>
          <td>
            <span class="badge <?= $msg['status']==='new'?'badge-gold':($msg['status']==='replied'?'badge-green':'badge-gray') ?>">
              <?= ucfirst($msg['status']) ?>
            </span>
          </td>
          <td style="color:var(--muted);font-size:.8rem"><?= date('d M Y', strtotime($msg['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:.3rem">
              <a href="messages.php?view=<?= $msg['id'] ?>" class="btn btn-sm btn-ghost">👁️ View</a>
              <a href="messages.php?delete=<?= $msg['id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete this message?">🗑️</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:2rem">No messages yet</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

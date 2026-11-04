<?php
$pageTitle = 'Product Reviews';
require_once 'includes/header.php';

$db      = getDB();
$message = '';

// Approve/Reject
if (isset($_GET['approve'])) {
    $db->prepare("UPDATE reviews SET is_approved=1 WHERE id=?")->execute([(int)$_GET['approve']]);
    $message = 'Review approved.';
}
if (isset($_GET['reject'])) {
    $db->prepare("UPDATE reviews SET is_approved=0 WHERE id=?")->execute([(int)$_GET['reject']]);
    $message = 'Review rejected.';
}
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM reviews WHERE id=?")->execute([(int)$_GET['delete']]);
    $message = 'Review deleted.';
}

$reviews = $db->query("SELECT r.*, p.name AS product_name FROM reviews r LEFT JOIN products p ON p.id=r.product_id ORDER BY r.created_at DESC")->fetchAll();
?>
<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<div class="card">
  <div class="card-header">
    <div class="card-title">All Reviews (<?= count($reviews) ?>)</div>
    <input type="text" id="tableSearch" placeholder="🔍 Search..." style="padding:.4rem .75rem;background:var(--surface2);border:1px solid var(--border);border-radius:6px;color:var(--text);font-size:.8rem;outline:none;width:200px">
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Customer</th><th>Product</th><th>Rating</th><th>Review</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($reviews as $r): ?>
        <tr>
          <td>
            <div style="font-weight:500"><?= htmlspecialchars($r['name'] ?? 'Anonymous') ?></div>
            <div style="font-size:.72rem;color:var(--muted)"><?= htmlspecialchars($r['email'] ?? '') ?></div>
          </td>
          <td style="font-size:.82rem"><?= htmlspecialchars($r['product_name'] ?? '—') ?></td>
          <td>
            <div style="color:#f59e0b"><?= str_repeat('★', $r['rating']) ?><?= str_repeat('☆', 5-$r['rating']) ?></div>
            <div style="font-size:.72rem;color:var(--muted)"><?= $r['rating'] ?>/5</div>
          </td>
          <td style="font-size:.82rem;max-width:250px">
            <?php if ($r['title']): ?><div style="font-weight:500"><?= htmlspecialchars($r['title']) ?></div><?php endif; ?>
            <div style="color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:220px"><?= htmlspecialchars(substr($r['comment'] ?? '', 0, 80)) ?>...</div>
          </td>
          <td><span class="badge <?= $r['is_approved']?'badge-green':'badge-yellow' ?>"><?= $r['is_approved']?'Approved':'Pending' ?></span></td>
          <td style="color:var(--muted);font-size:.8rem"><?= date('d M Y', strtotime($r['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:.3rem">
              <?php if (!$r['is_approved']): ?>
              <a href="reviews.php?approve=<?= $r['id'] ?>" class="btn btn-sm btn-success">✅</a>
              <?php else: ?>
              <a href="reviews.php?reject=<?= $r['id'] ?>" class="btn btn-sm btn-ghost">🔕</a>
              <?php endif; ?>
              <a href="reviews.php?delete=<?= $r['id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete this review?">🗑️</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($reviews)): ?>
        <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:2rem">No reviews yet</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

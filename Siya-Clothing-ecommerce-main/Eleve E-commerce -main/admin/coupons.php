<?php
$pageTitle = 'Coupons';
require_once 'includes/header.php';

$db      = getDB();
$message = '';
$action  = $_GET['action'] ?? 'list';

// Delete
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM coupons WHERE id=?")->execute([(int)$_GET['delete']]);
    $message = 'Coupon deleted.';
    $action  = 'list';
}

// Toggle
if (isset($_GET['toggle'])) {
    $db->prepare("UPDATE coupons SET is_active = 1 - is_active WHERE id=?")->execute([(int)$_GET['toggle']]);
    $message = 'Coupon status updated.';
    $action  = 'list';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int) ($_POST['id'] ?? 0);
    $code      = strtoupper(sanitize($_POST['code'] ?? ''));
    $type      = in_array($_POST['type']??'percent',['percent','fixed']) ? $_POST['type'] : 'percent';
    $value     = (float) ($_POST['value'] ?? 0);
    $minOrder  = (float) ($_POST['min_order'] ?? 0);
    $maxUses   = ($_POST['max_uses'] !== '') ? (int)$_POST['max_uses'] : null;
    $expires   = ($_POST['expires_at'] !== '') ? $_POST['expires_at'] : null;
    $isActive  = isset($_POST['is_active']) ? 1 : 0;

    if (!$code || $value <= 0) {
        $message = 'Code and value are required.';
        $msgType = 'danger';
    } else {
        if ($id) {
            $db->prepare("UPDATE coupons SET code=?,type=?,value=?,min_order=?,max_uses=?,expires_at=?,is_active=? WHERE id=?")
               ->execute([$code,$type,$value,$minOrder,$maxUses,$expires,$isActive,$id]);
        } else {
            $db->prepare("INSERT INTO coupons (code,type,value,min_order,max_uses,expires_at,is_active) VALUES (?,?,?,?,?,?,?)")
               ->execute([$code,$type,$value,$minOrder,$maxUses,$expires,$isActive]);
        }
        $message = 'Coupon saved!';
        $action  = 'list';
    }
}

if ($action === 'add' || $action === 'edit') {
    $coupon = null;
    if ($action === 'edit') {
        $coupon = $db->query("SELECT * FROM coupons WHERE id=".(int)$_GET['id'])->fetch();
    }
    ?>
    <?php if ($message): ?><div class="alert alert-<?= $msgType??'success' ?>"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <div class="card" style="max-width:600px">
      <div class="card-header">
        <div class="card-title"><?= $action==='add'?'+ Create Coupon':'✏️ Edit Coupon' ?></div>
        <a href="coupons.php" class="btn btn-sm btn-ghost">← Back</a>
      </div>
      <div class="card-body">
        <form method="POST">
          <?php if ($coupon): ?><input type="hidden" name="id" value="<?= $coupon['id'] ?>"><?php endif; ?>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Coupon Code *</label>
              <input type="text" name="code" class="form-control" required style="text-transform:uppercase"
                     value="<?= htmlspecialchars($coupon['code'] ?? '') ?>" placeholder="WELCOME10">
            </div>
            <div class="form-group">
              <label class="form-label">Discount Type</label>
              <select name="type" class="form-control">
                <option value="percent" <?= ($coupon['type']??'')==='percent'?'selected':'' ?>>Percentage (%)</option>
                <option value="fixed" <?= ($coupon['type']??'')==='fixed'?'selected':'' ?>>Fixed Amount (₹)</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Discount Value *</label>
              <input type="number" name="value" class="form-control" step="0.01" min="0" required
                     value="<?= $coupon['value'] ?? '' ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Min Order Amount (₹)</label>
              <input type="number" name="min_order" class="form-control" step="0.01" min="0"
                     value="<?= $coupon['min_order'] ?? '0' ?>">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Max Uses (blank = unlimited)</label>
              <input type="number" name="max_uses" class="form-control" min="1"
                     value="<?= $coupon['max_uses'] ?? '' ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Expires At (blank = never)</label>
              <input type="datetime-local" name="expires_at" class="form-control"
                     value="<?= $coupon['expires_at'] ? date('Y-m-d\TH:i', strtotime($coupon['expires_at'])) : '' ?>">
            </div>
          </div>
          <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem;margin-bottom:1.25rem">
            <input type="checkbox" name="is_active" <?= ($coupon['is_active']??1)?'checked':'' ?>> Active
          </label>
          <button type="submit" class="btn btn-primary">💾 Save Coupon</button>
        </form>
      </div>
    </div>
    <?php require_once 'includes/footer.php'; exit;
}

$coupons = $db->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll();
?>
<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<div class="card">
  <div class="card-header">
    <div class="card-title">All Coupons (<?= count($coupons) ?>)</div>
    <a href="coupons.php?action=add" class="btn btn-primary btn-sm">+ Create Coupon</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Min Order</th><th>Used</th><th>Expires</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($coupons as $c): ?>
        <tr>
          <td><strong style="color:var(--accent);font-family:monospace;font-size:.9rem"><?= htmlspecialchars($c['code']) ?></strong></td>
          <td><?= ucfirst($c['type']) ?></td>
          <td><?= $c['type']==='percent' ? $c['value'].'%' : '₹'.number_format($c['value'],0) ?></td>
          <td>₹<?= number_format($c['min_order'],0) ?></td>
          <td><?= $c['used_count'] ?> / <?= $c['max_uses'] ?? '∞' ?></td>
          <td style="font-size:.8rem;color:var(--muted)"><?= $c['expires_at'] ? date('d M Y', strtotime($c['expires_at'])) : 'Never' ?></td>
          <td><span class="badge <?= $c['is_active']?'badge-green':'badge-red' ?>"><?= $c['is_active']?'Active':'Disabled' ?></span></td>
          <td>
            <div style="display:flex;gap:.3rem">
              <a href="coupons.php?action=edit&id=<?= $c['id'] ?>" class="btn btn-sm btn-ghost">✏️</a>
              <a href="coupons.php?toggle=<?= $c['id'] ?>" class="btn btn-sm btn-ghost"><?= $c['is_active']?'🔕':'✅' ?></a>
              <a href="coupons.php?delete=<?= $c['id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete coupon '<?= htmlspecialchars($c['code']) ?>'?">🗑️</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($coupons)): ?>
        <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:2rem">No coupons yet</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>

<?php
$pageTitle = 'Site Settings';
require_once 'includes/header.php';

$db      = getDB();
$message = '';

// Load settings
$rows = $db->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
$settings = [];
foreach ($rows as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = ['site_name','site_email','site_phone','currency_symbol','free_shipping_min','tax_rate','maintenance_mode','hero_title','hero_subtitle'];
    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
    foreach ($keys as $key) {
        $val = sanitize($_POST[$key] ?? '');
        $stmt->execute([$key, $val]);
        $settings[$key] = $val;
    }

    // Change admin password
    $newPass = $_POST['admin_password'] ?? '';
    if ($newPass && strlen($newPass) >= 6) {
        $hash = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
        $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hash, $_SESSION['user_id']]);
        $message .= ' Admin password updated.';
    }

    $message = 'Settings saved successfully!' . $message;
}

$s = fn($k) => htmlspecialchars($settings[$k] ?? '');
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<form method="POST">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem">
  <!-- General Settings -->
  <div class="card">
    <div class="card-header"><div class="card-title">🏪 General Settings</div></div>
    <div class="card-body">
      <div class="form-group">
        <label class="form-label">Site Name</label>
        <input type="text" name="site_name" class="form-control" value="<?= $s('site_name') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Support Email</label>
        <input type="email" name="site_email" class="form-control" value="<?= $s('site_email') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Support Phone</label>
        <input type="text" name="site_phone" class="form-control" value="<?= $s('site_phone') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Currency Symbol</label>
        <input type="text" name="currency_symbol" class="form-control" value="<?= $s('currency_symbol') ?>" maxlength="5">
      </div>
      <div class="form-group">
        <label class="form-label">Maintenance Mode</label>
        <select name="maintenance_mode" class="form-control">
          <option value="0" <?= ($settings['maintenance_mode']??'0')==='0'?'selected':'' ?>>Off (Live)</option>
          <option value="1" <?= ($settings['maintenance_mode']??'0')==='1'?'selected':'' ?>>On (Maintenance)</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Commerce Settings -->
  <div class="card">
    <div class="card-header"><div class="card-title">🛒 Commerce Settings</div></div>
    <div class="card-body">
      <div class="form-group">
        <label class="form-label">Free Shipping Minimum (₹)</label>
        <input type="number" name="free_shipping_min" class="form-control" value="<?= $s('free_shipping_min') ?>" min="0">
      </div>
      <div class="form-group">
        <label class="form-label">Tax Rate (%)</label>
        <input type="number" name="tax_rate" class="form-control" value="<?= $s('tax_rate') ?>" min="0" max="100" step="0.5">
      </div>
    </div>
  </div>

  <!-- Hero Banner Settings -->
  <div class="card">
    <div class="card-header"><div class="card-title">🖼️ Homepage Hero</div></div>
    <div class="card-body">
      <div class="form-group">
        <label class="form-label">Hero Title</label>
        <input type="text" name="hero_title" class="form-control" value="<?= $s('hero_title') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Hero Subtitle</label>
        <textarea name="hero_subtitle" class="form-control" rows="3"><?= $s('hero_subtitle') ?></textarea>
      </div>
    </div>
  </div>

  <!-- Admin Password -->
  <div class="card">
    <div class="card-header"><div class="card-title">🔐 Change Admin Password</div></div>
    <div class="card-body">
      <div class="form-group">
        <label class="form-label">New Password (min 6 chars, blank = no change)</label>
        <input type="password" name="admin_password" class="form-control" minlength="6" placeholder="Leave blank to keep current">
      </div>
      <div style="background:rgba(201,169,110,.08);border:1px solid rgba(201,169,110,.2);border-radius:8px;padding:1rem;font-size:.82rem;color:var(--muted)">
        <strong style="color:var(--accent)">Current admin:</strong><br>
        Email: <?= htmlspecialchars($_SESSION['user_email']) ?><br>
        Login: <code style="color:var(--accent)">admin/login.php</code>
      </div>
    </div>
  </div>
</div>

<div style="margin-top:1.25rem">
  <button type="submit" class="btn btn-primary" style="padding:.8rem 2.5rem;font-size:.95rem">💾 Save All Settings</button>
</div>
</form>

<!-- System Info -->
<div class="card" style="margin-top:1.25rem">
  <div class="card-header"><div class="card-title">🖥️ System Information</div></div>
  <div class="card-body" style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;font-size:.82rem">
    <div><span style="color:var(--muted)">PHP Version</span><br><strong><?= PHP_VERSION ?></strong></div>
    <div><span style="color:var(--muted)">MySQL Version</span><br><strong><?= $db->query('SELECT VERSION()')->fetchColumn() ?></strong></div>
    <div><span style="color:var(--muted)">Server</span><br><strong><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Apache/Nginx' ?></strong></div>
    <div><span style="color:var(--muted)">Timezone</span><br><strong><?= date_default_timezone_get() ?></strong></div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

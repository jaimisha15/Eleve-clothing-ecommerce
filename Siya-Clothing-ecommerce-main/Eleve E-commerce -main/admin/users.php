<?php
$pageTitle = 'Users';
require_once 'includes/header.php';

$db      = getDB();
$message = '';
$search  = sanitize($_GET['q'] ?? '');

// Toggle active
if (isset($_GET['toggle'])) {
    $uid = (int) $_GET['toggle'];
    $db->prepare("UPDATE users SET is_active = 1 - is_active WHERE id=? AND role='user'")->execute([$uid]);
    $message = 'User status updated.';
}

// Add new user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = sanitize($_POST['name'] ?? '');
    $email = sanitizeEmail($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $phone = sanitize($_POST['phone'] ?? '');
    $role  = in_array($_POST['role'] ?? 'user', ['user','admin']) ? $_POST['role'] : 'user';

    if ($name && $email && $pass) {
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $message = 'Email already exists.';
            $msgType = 'danger';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
            $db->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?,?,?,?,?)")
               ->execute([$name, $email, $hash, $phone, $role]);
            $message = "User '$name' created successfully!";
        }
    }
}

$where  = ["u.role = 'user'"];
$params = [];
if ($search) {
    $where[]  = "(u.name LIKE ? OR u.email LIKE ?)";
    $l        = "%$search%";
    $params   = [$l, $l];
}
$whereStr = implode(' AND ', $where);
$stmt = $db->prepare("SELECT u.*, COUNT(o.id) AS order_count, COALESCE(SUM(o.total),0) AS total_spent
                      FROM users u LEFT JOIN orders o ON o.user_id = u.id
                      WHERE $whereStr GROUP BY u.id ORDER BY u.created_at DESC");
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<?php if ($message): ?>
<div class="alert alert-<?= $msgType ?? 'success' ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 340px;gap:1.25rem">
  <div class="card">
    <div class="card-header">
      <div class="card-title">All Customers (<?= count($users) ?>)</div>
      <form method="GET">
        <input type="text" name="q" placeholder="🔍 Search..." value="<?= htmlspecialchars($search) ?>"
               style="padding:.4rem .75rem;background:var(--surface2);border:1px solid var(--border);border-radius:6px;color:var(--text);font-size:.8rem;outline:none;width:200px">
      </form>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Email</th><th>Orders</th><th>Spent</th><th>Joined</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($users as $user): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:.65rem">
                <div style="width:32px;height:32px;border-radius:50%;background:rgba(201,169,110,.15);display:flex;align-items:center;justify-content:center;font-size:.75rem;color:var(--accent);font-weight:700;flex-shrink:0">
                  <?= strtoupper(substr($user['name'],0,1)) ?>
                </div>
                <div style="font-weight:500"><?= htmlspecialchars($user['name']) ?></div>
              </div>
            </td>
            <td style="color:var(--muted);font-size:.82rem"><?= htmlspecialchars($user['email']) ?></td>
            <td><?= $user['order_count'] ?></td>
            <td>₹<?= number_format($user['total_spent'], 0) ?></td>
            <td style="color:var(--muted);font-size:.8rem"><?= date('d M Y', strtotime($user['created_at'])) ?></td>
            <td><span class="badge <?= $user['is_active'] ? 'badge-green' : 'badge-red' ?>"><?= $user['is_active'] ? 'Active' : 'Blocked' ?></span></td>
            <td>
              <a href="users.php?toggle=<?= $user['id'] ?>" class="btn btn-sm btn-ghost"
                 data-confirm="<?= $user['is_active'] ? 'Block' : 'Unblock' ?> this user?">
                <?= $user['is_active'] ? '🚫' : '✅' ?>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($users)): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:2rem">No users found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add User Panel -->
  <div class="card" style="align-self:start">
    <div class="card-header"><div class="card-title">+ Add New User</div></div>
    <div class="card-body">
      <form method="POST">
        <div class="form-group">
          <label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required minlength="6">
        </div>
        <div class="form-group">
          <label class="form-label">Role</label>
          <select name="role" class="form-control">
            <option value="user">Customer</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">Create User</button>
      </form>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

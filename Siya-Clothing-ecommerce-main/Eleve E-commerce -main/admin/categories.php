<?php
$pageTitle = 'Categories';
require_once 'includes/header.php';

$db      = getDB();
$message = '';

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_GET['delete']]);
    $message = 'Category deleted.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int) ($_POST['id'] ?? 0);
    $name   = sanitize($_POST['name'] ?? '');
    $slug   = sanitize(strtolower(str_replace(' ', '-', $_POST['slug'] ?? $_POST['name'])));
    $gender = sanitize($_POST['gender'] ?? '');
    $img    = sanitize($_POST['image_url'] ?? '');
    $active = isset($_POST['is_active']) ? 1 : 0;
    $sort   = (int) ($_POST['sort_order'] ?? 0);

    if (!$name) {
        $message = 'Category name required.';
        $msgType = 'danger';
    } else {
        if ($id) {
            $db->prepare("UPDATE categories SET name=?,slug=?,gender=?,image_url=?,is_active=?,sort_order=? WHERE id=?")
               ->execute([$name,$slug,$gender,$img,$active,$sort,$id]);
        } else {
            $db->prepare("INSERT INTO categories (name,slug,gender,image_url,is_active,sort_order) VALUES (?,?,?,?,?,?)")
               ->execute([$name,$slug,$gender,$img,$active,$sort]);
        }
        $message = 'Category saved!';
    }
}

$categories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, name ASC")->fetchAll();
?>
<?php if ($message): ?><div class="alert alert-<?= $msgType??'success' ?>"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 360px;gap:1.25rem">
  <div class="card">
    <div class="card-header"><div class="card-title">All Categories (<?= count($categories) ?>)</div></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Slug</th><th>Gender</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($categories as $cat): ?>
          <tr>
            <td>
              <?php if ($cat['image_url']): ?>
              <img src="<?= htmlspecialchars($cat['image_url']) ?>" style="width:36px;height:36px;border-radius:6px;object-fit:cover;vertical-align:middle;margin-right:.5rem">
              <?php endif; ?>
              <strong><?= htmlspecialchars($cat['name']) ?></strong>
            </td>
            <td style="color:var(--muted);font-size:.8rem;font-family:monospace"><?= htmlspecialchars($cat['slug']) ?></td>
            <td><?= $cat['gender'] ? ucfirst($cat['gender']) : 'All' ?></td>
            <td><?= $cat['sort_order'] ?></td>
            <td><span class="badge <?= $cat['is_active']?'badge-green':'badge-red' ?>"><?= $cat['is_active']?'Active':'Off' ?></span></td>
            <td>
              <a href="categories.php?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete '<?= htmlspecialchars($cat['name']) ?>'?">🗑️</a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($categories)): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:2rem">No categories yet</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card" style="align-self:start">
    <div class="card-header"><div class="card-title">+ Add Category</div></div>
    <div class="card-body">
      <form method="POST">
        <div class="form-group">
          <label class="form-label">Name *</label>
          <input type="text" name="name" class="form-control" required placeholder="T-Shirts">
        </div>
        <div class="form-group">
          <label class="form-label">Slug (auto-generated)</label>
          <input type="text" name="slug" class="form-control" placeholder="t-shirts">
        </div>
        <div class="form-group">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-control">
            <option value="">All</option>
            <option value="men">Men</option>
            <option value="women">Women</option>
            <option value="kids">Kids</option>
            <option value="unisex">Unisex</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Image URL</label>
          <input type="url" name="image_url" class="form-control" placeholder="https://...">
        </div>
        <div class="form-group">
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" class="form-control" value="0" min="0">
        </div>
        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem;margin-bottom:1.25rem">
          <input type="checkbox" name="is_active" checked> Active
        </label>
        <button type="submit" class="btn btn-primary" style="width:100%">Save Category</button>
      </form>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

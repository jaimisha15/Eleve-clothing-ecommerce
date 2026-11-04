<?php
$pageTitle = 'Products';
require_once 'includes/header.php';

$db      = getDB();
$action  = $_REQUEST['action'] ?? 'list';
$message = '';
$msgType = 'success';

// ── Handle POST: Add / Edit ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int) ($_POST['id'] ?? 0);
    $name     = sanitize($_POST['name'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $subcategory = sanitize($_POST['subcategory'] ?? '');
    $gender   = sanitize($_POST['gender'] ?? '');
    $price    = (float) ($_POST['price'] ?? 0);
    $oldPrice = ($_POST['old_price'] !== '') ? (float) $_POST['old_price'] : null;
    $badge    = sanitize($_POST['badge'] ?? '');
    $desc     = sanitize($_POST['description'] ?? '');
    $fit      = sanitize($_POST['fit'] ?? '');
    $details  = sanitize($_POST['details'] ?? '');
    $rating   = (float) ($_POST['rating'] ?? 4.5);
    $isNew    = isset($_POST['is_new_arrival']) ? 1 : 0;
    $isBest   = isset($_POST['is_best_seller']) ? 1 : 0;
    $isFeat   = isset($_POST['is_featured']) ? 1 : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $stock    = (int) ($_POST['stock_qty'] ?? 100);
    $images   = array_filter(array_map('trim', explode("\n", $_POST['images'] ?? '')));
    $sizes    = array_filter(array_map('trim', explode(',', $_POST['sizes'] ?? '')));
    $colorsRaw = array_filter(array_map('trim', explode("\n", $_POST['colors'] ?? '')));

    if (!$name || !$category || $price <= 0) {
        $message = 'Name, category and price are required.';
        $msgType = 'danger';
    } else {
        if ($id) {
            // Update
            $stmt = $db->prepare("UPDATE products SET name=?,category=?,subcategory=?,gender=?,price=?,old_price=?,badge=?,description=?,fit=?,details=?,rating=?,is_new_arrival=?,is_best_seller=?,is_featured=?,is_active=?,stock_qty=? WHERE id=?");
            $stmt->execute([$name,$category,$subcategory,$gender,$price,$oldPrice,$badge?:null,$desc,$fit,$details,$rating,$isNew,$isBest,$isFeat,$isActive,$stock,$id]);
        } else {
            // Insert
            $stmt = $db->prepare("INSERT INTO products (name,category,subcategory,gender,price,old_price,badge,description,fit,details,rating,is_new_arrival,is_best_seller,is_featured,is_active,stock_qty) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$name,$category,$subcategory,$gender,$price,$oldPrice,$badge?:null,$desc,$fit,$details,$rating,$isNew,$isBest,$isFeat,$isActive,$stock]);
            $id = $db->lastInsertId();
        }

        // Update images
        $db->prepare("DELETE FROM product_images WHERE product_id=?")->execute([$id]);
        $imgStmt = $db->prepare("INSERT INTO product_images (product_id, url, sort_order) VALUES (?,?,?)");
        foreach ($images as $i => $url) {
            if ($url) $imgStmt->execute([$id, $url, $i]);
        }

        // Update sizes
        $db->prepare("DELETE FROM product_sizes WHERE product_id=?")->execute([$id]);
        $szStmt = $db->prepare("INSERT INTO product_sizes (product_id, size) VALUES (?,?)");
        foreach ($sizes as $sz) {
            if ($sz) $szStmt->execute([$id, $sz]);
        }

        // Update colors
        $db->prepare("DELETE FROM product_colors WHERE product_id=?")->execute([$id]);
        $clStmt = $db->prepare("INSERT INTO product_colors (product_id, name, hex) VALUES (?,?,?)");
        foreach ($colorsRaw as $colorLine) {
            $parts = explode(':', $colorLine, 2);
            if (count($parts) === 2) $clStmt->execute([$id, trim($parts[0]), trim($parts[1])]);
        }

        $message = 'Product saved successfully!';
        $action  = 'list';
    }
}

// ── Handle: Delete ───────────────────────────────────────────
if (isset($_GET['delete'])) {
    $delId = (int) $_GET['delete'];
    $db->prepare("UPDATE products SET is_active=0 WHERE id=?")->execute([$delId]);
    $message = 'Product deactivated.';
    $action  = 'list';
}

// ── Handle: Toggle Active ────────────────────────────────────
if (isset($_GET['toggle'])) {
    $tId = (int) $_GET['toggle'];
    $db->prepare("UPDATE products SET is_active = 1 - is_active WHERE id=?")->execute([$tId]);
    $message = 'Product status updated.';
    $action  = 'list';
}
?>

<?php if ($message): ?>
<div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
  <?php
  $product = null;
  if ($action === 'edit') {
    $pid     = (int) ($_GET['id'] ?? 0);
    $product = $db->query("SELECT * FROM products WHERE id=$pid")->fetch();
    $images  = $db->query("SELECT url FROM product_images WHERE product_id=$pid ORDER BY sort_order")->fetchAll(PDO::FETCH_COLUMN);
    $sizes   = $db->query("SELECT size FROM product_sizes WHERE product_id=$pid ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
    $colors  = $db->query("SELECT CONCAT(name,':',hex) FROM product_colors WHERE product_id=$pid ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
  }
  ?>
  <div class="card">
    <div class="card-header">
      <div class="card-title"><?= $action === 'add' ? '+ Add New Product' : '✏️ Edit Product' ?></div>
      <a href="products.php" class="btn btn-sm btn-ghost">← Back</a>
    </div>
    <div class="card-body">
      <form method="POST" autocomplete="off">
        <?php if ($product): ?>
          <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <?php endif; ?>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Product Name *</label>
            <input type="text" name="name" class="form-control" required
                   value="<?= htmlspecialchars($product['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Category *</label>
            <input type="text" name="category" class="form-control" required placeholder="e.g. T-Shirts, Jackets"
                   value="<?= htmlspecialchars($product['category'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Subcategory</label>
            <input type="text" name="subcategory" class="form-control" placeholder="e.g. hoodies, jeans"
                   value="<?= htmlspecialchars($product['subcategory'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-control">
              <option value="" <?= ($product['gender']??'')===''?'selected':'' ?>>Unisex/All</option>
              <option value="men" <?= ($product['gender']??'')==='men'?'selected':'' ?>>Men</option>
              <option value="women" <?= ($product['gender']??'')==='women'?'selected':'' ?>>Women</option>
              <option value="kids" <?= ($product['gender']??'')==='kids'?'selected':'' ?>>Kids</option>
              <option value="unisex" <?= ($product['gender']??'')==='unisex'?'selected':'' ?>>Unisex</option>
            </select>
          </div>
        </div>

        <div class="form-row-3">
          <div class="form-group">
            <label class="form-label">Price (₹) *</label>
            <input type="number" name="price" class="form-control" step="0.01" required min="0"
                   value="<?= $product['price'] ?? '' ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Old Price (₹) — for sale</label>
            <input type="number" name="old_price" class="form-control" step="0.01" min="0"
                   value="<?= $product['old_price'] ?? '' ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Badge</label>
            <select name="badge" class="form-control">
              <option value="">— None —</option>
              <?php foreach (['New','Best Seller','Sale','Limited','Hot'] as $b): ?>
              <option value="<?= $b ?>" <?= ($product['badge']??'')===$b?'selected':'' ?>><?= $b ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Rating (1–5)</label>
            <input type="number" name="rating" class="form-control" step="0.1" min="1" max="5"
                   value="<?= $product['rating'] ?? '4.5' ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Stock Qty</label>
            <input type="number" name="stock_qty" class="form-control" min="0"
                   value="<?= $product['stock_qty'] ?? '100' ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Details (pipe-separated: item1|item2|item3)</label>
          <input type="text" name="details" class="form-control" placeholder="100% Cotton|Machine washable|Slim fit"
                 value="<?= htmlspecialchars($product['details'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label">Fit Guide</label>
          <textarea name="fit" class="form-control" rows="2"><?= htmlspecialchars($product['fit'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Sizes (comma-separated: S,M,L,XL)</label>
            <input type="text" name="sizes" class="form-control" placeholder="S,M,L,XL"
                   value="<?= htmlspecialchars(implode(',', $sizes ?? [])) ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Colors (one per line: Name:#hex)</label>
            <textarea name="colors" class="form-control" rows="3" placeholder="Black:#1a1a1a&#10;White:#f5f5f5"><?= htmlspecialchars(implode("\n", $colors ?? [])) ?></textarea>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Image URLs (one per line)</label>
          <textarea name="images" class="form-control" rows="5" placeholder="https://images.unsplash.com/..."><?= htmlspecialchars(implode("\n", $images ?? [])) ?></textarea>
        </div>

        <div style="display:flex;gap:1.5rem;margin-bottom:1.25rem;flex-wrap:wrap">
          <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem">
            <input type="checkbox" name="is_new_arrival" <?= ($product['is_new_arrival']??0)?'checked':'' ?>> New Arrival
          </label>
          <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem">
            <input type="checkbox" name="is_best_seller" <?= ($product['is_best_seller']??0)?'checked':'' ?>> Best Seller
          </label>
          <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem">
            <input type="checkbox" name="is_featured" <?= ($product['is_featured']??1)?'checked':'' ?>> Featured on Homepage
          </label>
          <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem">
            <input type="checkbox" name="is_active" <?= ($product['is_active']??1)?'checked':'' ?>> Active
          </label>
        </div>

        <button type="submit" class="btn btn-primary">💾 Save Product</button>
        <a href="products.php" class="btn btn-ghost" style="margin-left:.5rem">Cancel</a>
      </form>
    </div>
  </div>

<?php else: ?>
  <!-- Product List -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">All Products (<?= $db->query("SELECT COUNT(*) FROM products")->fetchColumn() ?>)</div>
      <div style="display:flex;gap:.5rem;align-items:center">
        <input type="text" id="tableSearch" placeholder="🔍 Search..." style="padding:.4rem .75rem;background:var(--surface2);border:1px solid var(--border);border-radius:6px;color:var(--text);font-size:.8rem;outline:none;width:180px">
        <a href="products.php?action=add" class="btn btn-primary btn-sm">+ Add Product</a>
      </div>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Category</th>
            <th>Gender</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $products = $db->query("SELECT p.*, (SELECT url FROM product_images WHERE product_id=p.id ORDER BY sort_order LIMIT 1) AS image FROM products p ORDER BY p.id DESC")->fetchAll();
          foreach ($products as $p):
          ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:.75rem">
                <?php if ($p['image']): ?>
                  <img src="<?= htmlspecialchars($p['image']) ?>" class="td-img" alt="">
                <?php else: ?>
                  <div class="td-img" style="background:var(--surface2);display:flex;align-items:center;justify-content:center">📦</div>
                <?php endif; ?>
                <div>
                  <div style="font-weight:500"><?= htmlspecialchars($p['name']) ?></div>
                  <div style="font-size:.72rem;color:var(--muted)">#<?= $p['id'] ?> <?= $p['badge'] ? '· <span style="color:var(--accent)">'.$p['badge'].'</span>' : '' ?></div>
                </div>
              </div>
            </td>
            <td><?= htmlspecialchars($p['category']) ?><br><span style="font-size:.72rem;color:var(--muted)"><?= htmlspecialchars($p['subcategory'] ?? '') ?></span></td>
            <td><?= $p['gender'] ? ucfirst($p['gender']) : 'All' ?></td>
            <td>
              <strong>₹<?= number_format($p['price'], 0) ?></strong>
              <?php if ($p['old_price']): ?>
                <br><span style="font-size:.72rem;color:var(--muted);text-decoration:line-through">₹<?= number_format($p['old_price'], 0) ?></span>
              <?php endif; ?>
            </td>
            <td>
              <span style="color:<?= $p['stock_qty'] < 10 ? 'var(--danger)' : 'var(--success)' ?>"><?= $p['stock_qty'] ?></span>
            </td>
            <td>
              <span class="badge <?= $p['is_active'] ? 'badge-green' : 'badge-red' ?>">
                <?= $p['is_active'] ? 'Active' : 'Inactive' ?>
              </span>
            </td>
            <td>
              <div style="display:flex;gap:.3rem;flex-wrap:nowrap">
                <a href="products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-ghost">✏️</a>
                <a href="products.php?toggle=<?= $p['id'] ?>" class="btn btn-sm btn-ghost"><?= $p['is_active'] ? '🔕' : '✅' ?></a>
                <a href="products.php?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger"
                   data-confirm="Deactivate '<?= htmlspecialchars($p['name']) ?>'?">🗑️</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($products)): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:2rem">No products found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>

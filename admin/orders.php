<?php
$pageTitle = 'Orders';
require_once 'includes/header.php';

$db      = getDB();
$message = '';
$view    = (int) ($_GET['view'] ?? 0);
$filter  = sanitize($_GET['status'] ?? '');
$search  = sanitize($_GET['q'] ?? '');

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId   = (int) $_POST['order_id'];
    $newStatus = sanitize($_POST['status']);
    $payStatus = sanitize($_POST['payment_status']);
    $db->prepare("UPDATE orders SET status=?, payment_status=? WHERE id=?")->execute([$newStatus, $payStatus, $orderId]);
    $message = 'Order updated successfully!';
}

// View single order
if ($view) {
    $order = $db->query("SELECT o.*, COALESCE(u.name,'Guest') AS customer_name FROM orders o LEFT JOIN users u ON u.id=o.user_id WHERE o.id=$view")->fetch();
    $items = $db->query("SELECT * FROM order_items WHERE order_id=$view")->fetchAll();
    if (!$order) {
        header('Location: orders.php');
        exit;
    }
    ?>
    <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <div style="display:flex;gap:.75rem;margin-bottom:1.25rem;align-items:center">
      <a href="orders.php" class="btn btn-ghost">← All Orders</a>
      <h2 style="font-size:1.1rem;font-weight:600">Order #<?= htmlspecialchars($order['order_number']) ?></h2>
      <?php
      $statMap = ['pending'=>'badge-yellow','confirmed'=>'badge-blue','processing'=>'badge-blue','shipped'=>'badge-gold','delivered'=>'badge-green','cancelled'=>'badge-red','refunded'=>'badge-gray'];
      ?>
      <span class="badge <?= $statMap[$order['status']] ?? 'badge-gray' ?>"><?= ucfirst($order['status']) ?></span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 380px;gap:1.25rem">
      <!-- Order Items -->
      <div>
        <div class="card" style="margin-bottom:1.25rem">
          <div class="card-header"><div class="card-title">📦 Order Items</div></div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Product</th><th>Size</th><th>Color</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                  <td>
                    <div style="display:flex;align-items:center;gap:.6rem">
                      <?php if ($item['product_image']): ?>
                        <img src="<?= htmlspecialchars($item['product_image']) ?>" style="width:40px;height:40px;border-radius:6px;object-fit:cover">
                      <?php endif; ?>
                      <?= htmlspecialchars($item['product_name']) ?>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($item['size'] ?? '—') ?></td>
                  <td><?= htmlspecialchars($item['color'] ?? '—') ?></td>
                  <td><?= $item['qty'] ?></td>
                  <td>₹<?= number_format($item['price'], 0) ?></td>
                  <td><strong>₹<?= number_format($item['total'], 0) ?></strong></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div style="padding:1rem;border-top:1px solid var(--border)">
            <div style="display:flex;flex-direction:column;gap:.4rem;max-width:280px;margin-left:auto;font-size:.875rem">
              <div style="display:flex;justify-content:space-between"><span style="color:var(--muted)">Subtotal</span><span>₹<?= number_format($order['subtotal'], 0) ?></span></div>
              <div style="display:flex;justify-content:space-between"><span style="color:var(--muted)">Shipping</span><span>₹<?= number_format($order['shipping_fee'], 0) ?></span></div>
              <?php if ($order['discount'] > 0): ?>
              <div style="display:flex;justify-content:space-between;color:var(--success)"><span>Discount</span><span>−₹<?= number_format($order['discount'], 0) ?></span></div>
              <?php endif; ?>
              <div style="display:flex;justify-content:space-between;font-weight:700;font-size:1rem;padding-top:.5rem;border-top:1px solid var(--border)">
                <span>Total</span><span style="color:var(--accent)">₹<?= number_format($order['total'], 0) ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipping Info -->
        <div class="card">
          <div class="card-header"><div class="card-title">🚚 Shipping Details</div></div>
          <div class="card-body" style="font-size:.875rem;line-height:1.8">
            <strong><?= htmlspecialchars($order['shipping_name']) ?></strong><br>
            <?= htmlspecialchars($order['shipping_email']) ?><br>
            <?= htmlspecialchars($order['shipping_phone'] ?? '') ?><br>
            <?= htmlspecialchars($order['shipping_address']) ?><br>
            <?= htmlspecialchars($order['shipping_city']) ?><?= $order['shipping_state'] ? ', '.htmlspecialchars($order['shipping_state']) : '' ?> — <?= htmlspecialchars($order['shipping_pincode']) ?>
          </div>
        </div>
      </div>

      <!-- Status Update Panel -->
      <div>
        <div class="card" style="margin-bottom:1.25rem">
          <div class="card-header"><div class="card-title">⚙️ Update Status</div></div>
          <div class="card-body">
            <form method="POST">
              <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
              <input type="hidden" name="update_status" value="1">
              <div class="form-group">
                <label class="form-label">Order Status</label>
                <select name="status" class="form-control">
                  <?php foreach (['pending','confirmed','processing','shipped','delivered','cancelled','refunded'] as $s): ?>
                  <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Payment Status</label>
                <select name="payment_status" class="form-control">
                  <?php foreach (['pending','paid','failed','refunded'] as $s): ?>
                  <option value="<?= $s ?>" <?= $order['payment_status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" class="btn btn-primary" style="width:100%">Update Order</button>
            </form>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><div class="card-title">📋 Order Info</div></div>
          <div class="card-body" style="font-size:.8rem;line-height:2">
            <div><span style="color:var(--muted)">Order #</span> <?= htmlspecialchars($order['order_number']) ?></div>
            <div><span style="color:var(--muted)">Date</span> <?= date('d M Y H:i', strtotime($order['created_at'])) ?></div>
            <div><span style="color:var(--muted)">Customer</span> <?= htmlspecialchars($order['customer_name']) ?></div>
            <div><span style="color:var(--muted)">Payment</span> <?= strtoupper($order['payment_method']) ?></div>
          </div>
        </div>
      </div>
    </div>
    <?php require_once 'includes/footer.php'; exit;
}

// ── Order List ────────────────────────────────────────────────
$where  = ['1=1'];
$params = [];

if ($filter) {
    $where[]  = "o.status = ?";
    $params[] = $filter;
}
if ($search) {
    $where[]  = "(o.order_number LIKE ? OR o.shipping_name LIKE ? OR o.shipping_email LIKE ?)";
    $l        = "%$search%";
    $params   = array_merge($params, [$l, $l, $l]);
}

$whereStr = implode(' AND ', $where);
$stmt     = $db->prepare("SELECT o.*, COALESCE(u.name,'Guest') AS customer_name FROM orders o LEFT JOIN users u ON u.id=o.user_id WHERE $whereStr ORDER BY o.created_at DESC LIMIT 100");
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<div class="card">
  <div class="card-header">
    <div class="card-title">All Orders (<?= count($orders) ?>)</div>
    <div style="display:flex;gap:.5rem;align-items:center">
      <form method="GET" style="display:flex;gap:.5rem">
        <input type="text" name="q" placeholder="🔍 Search..." value="<?= htmlspecialchars($search) ?>"
               style="padding:.4rem .75rem;background:var(--surface2);border:1px solid var(--border);border-radius:6px;color:var(--text);font-size:.8rem;outline:none;width:180px">
        <select name="status" style="padding:.4rem .75rem;background:var(--surface2);border:1px solid var(--border);border-radius:6px;color:var(--text);font-size:.8rem;outline:none">
          <option value="">All Status</option>
          <?php foreach (['pending','confirmed','processing','shipped','delivered','cancelled'] as $s): ?>
          <option value="<?= $s ?>" <?= $filter===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
      </form>
    </div>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Order #</th>
          <th>Customer</th>
          <th>Items</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $statMap = ['pending'=>'badge-yellow','confirmed'=>'badge-blue','processing'=>'badge-blue','shipped'=>'badge-gold','delivered'=>'badge-green','cancelled'=>'badge-red','refunded'=>'badge-gray'];
        $payMap  = ['pending'=>'badge-yellow','paid'=>'badge-green','failed'=>'badge-red','refunded'=>'badge-gray'];
        foreach ($orders as $order):
          $itemCount = $db->query("SELECT SUM(qty) FROM order_items WHERE order_id={$order['id']}")->fetchColumn();
        ?>
        <tr>
          <td><strong style="color:var(--accent)"><?= htmlspecialchars($order['order_number']) ?></strong></td>
          <td>
            <div style="font-weight:500"><?= htmlspecialchars($order['customer_name']) ?></div>
            <div style="font-size:.72rem;color:var(--muted)"><?= htmlspecialchars($order['shipping_email']) ?></div>
          </td>
          <td><?= $itemCount ?> item<?= $itemCount != 1 ? 's' : '' ?></td>
          <td><strong>₹<?= number_format($order['total'], 0) ?></strong></td>
          <td><span class="badge <?= $payMap[$order['payment_status']] ?? 'badge-gray' ?>"><?= ucfirst($order['payment_status']) ?></span></td>
          <td><span class="badge <?= $statMap[$order['status']] ?? 'badge-gray' ?>"><?= ucfirst($order['status']) ?></span></td>
          <td style="color:var(--muted);font-size:.8rem"><?= date('d M Y', strtotime($order['created_at'])) ?></td>
          <td><a href="orders.php?view=<?= $order['id'] ?>" class="btn btn-sm btn-ghost">View</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
        <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:2rem">No orders found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

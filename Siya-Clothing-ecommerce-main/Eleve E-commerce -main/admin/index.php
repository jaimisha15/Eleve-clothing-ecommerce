<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';

$db = getDB();

// Stats
$totalProducts  = $db->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
$totalOrders    = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue   = $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE payment_status='paid'")->fetchColumn();
$totalUsers     = $db->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$pendingOrders  = $db->query("SELECT COUNT(*) FROM orders WHERE status='confirmed'")->fetchColumn();
$newMessages    = $db->query("SELECT COUNT(*) FROM contact_messages WHERE status='new'")->fetchColumn();
$subscribers    = $db->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE is_active=1")->fetchColumn();
$todayOrders    = $db->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();

// Recent orders
$recentOrders = $db->query("SELECT o.*, COALESCE(u.name, o.shipping_name) AS customer_name FROM orders o LEFT JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC LIMIT 8")->fetchAll();

// Top products (by order items)
$topProducts = $db->query("SELECT p.name, p.price, SUM(oi.qty) AS sold, SUM(oi.total) AS revenue,
                             (SELECT url FROM product_images WHERE product_id = p.id LIMIT 1) AS image
                            FROM order_items oi JOIN products p ON p.id = oi.product_id
                            GROUP BY p.id ORDER BY sold DESC LIMIT 5")->fetchAll();

// Revenue chart data (last 7 days)
$chartData = $db->query("SELECT DATE(created_at) AS date, COALESCE(SUM(total),0) AS revenue, COUNT(*) AS orders
                          FROM orders WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                          GROUP BY DATE(created_at) ORDER BY date ASC")->fetchAll();
?>

<!-- Stats Grid -->
<div class="stats-grid">
  <div class="stat-card gold">
    <div class="stat-icon">💰</div>
    <div class="stat-value">₹<?= number_format($totalRevenue, 0) ?></div>
    <div class="stat-label">Total Revenue</div>
    <div class="stat-change up">↑ From paid orders</div>
  </div>
  <div class="stat-card green">
    <div class="stat-icon">📦</div>
    <div class="stat-value"><?= $totalOrders ?></div>
    <div class="stat-label">Total Orders</div>
    <div class="stat-change up">↑ <?= $todayOrders ?> today</div>
  </div>
  <div class="stat-card blue">
    <div class="stat-icon">👥</div>
    <div class="stat-value"><?= $totalUsers ?></div>
    <div class="stat-label">Customers</div>
    <div class="stat-change up">↑ <?= $subscribers ?> subscribers</div>
  </div>
  <div class="stat-card gold">
    <div class="stat-icon">🛍️</div>
    <div class="stat-value"><?= $totalProducts ?></div>
    <div class="stat-label">Active Products</div>
  </div>
  <div class="stat-card red">
    <div class="stat-icon">⏳</div>
    <div class="stat-value"><?= $pendingOrders ?></div>
    <div class="stat-label">Pending Orders</div>
    <div class="stat-change down">Need attention</div>
  </div>
  <div class="stat-card blue">
    <div class="stat-icon">💬</div>
    <div class="stat-value"><?= $newMessages ?></div>
    <div class="stat-label">New Messages</div>
  </div>
</div>

<!-- Charts & Quick Actions Row -->
<div style="display:grid;grid-template-columns:1fr 300px;gap:1.25rem;margin-bottom:1.25rem">
  <!-- Revenue Chart -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">📈 Revenue — Last 7 Days</div>
    </div>
    <div class="card-body" style="padding:1rem">
      <canvas id="revenueChart" height="200"></canvas>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="card">
    <div class="card-header"><div class="card-title">⚡ Quick Actions</div></div>
    <div class="card-body" style="display:flex;flex-direction:column;gap:.6rem;padding:1rem">
      <a href="products.php?action=add" class="btn btn-primary" style="justify-content:center">+ Add New Product</a>
      <a href="orders.php?status=confirmed" class="btn btn-ghost" style="justify-content:center">📦 View Pending Orders</a>
      <a href="messages.php" class="btn btn-ghost" style="justify-content:center">💬 Read Messages</a>
      <a href="newsletter.php" class="btn btn-ghost" style="justify-content:center">📧 Newsletter</a>
      <a href="coupons.php?action=add" class="btn btn-ghost" style="justify-content:center">🏷️ Create Coupon</a>
      <a href="settings.php" class="btn btn-ghost" style="justify-content:center">⚙️ Site Settings</a>
    </div>
  </div>
</div>

<!-- Recent Orders & Top Products -->
<div style="display:grid;grid-template-columns:1fr 380px;gap:1.25rem">
  <!-- Recent Orders -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">🕒 Recent Orders</div>
      <a href="orders.php" class="btn btn-sm btn-ghost">View All</a>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentOrders as $order): ?>
          <tr>
            <td><strong style="color:var(--accent)"><?= htmlspecialchars($order['order_number']) ?></strong></td>
            <td><?= htmlspecialchars($order['customer_name'] ?? $order['shipping_name']) ?></td>
            <td>₹<?= number_format($order['total'], 0) ?></td>
            <td><?php
              $statMap = ['pending'=>'badge-yellow','confirmed'=>'badge-blue','processing'=>'badge-blue','shipped'=>'badge-gold','delivered'=>'badge-green','cancelled'=>'badge-red','refunded'=>'badge-gray'];
              $cls = $statMap[$order['status']] ?? 'badge-gray';
            ?>
              <span class="badge <?= $cls ?>"><?= ucfirst($order['status']) ?></span>
            </td>
            <td style="color:var(--muted);font-size:.8rem"><?= date('d M', strtotime($order['created_at'])) ?></td>
            <td><a href="orders.php?view=<?= $order['id'] ?>" class="btn btn-sm btn-ghost">View</a></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recentOrders)): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:2rem">No orders yet</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Top Products -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">🏆 Top Products</div>
      <a href="products.php" class="btn btn-sm btn-ghost">All</a>
    </div>
    <div class="card-body" style="padding:.75rem">
      <?php if (empty($topProducts)): ?>
        <p style="color:var(--muted);text-align:center;padding:1.5rem">No sales data yet</p>
      <?php endif; ?>
      <?php foreach ($topProducts as $i => $p): ?>
      <div style="display:flex;align-items:center;gap:.75rem;padding:.6rem .5rem;border-bottom:1px solid var(--border)">
        <div style="width:24px;height:24px;border-radius:50%;background:rgba(201,169,110,.15);display:flex;align-items:center;justify-content:center;font-size:.75rem;color:var(--accent);font-weight:700;flex-shrink:0"><?= $i+1 ?></div>
        <?php if ($p['image']): ?>
          <img src="<?= htmlspecialchars($p['image']) ?>" style="width:40px;height:40px;border-radius:6px;object-fit:cover">
        <?php endif; ?>
        <div style="flex:1;min-width:0">
          <div style="font-size:.82rem;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($p['name']) ?></div>
          <div style="font-size:.72rem;color:var(--muted)"><?= $p['sold'] ?> sold · ₹<?= number_format($p['revenue'], 0) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartData = <?= json_encode($chartData) ?>;

// Fill missing days
const labels = [];
const revenues = [];
const ordersData = [];
for (let i = 6; i >= 0; i--) {
  const d = new Date();
  d.setDate(d.getDate() - i);
  const dateStr = d.toISOString().split('T')[0];
  labels.push(d.toLocaleDateString('en-IN', {month:'short', day:'numeric'}));
  const found = chartData.find(r => r.date === dateStr);
  revenues.push(found ? parseFloat(found.revenue) : 0);
  ordersData.push(found ? parseInt(found.orders) : 0);
}

new Chart(document.getElementById('revenueChart'), {
  type: 'bar',
  data: {
    labels,
    datasets: [
      {
        label: 'Revenue (₹)',
        data: revenues,
        backgroundColor: 'rgba(201,169,110,.4)',
        borderColor: '#c9a96e',
        borderWidth: 1,
        borderRadius: 4,
        yAxisID: 'y',
      },
      {
        label: 'Orders',
        data: ordersData,
        type: 'line',
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59,130,246,.1)',
        borderWidth: 2,
        pointRadius: 4,
        tension: 0.3,
        yAxisID: 'y1',
      }
    ]
  },
  options: {
    responsive: true,
    interaction: {mode: 'index', intersect: false},
    plugins: {legend: {labels: {color: '#94a3b8', font: {size: 11}}}},
    scales: {
      x: {ticks: {color: '#94a3b8', font: {size: 11}}, grid: {color: 'rgba(42,49,70,.6)'}},
      y: {ticks: {color: '#94a3b8', font: {size: 11}, callback: v => '₹' + v}, grid: {color: 'rgba(42,49,70,.6)'}},
      y1: {position: 'right', ticks: {color: '#3b82f6', font: {size: 11}}, grid: {drawOnChartArea: false}}
    }
  }
});
</script>

<?php require_once 'includes/footer.php'; ?>

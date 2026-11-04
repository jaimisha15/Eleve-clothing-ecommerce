<?php
require_once __DIR__ . '/../../php/config.php';
requireAdmin();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? 'Admin' ?> — ELEVÉ Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ============================================================
   ELEVÉ Admin Panel Styles
   ============================================================ */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0f1117;--surface:#161b27;--surface2:#1e2332;--border:#2a3146;
  --text:#e2e8f0;--muted:#94a3b8;--accent:#c9a96e;--accent-h:#d4b87d;
  --success:#10b981;--danger:#ef4444;--warning:#f59e0b;--info:#3b82f6;
  --sidebar-w:260px;
}
body{font-family:'Inter',-apple-system,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;overflow-x:hidden}
a{color:inherit;text-decoration:none}
img{max-width:100%}
button{font:inherit;cursor:pointer;border:none;background:none;color:inherit}

/* Sidebar */
.sidebar{position:fixed;top:0;left:0;width:var(--sidebar-w);height:100vh;background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;z-index:100;overflow-y:auto}
.sidebar-brand{padding:1.5rem 1.25rem;border-bottom:1px solid var(--border)}
.sidebar-logo{font-family:Georgia,serif;font-size:1.4rem;letter-spacing:.2em;color:var(--accent);font-weight:700;text-transform:uppercase}
.sidebar-sub{font-size:.65rem;color:var(--muted);letter-spacing:.15em;text-transform:uppercase;margin-top:.2rem}
.sidebar-nav{flex:1;padding:.75rem 0}
.nav-section{font-size:.65rem;font-weight:600;letter-spacing:.12em;color:var(--muted);text-transform:uppercase;padding:.75rem 1.25rem .4rem}
.nav-item{display:flex;align-items:center;gap:.75rem;padding:.65rem 1.25rem;font-size:.88rem;color:var(--muted);transition:all .15s;border-left:3px solid transparent;margin:.1rem 0}
.nav-item:hover{background:var(--surface2);color:var(--text);border-left-color:rgba(201,169,110,.4)}
.nav-item.active{background:rgba(201,169,110,.1);color:var(--accent);border-left-color:var(--accent)}
.nav-item .icon{font-size:1.1rem;width:20px;text-align:center;flex-shrink:0}
.nav-badge{margin-left:auto;background:var(--danger);color:#fff;font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:9999px;min-width:20px;text-align:center}
.sidebar-footer{padding:1rem 1.25rem;border-top:1px solid var(--border)}
.sidebar-user{display:flex;align-items:center;gap:.75rem;font-size:.8rem}
.sidebar-avatar{width:34px;height:34px;border-radius:50%;background:rgba(201,169,110,.2);border:1.5px solid rgba(201,169,110,.4);display:flex;align-items:center;justify-content:center;font-size:.85rem;color:var(--accent);font-weight:700;flex-shrink:0}
.sidebar-name{font-weight:500;color:var(--text)}
.sidebar-role{color:var(--muted);font-size:.7rem}
.btn-logout{display:flex;align-items:center;gap:.5rem;margin-top:.75rem;padding:.5rem .75rem;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);border-radius:6px;color:#ef4444;font-size:.8rem;font-weight:500;transition:all .15s;width:100%;justify-content:center}
.btn-logout:hover{background:rgba(239,68,68,.2)}

/* Main Content */
.main-wrapper{margin-left:var(--sidebar-w);min-height:100vh;display:flex;flex-direction:column}
.topbar{background:var(--surface);border-bottom:1px solid var(--border);padding:.875rem 1.75rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50}
.topbar-title{font-size:1.1rem;font-weight:600;color:var(--text)}
.topbar-right{display:flex;align-items:center;gap:1rem}
.topbar-btn{padding:.45rem 1rem;background:var(--accent);color:#0a0a0a;border-radius:6px;font-size:.8rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem}
.topbar-btn:hover{background:var(--accent-h);transform:translateY(-1px)}
.topbar-btn-ghost{padding:.45rem 1rem;background:var(--surface2);border:1px solid var(--border);color:var(--text);border-radius:6px;font-size:.8rem;font-weight:500;transition:all .15s}
.topbar-btn-ghost:hover{border-color:var(--accent);color:var(--accent)}
.content{padding:1.75rem;flex:1}

/* Cards */
.card{background:var(--surface);border:1px solid var(--border);border-radius:10px;overflow:hidden}
.card-header{padding:1rem 1.25rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:.95rem;font-weight:600;color:var(--text)}
.card-body{padding:1.25rem}

/* Stats */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:1.25rem;position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s}
.stat-card:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(0,0,0,.3)}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px}
.stat-card.gold::before{background:linear-gradient(90deg,var(--accent),transparent)}
.stat-card.green::before{background:linear-gradient(90deg,var(--success),transparent)}
.stat-card.blue::before{background:linear-gradient(90deg,var(--info),transparent)}
.stat-card.red::before{background:linear-gradient(90deg,var(--danger),transparent)}
.stat-icon{font-size:1.75rem;margin-bottom:.75rem}
.stat-value{font-size:1.8rem;font-weight:700;color:var(--text);line-height:1}
.stat-label{font-size:.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-top:.35rem}
.stat-change{font-size:.75rem;margin-top:.5rem}
.stat-change.up{color:var(--success)}
.stat-change.down{color:var(--danger)}

/* Tables */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:.875rem}
thead th{background:var(--surface2);padding:.75rem 1rem;text-align:left;font-size:.72rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);border-bottom:1px solid var(--border);white-space:nowrap}
tbody td{padding:.75rem 1rem;border-bottom:1px solid rgba(42,49,70,.6);color:var(--text);vertical-align:middle}
tbody tr:hover td{background:rgba(201,169,110,.03)}
tbody tr:last-child td{border-bottom:none}
.td-img{width:44px;height:44px;border-radius:6px;object-fit:cover;background:var(--surface2)}

/* Badges */
.badge{display:inline-flex;align-items:center;padding:.25rem .65rem;border-radius:9999px;font-size:.7rem;font-weight:600;letter-spacing:.04em}
.badge-green{background:rgba(16,185,129,.15);color:#10b981;border:1px solid rgba(16,185,129,.25)}
.badge-red{background:rgba(239,68,68,.15);color:#ef4444;border:1px solid rgba(239,68,68,.25)}
.badge-yellow{background:rgba(245,158,11,.15);color:#f59e0b;border:1px solid rgba(245,158,11,.25)}
.badge-blue{background:rgba(59,130,246,.15);color:#3b82f6;border:1px solid rgba(59,130,246,.25)}
.badge-gold{background:rgba(201,169,110,.15);color:var(--accent);border:1px solid rgba(201,169,110,.25)}
.badge-gray{background:rgba(148,163,184,.1);color:var(--muted);border:1px solid rgba(148,163,184,.2)}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;border-radius:6px;font-size:.82rem;font-weight:600;transition:all .15s;cursor:pointer;border:1px solid transparent}
.btn-primary{background:var(--accent);color:#0a0a0a;border-color:var(--accent)}
.btn-primary:hover{background:var(--accent-h)}
.btn-danger{background:rgba(239,68,68,.15);color:#ef4444;border-color:rgba(239,68,68,.3)}
.btn-danger:hover{background:rgba(239,68,68,.25)}
.btn-success{background:rgba(16,185,129,.15);color:#10b981;border-color:rgba(16,185,129,.3)}
.btn-success:hover{background:rgba(16,185,129,.25)}
.btn-sm{padding:.35rem .7rem;font-size:.75rem}
.btn-ghost{background:var(--surface2);color:var(--text);border-color:var(--border)}
.btn-ghost:hover{border-color:var(--accent);color:var(--accent)}

/* Forms */
.form-group{margin-bottom:1.25rem}
.form-label{display:block;font-size:.78rem;font-weight:500;color:var(--muted);margin-bottom:.4rem;text-transform:uppercase;letter-spacing:.06em}
.form-control{width:100%;padding:.65rem .9rem;background:var(--surface2);border:1.5px solid var(--border);border-radius:6px;color:var(--text);font-size:.875rem;font-family:inherit;transition:border-color .15s,box-shadow .15s;outline:none;resize:vertical}
.form-control:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(201,169,110,.15)}
select.form-control{cursor:pointer}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.form-row-3{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem}

/* Pagination */
.pagination{display:flex;align-items:center;gap:.4rem;padding:.75rem 1.25rem;border-top:1px solid var(--border)}
.page-btn{width:34px;height:34px;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);border-radius:6px;background:var(--surface2);color:var(--muted);font-size:.8rem;cursor:pointer;transition:all .15s}
.page-btn:hover,.page-btn.active{border-color:var(--accent);color:var(--accent);background:rgba(201,169,110,.1)}

/* Alert */
.alert{padding:.875rem 1rem;border-radius:8px;font-size:.875rem;margin-bottom:1rem;display:flex;align-items:flex-start;gap:.6rem}
.alert-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);color:#10b981}
.alert-danger{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#ef4444}
.alert-warning{background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.25);color:#f59e0b}
.alert-info{background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.25);color:#3b82f6}

/* Responsive */
@media(max-width:900px){
  .sidebar{transform:translateX(-100%);transition:transform .25s}
  .sidebar.open{transform:translateX(0)}
  .main-wrapper{margin-left:0}
  .form-row,.form-row-3{grid-template-columns:1fr}
  .stats-grid{grid-template-columns:1fr 1fr}
}
@media(max-width:480px){.stats-grid{grid-template-columns:1fr}}
</style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="adminSidebar">
  <div class="sidebar-brand">
    <div class="sidebar-logo">ELEVÉ</div>
    <div class="sidebar-sub">Admin Panel</div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">Main</div>
    <a href="index.php" class="nav-item <?= $currentPage === 'index' ? 'active' : '' ?>">
      <span class="icon">📊</span> Dashboard
    </a>

    <div class="nav-section">Catalog</div>
    <a href="products.php" class="nav-item <?= $currentPage === 'products' ? 'active' : '' ?>">
      <span class="icon">🛍️</span> Products
    </a>
    <a href="categories.php" class="nav-item <?= $currentPage === 'categories' ? 'active' : '' ?>">
      <span class="icon">📂</span> Categories
    </a>

    <div class="nav-section">Sales</div>
    <a href="orders.php" class="nav-item <?= $currentPage === 'orders' ? 'active' : '' ?>">
      <span class="icon">📦</span> Orders
      <?php
      $db2 = getDB();
      $newOrders = $db2->query("SELECT COUNT(*) FROM orders WHERE status = 'confirmed'")->fetchColumn();
      if ($newOrders > 0): ?>
        <span class="nav-badge"><?= $newOrders ?></span>
      <?php endif; ?>
    </a>
    <a href="coupons.php" class="nav-item <?= $currentPage === 'coupons' ? 'active' : '' ?>">
      <span class="icon">🏷️</span> Coupons
    </a>

    <div class="nav-section">Customers</div>
    <a href="users.php" class="nav-item <?= $currentPage === 'users' ? 'active' : '' ?>">
      <span class="icon">👥</span> Users
    </a>
    <a href="reviews.php" class="nav-item <?= $currentPage === 'reviews' ? 'active' : '' ?>">
      <span class="icon">⭐</span> Reviews
    </a>

    <div class="nav-section">Marketing</div>
    <a href="newsletter.php" class="nav-item <?= $currentPage === 'newsletter' ? 'active' : '' ?>">
      <span class="icon">📧</span> Newsletter
    </a>
    <a href="messages.php" class="nav-item <?= $currentPage === 'messages' ? 'active' : '' ?>">
      <span class="icon">💬</span> Messages
      <?php
      $newMsgs = $db2->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
      if ($newMsgs > 0): ?>
        <span class="nav-badge"><?= $newMsgs ?></span>
      <?php endif; ?>
    </a>

    <div class="nav-section">System</div>
    <a href="settings.php" class="nav-item <?= $currentPage === 'settings' ? 'active' : '' ?>">
      <span class="icon">⚙️</span> Settings
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="sidebar-avatar"><?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?></div>
      <div>
        <div class="sidebar-name"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
        <div class="sidebar-role">Administrator</div>
      </div>
    </div>
    <a href="logout.php" class="btn-logout">🚪 Logout</a>
  </div>
</aside>

<!-- Main Content Wrapper -->
<div class="main-wrapper">
  <div class="topbar">
    <div class="topbar-title"><?= $pageTitle ?? 'Dashboard' ?></div>
    <div class="topbar-right">
      <a href="../index.html" class="topbar-btn-ghost" target="_blank">🌐 View Store</a>
      <a href="products.php?action=add" class="topbar-btn">+ Add Product</a>
    </div>
  </div>
  <div class="content">

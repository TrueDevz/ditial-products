<?php
// ============================================================
// License Server — Admin Dashboard (Multi-Product)
// Access at: https://license.yourdomain.com/admin.php
// ============================================================
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/settings.php';

session_start();

// Handle login
if (isset($_POST['login'])) {
    if (($_POST['password'] ?? '') === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "Invalid password!";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Check authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8"><title>Login — License Admin</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <style>
        body { font-family: system-ui; background: #0f172a; color: #fff; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { background: #1e293b; padding: 40px; border-radius: 16px; border: 1px solid #334155; width: 100%; max-width: 380px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        h1 { font-size: 1.25rem; margin-bottom: 24px; text-align: center; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; }
        input { w-full; background: #0f172a; border: 1px solid #334155; padding: 12px; border-radius: 8px; color: #fff; width: 100%; margin-bottom: 16px; outline: none; }
        button { width: 100%; background: #6366f1; border: none; padding: 12px; border-radius: 8px; color: #fff; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        button:hover { background: #4f46e5; }
        .error { color: #ef4444; font-size: 0.8rem; margin-bottom: 16px; text-align: center; }
      </style>
    </head>
    <body>
      <div class="login-card">
        <h1>License Admin</h1>
        <?php if(isset($error)): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="POST">
          <input type="password" name="password" placeholder="Admin Password" required autofocus autocomplete="current-password">
          <button name="login">Sign In</button>
        </form>
      </div>
    </body>
    </html>
    <?php
    exit;
}

$db   = new LicenseDB();
$conn = $db->connect();

// Product filter
$filterProduct = $_GET['product'] ?? '';
$products      = PRODUCTS;

// Stats per product
$stats = [];
foreach (array_keys($products) as $pid) {
    $stats[$pid] = [
        'total' => $conn->prepare("SELECT COUNT(*) FROM activations WHERE product_id = ?"),
        'today' => $conn->prepare("SELECT COUNT(*) FROM activations WHERE product_id = ? AND DATE(activated_at) = CURDATE()"),
    ];
    $stats[$pid]['total']->execute([$pid]);
    $stats[$pid]['today']->execute([$pid]);
    $stats[$pid]['total'] = $stats[$pid]['total']->fetchColumn();
    $stats[$pid]['today'] = $stats[$pid]['today']->fetchColumn();
}

// Activations list (filtered or all)
$activationsQuery = $filterProduct
    ? $conn->prepare("SELECT * FROM activations WHERE product_id = ? ORDER BY activated_at DESC")
    : $conn->prepare("SELECT * FROM activations ORDER BY activated_at DESC");
if ($filterProduct) $activationsQuery->execute([$filterProduct]);
else $activationsQuery->execute();
$activations = $activationsQuery->fetchAll(PDO::FETCH_ASSOC);

// Recent logs
$logsQuery = $filterProduct
    ? $conn->prepare("SELECT * FROM activation_logs WHERE product_id = ? ORDER BY created_at DESC LIMIT 20")
    : $conn->prepare("SELECT * FROM activation_logs ORDER BY created_at DESC LIMIT 20");
if ($filterProduct) $logsQuery->execute([$filterProduct]);
else $logsQuery->execute();
$recentLogs = $logsQuery->fetchAll(PDO::FETCH_ASSOC);

// Handle revoke
if (isset($_POST['revoke']) && !empty($_POST['code']) && !empty($_POST['pid'])) {
    $stmt = $conn->prepare("DELETE FROM activations WHERE purchase_code = ? AND product_id = ?");
    $stmt->execute([trim($_POST['code']), trim($_POST['pid'])]);
    header('Location: admin.php?msg=revoked' . ($filterProduct ? "&product=$filterProduct" : ''));
    exit;
}
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>License Admin — Multi-Product</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin:0; padding:0; }
    body { font-family: system-ui, sans-serif; background: #0f172a; color: #e2e8f0; min-height:100vh; }
    .top { background: #1e293b; border-bottom: 1px solid #334155; padding: 16px 32px;
      display:flex; align-items:center; justify-content:space-between; }
    .top h1 { font-size:1.1rem; font-weight:700; }
    .top .filter { display:flex; gap:8px; }
    .top .filter a { padding:6px 14px; border-radius:8px; font-size:0.8rem; font-weight:600;
      text-decoration:none; border:1px solid #334155; color:#94a3b8; }
    .top .filter a.active, .top .filter a:hover { background:#6366f1; color:#fff; border-color:#6366f1; }
    .main { padding: 32px; max-width: 1200px; margin:0 auto; }
    .stats { display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:16px; margin-bottom:32px; }
    .stat { background:#1e293b; border:1px solid #334155; border-radius:12px; padding:20px; }
    .stat .product-name { font-size:0.75rem; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px; }
    .stat .nums { display:flex; gap:20px; }
    .stat .num { }
    .stat .num span { font-size:1.8rem; font-weight:800; color:#6366f1; display:block; }
    .stat .num small { font-size:0.75rem; color:#64748b; }
    h2 { font-size:0.85rem; font-weight:700; margin-bottom:12px; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; }
    table { width:100%; border-collapse:collapse; font-size:0.85rem; margin-bottom:32px; }
    th { background:#1e293b; color:#94a3b8; font-weight:600; padding:10px 14px; text-align:left; border:1px solid #334155; }
    td { padding:10px 14px; border:1px solid #1e293b; color:#cbd5e1; }
    tr:nth-child(even) td { background:#111827; }
    .badge { display:inline-block; padding:2px 8px; border-radius:999px; font-size:0.72rem; font-weight:700;
      background:#312e81; color:#a5b4fc; }
    .btn-revoke { background:#ef4444; color:#fff; border:none; padding:5px 12px; border-radius:6px;
      font-size:0.78rem; cursor:pointer; font-family:inherit; }
    .msg { background:#052e16; border:1px solid #22c55e; color:#86efac; padding:12px 16px;
      border-radius:8px; margin-bottom:20px; font-size:0.875rem; }
  </style>
</head>
<body>
<div class="top">
  <h1>📰 License Server Admin</h1>
  <div class="filter">
    <a href="admin.php" class="<?= !$filterProduct ? 'active' : '' ?>">All Products</a>
    <?php foreach ($products as $pid => $p): ?>
      <a href="admin.php?product=<?= $pid ?>" class="<?= $filterProduct === $pid ? 'active' : '' ?>">
        <?= htmlspecialchars($p['name']) ?>
      </a>
    <?php endforeach; ?>
    <a href="admin.php?logout=1" style="background:#ef4444;color:#fff;border-color:#ef4444">Logout</a>
  </div>
</div>
<div class="main">

  <?php if ($msg === 'revoked'): ?>
    <div class="msg">✅ License revoked successfully.</div>
  <?php endif; ?>

  <!-- Per-product stats -->
  <div class="stats">
    <?php foreach ($products as $pid => $p): ?>
      <div class="stat">
        <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="nums">
          <div class="num"><span><?= $stats[$pid]['total'] ?></span><small>Total</small></div>
          <div class="num"><span><?= $stats[$pid]['today'] ?></span><small>Today</small></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Activations table -->
  <h2>Activations <?= $filterProduct ? '— ' . htmlspecialchars($products[$filterProduct]['name']) : '(All Products)' ?></h2>
  <table>
    <thead>
      <tr>
        <th>Product</th><th>Purchase Code</th><th>Buyer</th><th>Domain</th><th>Activated</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($activations as $row): ?>
      <tr>
        <td><span class="badge"><?= htmlspecialchars($row['product_id']) ?></span></td>
        <td><code><?= htmlspecialchars(substr($row['purchase_code'],0,12)) ?>…</code></td>
        <td><?= htmlspecialchars($row['buyer_username'] ?? '—') ?></td>
        <td><?= htmlspecialchars($row['domain']) ?></td>
        <td><?= $row['activated_at'] ?></td>
        <td>
          <form method="POST" onsubmit="return confirm('Revoke this license?')">
            <input type="hidden" name="code" value="<?= htmlspecialchars($row['purchase_code']) ?>"/>
            <input type="hidden" name="pid"  value="<?= htmlspecialchars($row['product_id']) ?>"/>
            <button class="btn-revoke" name="revoke" value="1">Revoke</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($activations)): ?>
      <tr><td colspan="6" style="text-align:center;color:#475569;padding:24px">No activations yet.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <!-- Activity log -->
  <h2>Recent Activity</h2>
  <table>
    <thead><tr><th>Time</th><th>Product</th><th>Code</th><th>Domain</th><th>Action</th><th>IP</th></tr></thead>
    <tbody>
    <?php foreach ($recentLogs as $log): ?>
      <tr>
        <td><?= $log['created_at'] ?></td>
        <td><span class="badge"><?= htmlspecialchars($log['product_id']) ?></span></td>
        <td><code><?= htmlspecialchars(substr($log['purchase_code'],0,8)) ?>…</code></td>
        <td><?= htmlspecialchars($log['domain']) ?></td>
        <td><?= $log['action'] ?></td>
        <td><?= htmlspecialchars($log['ip_address'] ?? '—') ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

</div>
</body>
</html>

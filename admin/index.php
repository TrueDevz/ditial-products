<?php
// admin/index.php
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /digitalProducts/login.php');
    exit;
}

// Fetch Stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$pending_products = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'pending'")->fetchColumn();
$total_sales = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE payment_status = 'completed'")->fetchColumn() ?: 0;

// Recent Orders
$recent_orders = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <div style="display: flex; gap: 3rem;">
        <?php include 'sidebar.php'; ?>

        <main style="flex: 1;">
            <h1 style="font-weight: 800; margin-bottom: 2.5rem;">Dashboard Overview</h1>

            <!-- Stats Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
                <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow); border-left: 4px solid var(--primary);">
                    <p style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Total Sales</p>
                    <h2 style="font-size: 2rem; font-weight: 800;"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($total_sales, 2); ?></h2>
                </div>
                <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow); border-left: 4px solid var(--secondary);">
                    <p style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Active Users</p>
                    <h2 style="font-size: 2rem; font-weight: 800;"><?php echo $total_users; ?></h2>
                </div>
                <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow); border-left: 4px solid #f59e0b;">
                    <p style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Total Items</p>
                    <h2 style="font-size: 2rem; font-weight: 800;"><?php echo $total_products; ?></h2>
                </div>
                <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow); border-left: 4px solid #ef4444;">
                    <p style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Pending items</p>
                    <h2 style="font-size: 2rem; font-weight: 800;"><?php echo $pending_products; ?></h2>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div style="background: white; padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h3 style="margin: 0;">Recent Transactions</h3>
                    <a href="/digitalProducts/admin/orders.php" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.875rem;">View All Orders</a>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid var(--light);">
                            <th style="padding: 1rem 0;">Order ID</th>
                            <th style="padding: 1rem 0;">Customer</th>
                            <th style="padding: 1rem 0;">Amount</th>
                            <th style="padding: 1rem 0;">Status</th>
                            <th style="padding: 1rem 0;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr><td colspan="5" style="padding: 2rem; text-align: center; color: var(--gray);">No orders found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr style="border-bottom: 1px solid var(--light);">
                                    <td style="padding: 1rem 0; font-weight: 600;">#<?php echo $order['id']; ?></td>
                                    <td style="padding: 1rem 0;"><?php echo $order['username']; ?></td>
                                    <td style="padding: 1rem 0; font-weight: 700;"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td style="padding: 1rem 0;">
                                        <span style="font-size: 0.75rem; font-weight: 700; color: <?php echo $order['payment_status'] === 'completed' ? '#16a34a' : '#ca8a04'; ?>;">
                                            <?php echo strtoupper($order['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem 0; color: var(--gray); font-size: 0.875rem;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

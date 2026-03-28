<?php
// admin/orders.php
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /digitalProducts/login.php');
    exit;
}

// Fetch all orders
$stmt = $pdo->query("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
$orders = $stmt->fetchAll();
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <div style="display: flex; gap: 3rem;">
        <?php include 'sidebar.php'; ?>

        <main style="flex: 1;">
            <h1 style="font-weight: 800; margin-bottom: 2.5rem;">Order Management</h1>

            <div style="background: white; padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--light); text-align: left;">
                            <th style="padding: 1rem;">Order ID</th>
                            <th style="padding: 1rem;">Customer</th>
                            <th style="padding: 1rem;">Amount</th>
                            <th style="padding: 1rem;">Status</th>
                            <th style="padding: 1rem;">Transaction ID</th>
                            <th style="padding: 1rem;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="6" style="padding: 2rem; text-align: center; color: var(--gray);">No orders have been placed yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 1.5rem 1rem; font-weight: 700;">#<?php echo $order['id']; ?></td>
                                    <td style="padding: 1.5rem 1rem;">
                                        <div style="font-weight: 600;"><?php echo $order['username']; ?></div>
                                        <div style="font-size: 0.75rem; color: var(--gray);"><?php echo $order['email']; ?></div>
                                    </td>
                                    <td style="padding: 1.5rem 1rem; font-weight: 800; color: var(--primary);"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td style="padding: 1.5rem 1rem;">
                                        <?php 
                                            $c = ($order['payment_status'] === 'completed') ? '#16a34a' : (($order['payment_status'] === 'pending') ? '#ca8a04' : '#dc2626');
                                            $bg = ($order['payment_status'] === 'completed') ? '#dcfce7' : (($order['payment_status'] === 'pending') ? '#fef9c3' : '#fee2e2');
                                        ?>
                                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: <?php echo $bg; ?>; color: <?php echo $c; ?>; text-transform: uppercase;">
                                            <?php echo $order['payment_status']; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1.5rem 1rem; color: var(--gray); font-size: 0.8125rem;">
                                        <?php echo $order['razorpay_payment_id'] ?: 'N/A'; ?>
                                        <div style="font-size: 0.7rem;">ORID: <?php echo $order['razorpay_order_id']; ?></div>
                                    </td>
                                    <td style="padding: 1.5rem 1rem; color: var(--gray); font-size: 0.875rem;">
                                        <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                        <div style="font-size: 0.75rem;"><?php echo date('h:i A', strtotime($order['created_at'])); ?></div>
                                    </td>
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

<?php
// dashboard/index.php
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /digitalProducts/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// If buyer: Fetch purchases
$purchases = [];
if ($role === 'user' || $role === 'seller') {
    $stmt = $pdo->prepare("SELECT p.*, o.created_at as purchase_date, oi.purchase_code FROM order_items oi 
                           JOIN orders o ON oi.order_id = o.id 
                           JOIN products p ON oi.product_id = p.id 
                           WHERE o.user_id = ? AND o.payment_status = 'completed'");
    $stmt->execute([$user_id]);
    $purchases = $stmt->fetchAll();
}

// If seller: Fetch my products and sales
$my_products = [];
if ($role === 'seller') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $my_products = $stmt->fetchAll();
}
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
        <!-- Sidebar -->
        <aside style="width: 250px; min-width: 250px;">
            <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow); text-align: center; margin-bottom: 2rem;">
                <img src="/digitalProducts/assets/images/<?php echo $user['avatar']; ?>" style="width: 100px; height: 100px; border-radius: 50%; border: 3px solid var(--primary); margin-bottom: 1rem; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo $user['username']; ?>&background=random';">
                <h3 style="font-weight: 700;"><?php echo $user['username']; ?></h3>
                <p style="color: var(--gray); font-size: 0.875rem; text-transform: capitalize;"><?php echo $user['role']; ?></p>
            </div>
            
            <div style="background: white; padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                <ul style="list-style: none;">
                    <li style="margin-bottom: 1rem;"><a href="<?php echo ($_SESSION['role'] === 'admin') ? '/digitalProducts/admin/index.php' : '/digitalProducts/dashboard/index.php'; ?>" style="color: var(--primary); font-weight: 600;"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <?php if ($role === 'seller'): ?>
                        <li style="margin-bottom: 1rem;"><a href="/digitalProducts/dashboard/upload.php" style="color: var(--gray);"><i class="bi bi-cloud-upload"></i> Upload Item</a></li>
                    <?php endif; ?>
                    <li style="margin-bottom: 1rem;"><a href="#" style="color: var(--gray);"><i class="bi bi-person"></i> My Profile</a></li>
                    <li style="margin-bottom: 1rem;"><a href="/digitalProducts/logout.php" style="color: #ef4444;"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main style="flex: 1; min-width: 400px;">
            <?php if (isset($_SESSION['payment_success'])): ?>
                <div style="background: #dcfce7; color: #15803d; padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                    <i class="bi bi-check-circle-fill" style="font-size: 1.5rem;"></i>
                    <?php echo $_SESSION['payment_success']; unset($_SESSION['payment_success']); ?>
                </div>
            <?php endif; ?>

            <h2 style="font-weight: 800; margin-bottom: 2rem;">My Dashboard</h2>

            <!-- Stats Bar -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
                <div style="background: white; padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                    <p style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.5rem;">Total Purchases</p>
                    <h3 style="font-size: 1.75rem; font-weight: 800;"><?php echo count($purchases); ?></h3>
                </div>
                <?php if ($role === 'seller'): ?>
                    <div style="background: white; padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                        <p style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.5rem;">My Products</p>
                        <h3 style="font-size: 1.75rem; font-weight: 800;"><?php echo count($my_products); ?></h3>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Purchases Section -->
            <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 3rem;">
                <h3 style="margin-bottom: 1.5rem;">My Purchases (Downloads)</h3>
                <?php if (empty($purchases)): ?>
                    <p style="color: var(--gray);">You haven't purchased any items yet.</p>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 2px solid var(--light);">
                                <th style="padding: 1rem 0;">Product</th>
                                <th style="padding: 1rem 0;">Purchase Code</th>
                                <th style="padding: 1rem 0;">Date</th>
                                <th style="padding: 1rem 0; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purchases as $p): ?>
                                <tr style="border-bottom: 1px solid var(--light);">
                                    <td style="padding: 1rem 0; font-weight: 600;"><?php echo $p['name']; ?></td>
                                    <td style="padding: 1rem 0;">
                                        <code style="background: var(--light); padding: 4px 8px; border-radius: 4px; font-size: 0.8125rem; font-family: monospace;"><?php echo $p['purchase_code']; ?></code>
                                    </td>
                                    <td style="padding: 1rem 0; color: var(--gray); font-size: 0.875rem;"><?php echo date('M d, Y', strtotime($p['purchase_date'])); ?></td>
                                    <td style="padding: 1rem 0; text-align: right;">
                                        <a href="/digitalProducts/uploads/files/<?php echo $p['main_file']; ?>" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.875rem;" download>
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Seller Section (if applicable) -->
            <?php if ($role === 'seller'): ?>
                <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="margin: 0;">My Listed Products</h3>
                        <a href="/digitalProducts/dashboard/upload.php" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">+ Upload New</a>
                    </div>
                    <?php if (empty($my_products)): ?>
                        <p style="color: var(--gray);">You haven't uploaded any items yet.</p>
                    <?php else: ?>
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 2px solid var(--light);">
                                    <th style="padding: 1rem 0;">Product</th>
                                    <th style="padding: 1rem 0;">Status</th>
                                    <th style="padding: 1rem 0;">Sales</th>
                                    <th style="padding: 1rem 0; text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($my_products as $p): ?>
                                    <tr style="border-bottom: 1px solid var(--light);">
                                        <td style="padding: 1rem 0; font-weight: 600;"><?php echo $p['name']; ?></td>
                                        <td style="padding: 1rem 0;">
                                            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;"><?php echo $p['status']; ?></span>
                                        </td>
                                        <td style="padding: 1rem 0;"><?php echo $p['sales']; ?></td>
                                        <td style="padding: 1rem 0; text-align: right;">
                                            <a href="/digitalProducts/product.php?slug=<?php echo $p['slug']; ?>" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.875rem;">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

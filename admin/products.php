<?php
// admin/products.php
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// Handle Approval/Rejection/Delete
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] === 'delete') {
        // Optional: Delete physical files too
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        $status = ($_GET['action'] === 'approve') ? 'approved' : 'rejected';
        $stmt = $pdo->prepare("UPDATE products SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
    header('Location: ' . BASE_URL . '/admin/products.php');
    exit;
}

// Fetch pending products
$stmt = $pdo->query("SELECT p.*, u.username as seller_name, c.name as category_name FROM products p 
                     JOIN users u ON p.seller_id = u.id 
                     JOIN categories c ON p.category_id = c.id 
                     ORDER BY p.created_at DESC");
$products = $stmt->fetchAll();
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <div style="display: flex; gap: 3rem;">
        <?php include 'sidebar.php'; ?>

        <main style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1 style="font-weight: 800; margin: 0;">Product Management</h1>
                <a href="<?php echo BASE_URL; ?>/dashboard/upload.php" class="btn btn-primary">+ Add New Product</a>
            </div>

            <div style="background: white; padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--shadow);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--light); text-align: left;">
                    <th style="padding: 1rem;">Product</th>
                    <th style="padding: 1rem;">Seller</th>
                    <th style="padding: 1rem;">Category</th>
                    <th style="padding: 1rem;">Price</th>
                    <th style="padding: 1rem;">Status</th>
                    <th style="padding: 1rem; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem;">
                            <div style="display: flex; gap: 1rem; align-items: center;">
                                <img src="<?php echo BASE_URL; ?>/uploads/previews/<?php echo $p['preview_image']; ?>" style="width: 50px; height: 40px; border-radius: 4px; object-fit: cover;">
                                <span style="font-weight: 600;"><?php echo $p['name']; ?></span>
                            </div>
                        </td>
                        <td style="padding: 1rem;"><?php echo $p['seller_name']; ?></td>
                        <td style="padding: 1rem;"><?php echo $p['category_name']; ?></td>
                        <td style="padding: 1rem; font-weight: 700;"><?php echo APP_CURRENCY_SYMBOL; ?> <?php echo number_format($p['price'], 2); ?></td>
                        <td style="padding: 1rem;">
                            <?php 
                                $status_color = ($p['status'] === 'approved') ? '#16a34a' : (($p['status'] === 'pending') ? '#ca8a04' : '#dc2626');
                                $status_bg = ($p['status'] === 'approved') ? '#dcfce7' : (($p['status'] === 'pending') ? '#fef9c3' : '#fee2e2');
                            ?>
                            <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: <?php echo $status_bg; ?>; color: <?php echo $status_color; ?>; text-transform: uppercase;">
                                <?php echo $p['status']; ?>
                            </span>
                        </td>
                        <td style="padding: 1rem; text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <?php if ($p['status'] === 'pending'): ?>
                                <a href="?action=approve&id=<?php echo $p['id']; ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background: #16a34a;"><i class="bi bi-check-lg"></i></a>
                                <a href="?action=reject&id=<?php echo $p['id']; ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background: #dc2626;"><i class="bi bi-x-lg"></i></a>
                            <?php endif; ?>
                            <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; color: var(--primary);"><i class="bi bi-pencil"></i></a>
                            <a href="?action=delete&id=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; color: #ef4444;"><i class="bi bi-trash"></i></a>
                            <a href="<?php echo BASE_URL; ?>/product.php?slug=<?php echo $p['slug']; ?>" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

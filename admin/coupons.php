<?php
// admin/coupons.php
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /digitalProducts/login.php');
    exit;
}

$error = '';
$success = '';

// Handle Coupon Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_coupon'])) {
    $code = strtoupper(trim($_POST['code']));
    $discount = $_POST['discount_percent'];
    $expiry = $_POST['expiry_date'];

    if (empty($code) || empty($discount) || empty($expiry)) {
        $error = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO coupons (code, discount_percent, expiry_date) VALUES (?, ?, ?)");
            $stmt->execute([$code, $discount, $expiry]);
            $success = "Coupon created successfully!";
        } catch (PDOException $e) {
            $error = "Coupon code already exists or database error.";
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM coupons WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $success = "Coupon deleted successfully!";
}

// Fetch all coupons
$coupons = $pdo->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll();
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <div style="display: flex; gap: 3rem;">
        <?php include 'sidebar.php'; ?>

        <main style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
                <h1 style="font-weight: 800; margin: 0;">Coupon Management</h1>
            </div>

            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem;"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="background: #dcfce7; color: #15803d; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem;"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Create Coupon Form -->
            <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 3rem;">
                <h3 style="margin-bottom: 1.5rem;">Create New Coupon</h3>
                <form action="" method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: flex-end;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Coupon Code</label>
                        <input type="text" name="code" placeholder="E.g. SUMMER50" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Discount (%)</label>
                        <input type="number" name="discount_percent" min="1" max="100" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Expiry Date</label>
                        <input type="date" name="expiry_date" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius);" required>
                    </div>
                    <button type="submit" name="create_coupon" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">Create</button>
                </form>
            </div>

            <!-- Coupons List -->
            <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                <h3 style="margin-bottom: 1.5rem;">Existing Coupons</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid var(--light);">
                            <th style="padding: 1rem 0;">Code</th>
                            <th style="padding: 1rem 0;">Discount</th>
                            <th style="padding: 1rem 0;">Expiry</th>
                            <th style="padding: 1rem 0;">Status</th>
                            <th style="padding: 1rem 0; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coupons as $c): ?>
                            <tr style="border-bottom: 1px solid var(--light);">
                                <td style="padding: 1rem 0; font-weight: 700; color: var(--primary);"><?php echo $c['code']; ?></td>
                                <td style="padding: 1rem 0;"><?php echo $c['discount_percent']; ?>%</td>
                                <td style="padding: 1rem 0; color: var(--gray);"><?php echo date('M d, Y', strtotime($c['expiry_date'])); ?></td>
                                <td style="padding: 1rem 0;">
                                    <?php 
                                    $is_expired = strtotime($c['expiry_date']) < time();
                                    if ($is_expired) echo '<span style="color: #ef4444; font-size: 0.75rem; font-weight: 700;">EXPIRED</span>';
                                    else echo '<span style="color: #16a34a; font-size: 0.75rem; font-weight: 700;">ACTIVE</span>';
                                    ?>
                                </td>
                                <td style="padding: 1rem 0; text-align: right;">
                                    <a href="?delete=<?php echo $c['id']; ?>" onclick="return confirm('Are you sure?')" style="color: #ef4444; font-size: 1.25rem;"><i class="bi bi-trash"></i></a>
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

<?php
// admin/edit_product.php
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /digitalProducts/login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: products.php');
    exit;
}

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

$error = '';
$success = '';

if (isset($_POST['update_product'])) {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $status = $_POST['status'];
    $description = trim($_POST['description']);

    if (empty($name) || empty($price)) {
        $error = "Name and price are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, category_id = ?, status = ?, description = ? WHERE id = ?");
        if ($stmt->execute([$name, $price, $category_id, $status, $description, $id])) {
            $success = "Product updated successfully.";
            // Refresh product data
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();
        } else {
            $error = "Failed to update product.";
        }
    }
}
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <div style="display: flex; gap: 3rem;">
        <?php include 'sidebar.php'; ?>

        <main style="flex: 1;">
            <div style="margin-bottom: 2rem;">
                <a href="products.php" style="color: var(--gray); text-decoration: none;"><i class="bi bi-arrow-left"></i> Back to Products</a>
                <h1 style="font-weight: 800; margin-top: 1rem;">Edit Product: <?php echo htmlspecialchars($product['name']); ?></h1>
            </div>

            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem;"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div style="background: #dcfce7; color: #15803d; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem;"><?php echo $success; ?></div>
            <?php endif; ?>

            <div style="background: white; padding: 3rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                <form action="" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Product Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;" required>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Price (<?php echo CURRENCY_SYMBOL; ?>)</label>
                            <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Category</label>
                            <select name="category_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Status</label>
                            <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;">
                                <option value="pending" <?php echo ($product['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo ($product['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo ($product['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Description</label>
                        <textarea name="description" rows="6" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <div style="margin-bottom: 3rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 1rem;">Current Preview Image</label>
                        <img src="/digitalProducts/uploads/previews/<?php echo $product['preview_image']; ?>" style="max-width: 300px; border-radius: var(--radius); border: 1px solid var(--border);">
                    </div>

                    <button type="submit" name="update_product" class="btn btn-primary" style="padding: 1rem 3rem;">Update Product</button>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

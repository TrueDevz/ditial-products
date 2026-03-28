<?php
// dashboard/upload.php
include '../includes/header.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'seller' && $_SESSION['role'] !== 'admin')) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$error = '';
$success = '';

// Fetch categories for the select dropdown
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'];
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $slug = generateSlug($name);

    // File handling
    $preview_image = $_FILES['preview_image'];
    $main_file = $_FILES['main_file'];

    if (empty($name) || empty($description) || empty($price) || empty($preview_image['name']) || empty($main_file['name'])) {
        $error = 'All fields and files are required.';
    } else {
        $preview_ext = pathinfo($preview_image['name'], PATHINFO_EXTENSION);
        $preview_name = time() . '_preview.' . $preview_ext;
        $main_ext = pathinfo($main_file['name'], PATHINFO_EXTENSION);
        $main_name = time() . '_file.' . $main_ext;

        if (move_uploaded_file($preview_image['tmp_name'], '../uploads/previews/' . $preview_name) &&
            move_uploaded_file($main_file['tmp_name'], '../uploads/files/' . $main_name)) {
            
            $stmt = $pdo->prepare("INSERT INTO products (seller_id, category_id, name, slug, description, price, preview_image, main_file) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$_SESSION['user_id'], $category_id, $name, $slug, $description, $price, $preview_name, $main_name])) {
                $success = 'Product uploaded successfully! It will be visible after admin approval.';
            } else {
                $error = 'Failed to save product to database.';
            }
        } else {
            $error = 'Failed to upload files.';
        }
    }
}
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <div style="display: flex; gap: 2rem;">
        <!-- Sidebar -->
        <aside style="width: 250px;">
            <div style="background: white; padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.1rem;">Seller Menu</h3>
                <ul style="list-style: none;">
                    <li style="margin-bottom: 1rem;"><a href="<?php echo ($_SESSION['role'] === 'admin') ? BASE_URL . '/admin/index.php' : BASE_URL . '/dashboard/index.php'; ?>" style="color: var(--gray);"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li style="margin-bottom: 1rem;"><a href="<?php echo BASE_URL; ?>/dashboard/upload.php" style="color: var(--primary); font-weight: 600;"><i class="bi bi-cloud-upload"></i> Upload Item</a></li>
                    <li style="margin-bottom: 1rem;"><a href="#" style="color: var(--gray);"><i class="bi bi-box"></i> My Products</a></li>
                    <li style="margin-bottom: 1rem;"><a href="#" style="color: var(--gray);"><i class="bi bi-currency-dollar"></i> Sales Settings</a></li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main style="flex: 1;">
            <div style="background: white; padding: 3rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                <h2 style="font-weight: 800; margin-bottom: 0.5rem;">Upload New Product</h2>
                <p style="color: var(--gray); margin-bottom: 2rem;">Fill in the details below to list your product in the marketplace.</p>

                <?php if ($error): ?>
                    <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem;"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div style="background: #dcfce7; color: #15803d; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem;"><?php echo $success; ?></div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Product Name</label>
                            <input type="text" name="name" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius);" required>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Category</label>
                            <select name="category_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius);" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Description</label>
                        <textarea name="description" rows="6" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius);" required></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Price (<?php echo APP_CURRENCY_SYMBOL; ?>)</label>
                            <input type="number" step="0.01" name="price" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius);" required>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Preview Image</label>
                            <input type="file" name="preview_image" accept="image/*" style="width: 100%;" required>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Main Product File (Zip)</label>
                            <input type="file" name="main_file" style="width: 100%;" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">Upload Product for Review</button>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

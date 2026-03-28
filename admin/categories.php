<?php
// admin/categories.php
include '../includes/header.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$error = '';
$success = '';

// Handle Add/Edit Category
if (isset($_POST['save_category'])) {
    $name = trim($_POST['name']);
    $icon = trim($_POST['icon']);
    $description = trim($_POST['description']);
    $slug = generateSlug($name);
    $id = $_POST['id'] ?? null;

    if (empty($name)) {
        $error = 'Category name is required.';
    } else {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, icon = ?, description = ? WHERE id = ?");
            if ($stmt->execute([$name, $slug, $icon, $description, $id])) {
                $success = 'Category updated successfully.';
            } else {
                $error = 'Failed to update category.';
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, description) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $slug, $icon, $description])) {
                $success = 'Category added successfully.';
            } else {
                $error = 'Failed to add category.';
            }
        }
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
}

// Fetch all categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <div style="display: flex; gap: 3rem;">
        <?php include 'sidebar.php'; ?>

        <main style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
                <h1 style="font-weight: 800; margin: 0;">Category Management</h1>
                <button onclick="openAddModal()" class="btn btn-primary">+ Add New Category</button>
            </div>

            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem;"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div style="background: #dcfce7; color: #15803d; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem;"><?php echo $success; ?></div>
            <?php endif; ?>

            <div style="background: white; padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--light); text-align: left;">
                            <th style="padding: 1rem;">Icon</th>
                            <th style="padding: 1rem;">Name</th>
                            <th style="padding: 1rem;">Slug</th>
                            <th style="padding: 1rem;">Description</th>
                            <th style="padding: 1rem; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 1rem; font-size: 1.5rem;"><i class="bi <?php echo $cat['icon']; ?>"></i></td>
                                <td style="padding: 1rem; font-weight: 600;"><?php echo $cat['name']; ?></td>
                                <td style="padding: 1rem; color: var(--gray); font-size: 0.875rem;"><?php echo $cat['slug']; ?></td>
                                <td style="padding: 1rem; color: var(--gray); font-size: 0.875rem;"><?php echo $cat['description']; ?></td>
                                <td style="padding: 1rem; text-align: right; display: flex; gap: 1rem; justify-content: flex-end;">
                                    <a href="javascript:void(0)" onclick="editCategory(<?php echo htmlspecialchars(json_encode($cat)); ?>)" style="color: var(--primary); font-size: 1.25rem;"><i class="bi bi-pencil-square"></i></a>
                                    <a href="?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Are you sure? All products in this category will also be deleted.')" style="color: #ef4444; font-size: 1.25rem;"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<div id="cat-modal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div style="background: white; padding: 3rem; border-radius: var(--radius); width: 100%; max-width: 500px; position: relative;">
        <button onclick="document.getElementById('cat-modal').style.display='none'" style="position: absolute; right: 1.5rem; top: 1.5rem; background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        <h2 id="modal-title" style="margin-bottom: 2rem; font-weight: 800;">Add New Category</h2>
        <form action="" method="POST">
            <input type="hidden" name="id" id="cat-id">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Category Name</label>
                <input type="text" name="name" id="cat-name" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;" required placeholder="e.g. PHP Scripts">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Icon Class (Bootstrap Icons)</label>
                <input type="text" name="icon" id="cat-icon" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;" placeholder="e.g. bi-code-slash">
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Description</label>
                <textarea name="description" id="cat-desc" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;"></textarea>
            </div>
            <button type="submit" name="save_category" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">Save Category</button>
        </form>
    </div>
</div>

<script>
function editCategory(cat) {
    document.getElementById('modal-title').innerText = 'Edit Category';
    document.getElementById('cat-id').value = cat.id;
    document.getElementById('cat-name').value = cat.name;
    document.getElementById('cat-icon').value = cat.icon;
    document.getElementById('cat-desc').value = cat.description;
    document.getElementById('cat-modal').style.display = 'flex';
}

function openAddModal() {
    document.getElementById('modal-title').innerText = 'Add New Category';
    document.getElementById('cat-id').value = '';
    document.getElementById('cat-name').value = '';
    document.getElementById('cat-icon').value = '';
    document.getElementById('cat-desc').value = '';
    document.getElementById('cat-modal').style.display = 'flex';
}
</script>

<?php include '../includes/footer.php'; ?>

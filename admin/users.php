<?php
// admin/users.php
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /digitalProducts/login.php');
    exit;
}

// Handle Role Change
if (isset($_GET['change_role']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $role = $_GET['change_role'];
    if (in_array($role, ['user', 'seller', 'admin'])) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $id]);
    }
    header('Location: /digitalProducts/admin/users.php');
    exit;
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND id != ?");
    $stmt->execute([$id, $_SESSION['user_id']]); // Don't delete self
    header('Location: /digitalProducts/admin/users.php');
    exit;
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <div style="display: flex; gap: 3rem;">
        <?php include 'sidebar.php'; ?>

        <main style="flex: 1;">
            <h1 style="font-weight: 800; margin-bottom: 2.5rem;">User Management</h1>

            <div style="background: white; padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--light); text-align: left;">
                            <th style="padding: 1rem;">User</th>
                            <th style="padding: 1rem;">Email</th>
                            <th style="padding: 1rem;">Role</th>
                            <th style="padding: 1rem;">Joined</th>
                            <th style="padding: 1rem; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 1rem;">
                                    <div style="display: flex; gap: 1rem; align-items: center;">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo $user['username']; ?>&background=random" style="width: 40px; height: 40px; border-radius: 50%;">
                                        <span style="font-weight: 600;"><?php echo $user['username']; ?></span>
                                    </div>
                                </td>
                                <td style="padding: 1rem; color: var(--gray);"><?php echo $user['email']; ?></td>
                                <td style="padding: 1rem;">
                                    <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: <?php echo $user['role'] === 'admin' ? '#ef4444' : ($user['role'] === 'seller' ? '#3b82f6' : '#64748b'); ?>;">
                                        <?php echo $user['role']; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: var(--gray); font-size: 0.875rem;"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td style="padding: 1rem; text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <div style="position: relative; display: inline-block;">
                                        <button class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'block' ? 'none' : 'block'">Role <i class="bi bi-chevron-down"></i></button>
                                        <div style="display: none; position: absolute; right: 0; background: white; box-shadow: var(--shadow-lg); border-radius: 4px; z-index: 100; min-width: 120px; text-align: left;">
                                            <a href="?change_role=user&id=<?php echo $user['id']; ?>" style="display: block; padding: 0.75rem; font-size: 0.875rem;">Set as Buyer</a>
                                            <a href="?change_role=seller&id=<?php echo $user['id']; ?>" style="display: block; padding: 0.75rem; font-size: 0.875rem;">Set as Seller</a>
                                            <a href="?change_role=admin&id=<?php echo $user['id']; ?>" style="display: block; padding: 0.75rem; font-size: 0.875rem;">Set as Admin</a>
                                        </div>
                                    </div>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="?delete=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')" style="color: #ef4444; padding: 0.5rem;"><i class="bi bi-trash"></i></a>
                                    <?php endif; ?>
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

<?php
// admin/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside style="width: 280px; flex-shrink: 0;">
    <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow); position: sticky; top: 100px;">
        <h3 style="margin-bottom: 2rem; font-size: 1.25rem; font-weight: 800; color: var(--dark);">Admin Panel</h3>
        <ul style="list-style: none;">
            <li style="margin-bottom: 1rem;">
                <a href="<?php echo BASE_URL; ?>/admin/index.php" style="display: flex; align-items: center; gap: 0.75rem; <?php echo $current_page == 'index.php' ? 'color: var(--primary); font-weight: 600;' : 'color: var(--gray);'; ?>">
                    <i class="bi bi-speedometer2"></i> Overview
                </a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="<?php echo BASE_URL; ?>/admin/products.php" style="display: flex; align-items: center; gap: 0.75rem; <?php echo $current_page == 'products.php' ? 'color: var(--primary); font-weight: 600;' : 'color: var(--gray);'; ?>">
                    <i class="bi bi-box-seam"></i> Products
                </a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="<?php echo BASE_URL; ?>/admin/categories.php" style="display: flex; align-items: center; gap: 0.75rem; <?php echo $current_page == 'categories.php' ? 'color: var(--primary); font-weight: 600;' : 'color: var(--gray);'; ?>">
                    <i class="bi bi-tags"></i> Categories
                </a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="<?php echo BASE_URL; ?>/admin/users.php" style="display: flex; align-items: center; gap: 0.75rem; <?php echo $current_page == 'users.php' ? 'color: var(--primary); font-weight: 600;' : 'color: var(--gray);'; ?>">
                    <i class="bi bi-people"></i> Users
                </a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="<?php echo BASE_URL; ?>/admin/coupons.php" style="display: flex; align-items: center; gap: 0.75rem; <?php echo $current_page == 'coupons.php' ? 'color: var(--primary); font-weight: 600;' : 'color: var(--gray);'; ?>">
                    <i class="bi bi-ticket-perforated"></i> Coupons
                </a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="<?php echo BASE_URL; ?>/admin/settings.php" style="display: flex; align-items: center; gap: 0.75rem; <?php echo $current_page == 'settings.php' ? 'color: var(--primary); font-weight: 600;' : 'color: var(--gray);'; ?>">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </li>
            <li style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                <a href="<?php echo BASE_URL; ?>/logout.php" style="display: flex; align-items: center; gap: 0.75rem; color: #ef4444;">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</aside>

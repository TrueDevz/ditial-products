<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

// Fetch Site Name from settings
try {
    $stmt = $pdo->query("SELECT val FROM settings WHERE name = 'site_name'");
    $site_name = $stmt->fetchColumn() ?: 'DigitalMarket';
}
catch (PDOException $e) {
    $site_name = 'DigitalMarket';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_name); ?> - Premium Assets</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anek+Telugu:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <script>
        window.BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-inner">
                <a href="<?php echo BASE_URL; ?>/index.php" class="logo">
                    <i class="bi bi-cart-check"></i> <?php echo htmlspecialchars($site_name); ?>
                </a>
                
                <div class="search-bar">
                    <form action="<?php echo BASE_URL; ?>/search.php" method="GET">
                        <i class="bi bi-search"></i>
                        <input type="text" name="q" placeholder="Search for items...">
                    </form>
                </div>

                <nav class="nav-links">
                    <a href="<?php echo BASE_URL; ?>/category.php" class="nav-link">Marketplace</a>
                    
                    <a href="<?php echo BASE_URL; ?>/cart.php" class="nav-link" style="position: relative;">
                        <i class="bi bi-cart"></i> Cart
                        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span style="background: var(--primary); color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; position: absolute; top: -5px; right: -10px;"><?php echo count($_SESSION['cart']); ?></span>
                        <?php
endif; ?>
                    </a>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo BASE_URL; ?>/wishlist.php" class="nav-link"><i class="bi bi-heart"></i> Wishlist</a>
                        
                        <?php if ($_SESSION['role'] === 'seller' || $_SESSION['role'] === 'admin'): ?>
                            <a href="<?php echo BASE_URL; ?>/dashboard/upload.php" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8125rem;">+ Post Product</a>
                        <?php
    endif; ?>
                        
                        <div class="user-menu" style="display: flex; gap: 1rem; align-items: center;">
                            <?php
    $dash_link = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? BASE_URL . '/admin/index.php' : BASE_URL . '/dashboard/index.php';
?>
                            <a href="<?php echo $dash_link; ?>" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.8125rem;">Dashboard</a>
                            <a href="<?php echo BASE_URL; ?>/logout.php" title="Logout" style="color: #ef4444;"><i class="bi bi-box-arrow-right"></i></a>
                        </div>
                    <?php
else: ?>
                        <a href="<?php echo BASE_URL; ?>/login.php" class="nav-link">Login</a>
                        <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-primary">Sign Up</a>
                    <?php
endif; ?>
                </nav>
            </div>
        </div>
    </header>
    <main>

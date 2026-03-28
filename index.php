<?php
// index.php
include 'includes/header.php';

// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories LIMIT 4");
$categories = $stmt->fetchAll();

// Fetch latest products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 'approved' ORDER BY p.created_at DESC LIMIT 8");
$products = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Discover Premium Digital Assets</h1>
        <p>Over 10,000+ high-quality PHP scripts, HTML templates, and graphics created by top developers worldwide.</p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="<?php echo BASE_URL; ?>/category.php" class="btn btn-primary" style="background: white; color: var(--primary);">Browse Marketplace</a>
            <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-outline" style="border-color: white; color: white;">Start Selling</a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="container" style="margin-top: -3rem; position: relative; z-index: 10;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
        <?php foreach ($categories as $cat): ?>
            <a href="<?php echo BASE_URL; ?>/category.php?slug=<?php echo $cat['slug']; ?>" class="product-card" style="padding: 2rem; text-align: center; justify-content: center; align-items: center; border-radius: 1rem;">
                <i class="bi <?php echo $cat['icon']; ?>" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1rem;"></i>
                <h3 style="margin-bottom: 0.5rem;"><?php echo $cat['name']; ?></h3>
                <p style="font-size: 0.875rem; color: var(--gray);"><?php echo $cat['description']; ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Latest Products -->
<section class="container" style="margin-top: 5rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 2rem; font-weight: 800;">Recently Added</h2>
            <p style="color: var(--gray);">Check out the newest items in our marketplace.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/category.php" class="btn btn-outline">View All Items</a>
    </div>

    <div class="grid">
        <?php if (empty($products)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 4rem; background: var(--white); border-radius: var(--radius);">
                <i class="bi bi-box-seam" style="font-size: 3rem; color: var(--gray); opacity: 0.5;"></i>
                <h3 style="margin-top: 1rem;">No items found yet</h3>
                <p style="color: var(--gray);">We're just getting started! Check back soon for amazing products.</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image" style="background-image: url('<?php echo BASE_URL; ?>/uploads/previews/<?php echo $product['preview_image']; ?>'); position: relative;">
                        <?php
                        $is_wishlisted = false;
                        if (isset($_SESSION['user_id'])) {
                            $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
                            $stmt->execute([$_SESSION['user_id'], $product['id']]);
                            $is_wishlisted = $stmt->fetch() ? true : false;
                        }
                        ?>
                        <button class="wishlist-btn" data-product-id="<?php echo $product['id']; ?>" style="position: absolute; top: 0.75rem; right: 0.75rem; background: white; border: none; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow); cursor: pointer; z-index: 5;">
                            <i class="bi <?php echo $is_wishlisted ? 'bi-heart-fill' : 'bi-heart'; ?>" style="color: <?php echo $is_wishlisted ? '#ef4444' : 'inherit'; ?>; font-size: 0.875rem;"></i>
                        </button>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?php echo $product['category_name']; ?></div>
                        <h3 class="product-title"><?php echo $product['name']; ?></h3>
                        <div class="product-meta">
                            <div class="product-price"><?php echo APP_CURRENCY_SYMBOL; ?> <?php echo number_format($product['price'], 2); ?></div>
                            <a href="<?php echo BASE_URL; ?>/product.php?slug=<?php echo $product['slug']; ?>" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.875rem;">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section style="background: var(--white); padding: 6rem 0; margin-top: 5rem; border-top: 1px solid var(--border);">
    <div class="container" style="display: flex; align-items: center; gap: 4rem; justify-content: space-between; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
            <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1.5rem;">Join Our Community of Creators</h2>
            <p style="font-size: 1.1rem; color: var(--gray); margin-bottom: 2rem;">Are you a developer, designer, or digital artist? Start selling your work to thousands of customers today.</p>
            <ul style="list-style: none; margin-bottom: 2.5rem;">
                <li style="margin-bottom: 1rem; display: flex; gap: 1rem; align-items: center;">
                    <i class="bi bi-check-circle-fill" style="color: var(--secondary); font-size: 1.25rem;"></i>
                    <span>Keep up to 80% of every sale</span>
                </li>
                <li style="margin-bottom: 1rem; display: flex; gap: 1rem; align-items: center;">
                    <i class="bi bi-check-circle-fill" style="color: var(--secondary); font-size: 1.25rem;"></i>
                    <span>Secure payment processing via Razorpay</span>
                </li>
                <li style="margin-bottom: 1rem; display: flex; gap: 1rem; align-items: center;">
                    <i class="bi bi-check-circle-fill" style="color: var(--secondary); font-size: 1.25rem;"></i>
                    <span>Dedicated seller support team</span>
                </li>
            </ul>
            <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-primary btn-lg" style="padding: 1rem 2.5rem;">Create Seller Account</a>
        </div>
        <div style="flex: 1; min-width: 300px; display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div style="background: var(--light); padding: 2rem; border-radius: var(--radius); text-align: center;">
                <h3 style="font-size: 2rem; color: var(--primary);">5k+</h3>
                <p style="color: var(--gray);">Active Sellers</p>
            </div>
            <div style="background: var(--light); padding: 2rem; border-radius: var(--radius); text-align: center;">
                <h3 style="font-size: 2rem; color: var(--primary);">20k+</h3>
                <p style="color: var(--gray);">Products Sold</p>
            </div>
            <div style="background: var(--light); padding: 2rem; border-radius: var(--radius); text-align: center;">
                <h3 style="font-size: 2rem; color: var(--primary);">15k+</h3>
                <p style="color: var(--gray);">Reviews</p>
            </div>
            <div style="background: var(--light); padding: 2rem; border-radius: var(--radius); text-align: center;">
                <h3 style="font-size: 2rem; color: var(--primary);">24/7</h3>
                <p style="color: var(--gray);">Support</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

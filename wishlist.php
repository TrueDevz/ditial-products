<?php
// wishlist.php
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /digitalProducts/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch wishlist items
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, u.username as seller_name FROM wishlist w 
                       JOIN products p ON w.product_id = p.id 
                       JOIN categories c ON p.category_id = c.id 
                       JOIN users u ON p.seller_id = u.id 
                       WHERE w.user_id = ?");
$stmt->execute([$user_id]);
$wishlist = $stmt->fetchAll();
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 2rem;">My Wishlist</h1>

    <?php if (empty($wishlist)): ?>
        <div style="background: white; padding: 5rem; border-radius: var(--radius); text-align: center; box-shadow: var(--shadow);">
            <i class="bi bi-heart" style="font-size: 4rem; color: var(--gray); opacity: 0.2;"></i>
            <h2 style="margin-top: 2rem;">Your wishlist is empty</h2>
            <p style="color: var(--gray); margin-bottom: 2rem;">Save items you're interested in to keep track of them!</p>
            <a href="/digitalProducts/category.php" class="btn btn-primary">Browse Marketplace</a>
        </div>
    <?php else: ?>
        <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
            <?php foreach ($wishlist as $p): ?>
                <div class="product-card">
                    <div style="position: relative;">
                        <img src="/digitalProducts/uploads/previews/<?php echo $p['preview_image']; ?>" class="product-image">
                        <button class="wishlist-btn" data-product-id="<?php echo $p['id']; ?>" style="position: absolute; top: 1rem; right: 1rem; background: white; border: none; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow); cursor: pointer;">
                            <i class="bi bi-heart-fill" style="color: #ef4444;"></i>
                        </button>
                    </div>
                    <div class="product-info">
                        <span class="product-category"><?php echo $p['category_name']; ?></span>
                        <h3 class="product-title"><a href="/digitalProducts/product.php?slug=<?php echo $p['slug']; ?>"><?php echo $p['name']; ?></a></h3>
                        <p style="font-size: 0.8125rem; color: var(--gray); margin-bottom: 1.5rem;">by <span style="font-weight: 600; color: var(--dark);"><?php echo $p['seller_name']; ?></span></p>
                        <div class="product-footer">
                            <span class="product-price"><?php echo CURRENCY_SYMBOL; ?><?php echo $p['price']; ?></span>
                            <div style="display: flex; gap: 0.5rem;">
                                <form action="/digitalProducts/handlers/cart_handler.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8125rem;">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

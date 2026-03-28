<?php
// product.php
include 'includes/header.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: /digitalProducts/index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, u.username as seller_name, c.name as category_name FROM products p 
                       JOIN users u ON p.seller_id = u.id 
                       JOIN categories c ON p.category_id = c.id 
                       WHERE p.slug = ? AND p.status = 'approved'");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='container' style='padding: 5rem 0; text-align: center;'><h2>Product not found or pending approval.</h2><a href='/digitalProducts/index.php' class='btn btn-primary'>Back Home</a></div>";
    include 'includes/footer.php';
    exit;
}

// Update views
$pdo->prepare("UPDATE products SET views = views + 1 WHERE id = ?")->execute([$product['id']]);

// Fetch reviews
$stmt = $pdo->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$stmt->execute([$product['id']]);
$reviews = $stmt->fetchAll();
?>

<section class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <div style="display: flex; gap: 3rem; flex-wrap: wrap;">
        <!-- Left: Image and Description -->
        <div style="flex: 2; min-width: 400px;">
            <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 2rem;">
                <img src="/digitalProducts/uploads/previews/<?php echo $product['preview_image']; ?>" style="width: 100%; border-radius: var(--radius); margin-bottom: 2rem; box-shadow: var(--shadow);">
                <h2 style="font-weight: 800; margin-bottom: 1.5rem;">Description</h2>
                <div style="line-height: 1.8; color: #444;">
                    <?php echo nl2br($product['description']); ?>
                </div>
            </div>

            <!-- Reviews Section -->
            <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                <h3 style="margin-bottom: 2rem;">Reviews (<?php echo count($reviews); ?>)</h3>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="/digitalProducts/handlers/review_handler.php" method="POST" style="margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border);">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Your Rating</label>
                            <select name="rating" style="padding: 0.5rem 1rem; border: 1px solid var(--border); border-radius: var(--radius); font-weight: 600;">
                                <option value="5">5 ★★★★★</option>
                                <option value="4">4 ★★★★☆</option>
                                <option value="3">3 ★★★☆☆</option>
                                <option value="2">2 ★★☆☆☆</option>
                                <option value="1">1 ★☆☆☆☆</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Comment</label>
                            <textarea name="comment" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius);"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">Submit Review</button>
                    </form>
                <?php endif; ?>

                <?php if (empty($reviews)): ?>
                    <p style="color: var(--gray);">No reviews yet. Be the first to review!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div style="border-bottom: 1px solid var(--border); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="font-weight: 700;"><?php echo $review['username']; ?></span>
                                <div style="color: #fbbf24;">
                                    <?php for($i=1; $i<=5; $i++) echo $i <= $review['rating'] ? '★' : '☆'; ?>
                                </div>
                            </div>
                            <p style="color: var(--gray); font-size: 0.9375rem;"><?php echo $review['comment']; ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Purchase Sidebar -->
        <aside style="flex: 1; min-width: 300px;">
            <div style="background: white; padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--shadow-lg); position: sticky; top: 100px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <span style="font-size: 0.875rem; color: var(--gray); font-weight: 600; text-transform: uppercase;"><?php echo $product['category_name']; ?></span>
                    <span style="background: var(--light); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;"><i class="bi bi-cart-check"></i> <?php echo $product['sales']; ?> Sales</span>
                </div>
                
                <h1 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 1rem;"><?php echo $product['name']; ?></h1>
                <p style="color: var(--gray); margin-bottom: 2rem;">Published by <span style="color: var(--primary); font-weight: 600;"><?php echo $product['seller_name']; ?></span></p>

                <div style="font-size: 2.5rem; font-weight: 800; color: var(--dark); margin-bottom: 2rem;">
                    <?php echo CURRENCY_SYMBOL; ?><?php echo $product['price']; ?>
                </div>

                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                    <form action="/digitalProducts/handlers/cart_handler.php" method="POST" style="flex: 1;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="action" value="add">
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem; font-size: 1.125rem;">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </form>
                    <?php
                    $is_wishlisted = false;
                    if (isset($_SESSION['user_id'])) {
                        $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
                        $stmt->execute([$_SESSION['user_id'], $product['id']]);
                        $is_wishlisted = $stmt->fetch() ? true : false;
                    }
                    ?>
                    <button class="btn btn-outline wishlist-btn" data-product-id="<?php echo $product['id']; ?>" style="padding: 1.25rem; font-size: 1.25rem;">
                        <i class="bi <?php echo $is_wishlisted ? 'bi-heart-fill' : 'bi-heart'; ?>" style="color: <?php echo $is_wishlisted ? '#ef4444' : 'inherit'; ?>;"></i>
                    </button>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                    <div style="text-align: center;">
                        <div style="font-weight: 700; font-size: 1.125rem;"><?php echo $product['views']; ?></div>
                        <div style="font-size: 0.75rem; color: var(--gray);">VIEWS</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-weight: 700; font-size: 1.125rem;">24h</div>
                        <div style="font-size: 0.75rem; color: var(--gray);">SUPPORT</div>
                    </div>
                </div>

                <div style="margin-top: 2rem; background: #f0fdf4; padding: 1rem; border-radius: 0.5rem; display: flex; gap: 0.75rem;">
                    <i class="bi bi-shield-check" style="color: #16a34a; font-size: 1.25rem;"></i>
                    <p style="font-size: 0.8125rem; color: #166534;">Verified by DigitalMarket Quality Team.</p>
                </div>
            </div>
        </aside>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<?php
// category.php
include 'includes/header.php';

$category_slug = $_GET['slug'] ?? '';
$search_query = $_GET['q'] ?? '';

// Fetch all categories for sidebar
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

// Build query
$query = "SELECT p.*, c.name as category_name FROM products p 
          JOIN categories c ON p.category_id = c.id 
          WHERE p.status = 'approved'";
$params = [];

if ($category_slug) {
    $query .= " AND c.slug = ?";
    $params[] = $category_slug;
}

if ($search_query) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$query .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Current category name
$current_category = 'All Products';
if ($category_slug) {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $category_slug) {
            $current_category = $cat['name'];
            break;
        }
    }
}
?>

<section class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <div style="display: flex; gap: 3rem;">
        <!-- Sidebar Filters -->
        <aside style="width: 280px; flex-shrink: 0;">
            <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow); position: sticky; top: 100px;">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 700;">Categories</h3>
                <ul style="list-style: none;">
                    <li style="margin-bottom: 0.75rem;">
                        <a href="/digitalProducts/category.php" style="<?php echo !$category_slug ? 'color: var(--primary); font-weight: 600;' : 'color: var(--gray);'; ?>">
                            All Categories
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                        <li style="margin-bottom: 0.75rem;">
                            <a href="/digitalProducts/category.php?slug=<?php echo $cat['slug']; ?>" style="<?php echo $category_slug === $cat['slug'] ? 'color: var(--primary); font-weight: 600;' : 'color: var(--gray);'; ?>">
                                <?php echo $cat['name']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border);">

                <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 700;">Filter by Price</h3>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="number" placeholder="Min" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px; font-size: 0.875rem;">
                    <span>-</span>
                    <input type="number" placeholder="Max" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px; font-size: 0.875rem;">
                </div>
                <button class="btn btn-outline" style="width: 100%; margin-top: 1rem; padding: 0.5rem; justify-content: center;">Apply</button>
            </div>
        </aside>

        <!-- Product Listing -->
        <main style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 800;"><?php echo $current_category; ?></h1>
                    <p style="color: var(--gray);">Showing <?php echo count($products); ?> items</p>
                </div>
                <div>
                    <select style="padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius); background: var(--white); font-weight: 600;">
                        <option>Newest First</option>
                        <option>Best Selling</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                    </select>
                </div>
            </div>

            <div class="grid" style="margin-top: 0;">
                <?php if (empty($products)): ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 5rem; background: var(--white); border-radius: var(--radius);">
                        <i class="bi bi-search" style="font-size: 3rem; color: var(--gray); opacity: 0.5;"></i>
                        <h3 style="margin-top: 1rem;">No items found</h3>
                        <p style="color: var(--gray);">Try adjusting your search or filters to find what you're looking for.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image" style="background-image: url('/digitalProducts/uploads/previews/<?php echo $product['preview_image']; ?>'); position: relative;">
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
                                    <div class="product-price"><?php echo CURRENCY_SYMBOL; ?><?php echo $product['price']; ?></div>
                                    <a href="/digitalProducts/product.php?slug=<?php echo $product['slug']; ?>" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

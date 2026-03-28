<?php
// cart.php
include 'includes/header.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

$discount = 0;
if (isset($_SESSION['applied_coupon'])) {
    $discount = ($total * $_SESSION['applied_coupon']['discount_percent']) / 100;
}
$final_total = $total - $discount;
?>

<section class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 2rem;">Shopping Cart</h1>

    <div style="display: flex; gap: 3rem; flex-wrap: wrap;">
        <!-- Cart Items -->
        <div style="flex: 2; min-width: 400px;">
            <?php if (empty($cart)): ?>
                <div style="background: white; padding: 4rem; border-radius: var(--radius); text-align: center; box-shadow: var(--shadow);">
                    <i class="bi bi-cart-x" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                    <h2 style="margin-top: 1.5rem;">Your cart is empty</h2>
                    <p style="color: var(--gray); margin-bottom: 2rem;">Looks like you haven't added anything to your cart yet.</p>
                    <a href="<?php echo BASE_URL; ?>/category.php" class="btn btn-primary">Browse Marketplace</a>
                </div>
            <?php else: ?>
                <div style="background: white; border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--light); text-align: left;">
                                <th style="padding: 1.5rem;">Product</th>
                                <th style="padding: 1.5rem;">Price</th>
                                <th style="padding: 1.5rem;">Quantity</th>
                                <th style="padding: 1.5rem;">Total</th>
                                <th style="padding: 1.5rem;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $item): ?>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 1.5rem; display: flex; gap: 1rem; align-items: center;">
                                        <div style="width: 80px; height: 60px; background-image: url('<?php echo BASE_URL; ?>/uploads/previews/<?php echo $item['image']; ?>'); background-size: cover; background-position: center; border-radius: 4px;"></div>
                                        <span style="font-weight: 600;"><?php echo $item['name']; ?></span>
                                    </td>
                                    <td style="padding: 1.5rem;"><?php echo APP_CURRENCY_SYMBOL; ?> <?php echo number_format($item['price'], 2); ?></td>
                                    <td style="padding: 1.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <input type="number" value="<?php echo $item['quantity']; ?>" style="width: 60px; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;" readonly>
                                        </div>
                                    </td>
                                    <td style="padding: 1.5rem; font-weight: 700;"><?php echo APP_CURRENCY_SYMBOL; ?> <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    <td style="padding: 1.5rem; text-align: right;">
                                        <form action="<?php echo BASE_URL; ?>/handlers/cart_handler.php" method="POST">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="action" value="remove">
                                            <button type="submit" style="background: none; border: none; color: #ef4444; font-size: 1.25rem; cursor: pointer;"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Order Summary -->
        <?php if (!empty($cart)): ?>
            <aside style="flex: 1; min-width: 300px;">
                <div style="background: white; padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--shadow-lg); position: sticky; top: 100px;">
                    <h3 style="margin-bottom: 2rem; font-weight: 800;">Order Summary</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 1.1rem;">
                        <span style="color: var(--gray);">Subtotal</span>
                        <span style="font-weight: 600;"><?php echo APP_CURRENCY_SYMBOL; ?> <?php echo number_format($total, 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 1.1rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem;">
                        <span style="color: var(--gray);">Handling Fee</span>
                        <span style="font-weight: 600;"><?php echo APP_CURRENCY_SYMBOL; ?> 0.00</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 1.1rem; color: #16a34a; <?php echo $discount > 0 ? '' : 'display: none;'; ?>">
                        <span>Discount (<?php echo $_SESSION['applied_coupon']['code'] ?? ''; ?>)</span>
                        <span>-<?php echo APP_CURRENCY_SYMBOL; ?> <?php echo number_format($discount, 2); ?></span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 2.5rem;">
                        <span style="font-size: 1.25rem; font-weight: 800;">Total</span>
                        <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary);"><?php echo APP_CURRENCY_SYMBOL; ?> <?php echo number_format($final_total, 2); ?></span>
                    </div>

                    <form id="coupon-form" style="margin-bottom: 2rem; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray);">HAVE A COUPON?</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" id="coupon-code" placeholder="Enter code..." style="flex: 1; padding: 0.5rem 1rem; border: 1px solid var(--border); border-radius: var(--radius); font-size: 0.875rem;">
                            <button type="submit" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.8125rem;">Apply</button>
                        </div>
                        <p id="coupon-message" style="font-size: 0.75rem; margin-top: 0.5rem; min-height: 18px;"></p>
                    </form>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem; font-size: 1.125rem;">
                            Proceed to Checkout
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem; font-size: 1.125rem;">
                            Login to Checkout
                        </a>
                    <?php endif; ?>

                    <div style="margin-top: 2rem; text-align: center;">
                        <img src="https://razorpay.com/assets/razorpay-logo-white.png" style="height: 20px; filter: brightness(0.2); opacity: 0.5;">
                        <p style="font-size: 0.75rem; color: var(--gray); margin-top: 0.5rem;">Secure payment via Razorpay</p>
                    </div>
                </div>
            </aside>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

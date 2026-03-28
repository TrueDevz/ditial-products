<?php
// checkout.php
include 'includes/header.php';
require_once 'config/razorpay.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header('Location: ' . BASE_URL . '/cart.php');
    exit;
}

$cart = $_SESSION['cart'];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

$discount = 0;
$coupon_id = null;
if (isset($_SESSION['applied_coupon'])) {
    $coupon_id = $_SESSION['applied_coupon']['id'];
    $discount = ($total * $_SESSION['applied_coupon']['discount_percent']) / 100;
}
$final_total = $total - $discount;

$amount_in_paise = $final_total * 100;
$order_id = 'ORD' . time(); // In a real app, create this in DB first

// Create order in DB as pending
$stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, payment_status, razorpay_order_id, coupon_id, discount_amount) VALUES (?, ?, 'pending', ?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $final_total, $order_id, $coupon_id, $discount]);
$db_order_id = $pdo->lastInsertId();
?>

<section class="container" style="margin-top: 5rem; margin-bottom: 5rem; max-width: 800px;">
    <div style="background: white; padding: 4rem; border-radius: var(--radius); box-shadow: var(--shadow-lg); text-align: center;">
        <i class="bi bi-shield-lock" style="font-size: 3rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
        <h2 style="font-weight: 800; margin-bottom: 1rem;">Complete Your Purchase</h2>
        <p style="color: var(--gray); margin-bottom: 3rem;">You are about to purchase <?php echo count($cart); ?> items for a total of <strong><?php echo APP_CURRENCY_SYMBOL; ?> <?php echo number_format($final_total, 2); ?></strong>.</p>

        <div style="background: var(--light); padding: 2rem; border-radius: var(--radius); margin-bottom: 3rem; text-align: left;">
            <h4 style="margin-bottom: 1.5rem;">Order Summary</h4>
            <?php foreach ($cart as $item): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.9375rem;">
                    <span><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                    <span style="font-weight: 600;"><?php echo APP_CURRENCY_SYMBOL; ?> <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endforeach; ?>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.9375rem; color: #16a34a; <?php echo $discount > 0 ? '' : 'display: none;'; ?>">
                <span>Coupon Discount (<?php echo $_SESSION['applied_coupon']['code'] ?? ''; ?>)</span>
                <span style="font-weight: 600;">-<?php echo APP_CURRENCY_SYMBOL; ?> <?php echo number_format($discount, 2); ?></span>
            </div>
            <div style="border-top: 1px solid var(--border); margin-top: 1rem; padding-top: 1rem; display: flex; justify-content: space-between; font-weight: 800; font-size: 1.125rem;">
                <span>Total Amount</span>
                <span style="color: var(--primary);"><?php echo APP_CURRENCY_SYMBOL; ?> <?php echo number_format($final_total, 2); ?></span>
            </div>
        </div>

        <!-- Razorpay Payment Button -->
        <button id="rzp-button1" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem; font-size: 1.25rem;">
            Pay with Razorpay
        </button>

        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
        var options = {
            "key": "<?php echo RAZORPAY_KEY_ID; ?>", 
            "amount": "<?php echo $amount_in_paise; ?>", 
            "currency": "<?php echo CURRENCY_CODE; ?>",
            "name": "DigitalMarket",
            "description": "Purchase of Digital Assets",
            "image": "https://example.com/your_logo.png",
            "order_id": "", // In real life, use Razorpay Server SDK to get this
            "handler": function (response){
                // On success, redirect to success handler
                window.location.href = "<?php echo BASE_URL; ?>/handlers/payment_success.php?order_id=<?php echo $db_order_id; ?>&payment_id=" + response.razorpay_payment_id;
            },
            "prefill": {
                "name": "<?php echo $_SESSION['username']; ?>",
                "email": "user@example.com"
            },
            "theme": {
                "color": "#2563eb"
            }
        };
        var rzp1 = new Razorpay(options);
        document.getElementById('rzp-button1').onclick = function(e){
            rzp1.open();
            e.preventDefault();
        }
        </script>
        
        <p style="margin-top: 2rem; font-size: 0.8125rem; color: var(--gray);">
            <i class="bi bi-info-circle"></i> This is a secure payment processing area. Your data is encrypted.
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

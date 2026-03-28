<?php
// handlers/payment_success.php
session_start();
require_once '../config/db.php';

$order_id = $_GET['order_id'] ?? 0;
$payment_id = $_GET['payment_id'] ?? '';

if ($order_id && $payment_id) {
    // In a real app, verify the signature here using Razorpay SDK
    
    // Update order status
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'completed', razorpay_payment_id = ? WHERE id = ?");
    $stmt->execute([$payment_id, $order_id]);

    // Add order items
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, price) VALUES (?, ?, ?)");
            $stmt->execute([$order_id, $item['id'], $item['price']]);

            // Update product sales count
            $pdo->prepare("UPDATE products SET sales = sales + 1 WHERE id = ?")->execute([$item['id']]);
        }
    }

    // Clear cart
    $_SESSION['cart'] = [];
    
    $_SESSION['payment_success'] = "Thank you! Your purchase was successful. You can find your downloads in the dashboard.";
    header('Location: /digitalProducts/dashboard/index.php');
    exit;
} else {
    header('Location: /digitalProducts/index.php');
    exit;
}
?>

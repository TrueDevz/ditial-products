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
        function generatePurchaseCode() {
            return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }

        foreach ($_SESSION['cart'] as $item) {
            $quantity = $item['quantity'] ?? 1;
            for ($i = 0; $i < $quantity; $i++) {
                $purchase_code = strtoupper(generatePurchaseCode());
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, price, purchase_code) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $item['id'], $item['price'], $purchase_code]);
            }

            // Update product sales count by quantity
            $pdo->prepare("UPDATE products SET sales = sales + ? WHERE id = ?")->execute([$quantity, $item['id']]);
        }
    }

    // Clear cart
    $_SESSION['cart'] = [];
    
    $_SESSION['payment_success'] = "Thank you! Your purchase was successful. You can find your downloads in the dashboard.";
    header('Location: ' . BASE_URL . '/dashboard/index.php');
    exit;
} else {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}
?>

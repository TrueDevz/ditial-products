<?php
// handlers/cart_handler.php
session_start();
require_once '../config/db.php';

$action = $_POST['action'] ?? '';
$product_id = $_POST['product_id'] ?? 0;

if ($action === 'add' && $product_id > 0) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if product exists and is approved
    $stmt = $pdo->prepare("SELECT id, name, price, preview_image FROM products WHERE id = ? AND status = 'approved'");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity']++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['preview_image'],
                'quantity' => 1
            ];
        }
    }
}

if ($action === 'remove' && $product_id > 0) {
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $product_id) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>

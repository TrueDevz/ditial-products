<?php
// handlers/apply_coupon.php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

$code = trim($_POST['code'] ?? '');

if (empty($code)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a coupon code.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND expiry_date >= CURDATE()");
    $stmt->execute([$code]);
    $coupon = $stmt->fetch();

    if ($coupon) {
        $_SESSION['applied_coupon'] = [
            'id' => $coupon['id'],
            'code' => $coupon['code'],
            'discount_percent' => $coupon['discount_percent']
        ];
        echo json_encode([
            'status' => 'success', 
            'discount_percent' => $coupon['discount_percent'],
            'code' => $coupon['code']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired coupon code.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
?>

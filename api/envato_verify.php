<?php
// api/envato_verify.php
require_once '../config/db.php';

header('Content-Type: application/json');

// Get the purchase code from query params
$code = $_GET['code'] ?? '';

if (empty($code)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing purchase code']);
    exit;
}

// Fetch our custom Envato token from settings
$stmt = $pdo->query("SELECT val FROM settings WHERE name = 'envato_token'");
$our_token = $stmt->fetchColumn() ?: 'OUR_SECRET_TOKEN';

// Verify Authorization header
$headers = getallheaders();
$auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if ($auth !== 'Bearer ' . $our_token) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Check our database for this purchase code
$stmt = $pdo->prepare("SELECT oi.*, u.username as buyer, p.name as product_name, p.id as product_id, o.created_at 
                       FROM order_items oi 
                       JOIN orders o ON oi.order_id = o.id 
                       JOIN users u ON o.user_id = u.id 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE oi.purchase_code = ? AND o.payment_status = 'completed'");
$stmt->execute([$code]);
$purchase = $stmt->fetch();

if ($purchase) {
    // Format response exactly as Envato does
    echo json_encode([
        'buyer' => $purchase['buyer'],
        'item' => [
            'id' => $purchase['product_id'],
            'name' => $purchase['product_name']
        ],
        'purchase_count' => 1,
        'license' => 'Regular License',
        'support_amount' => '0.00',
        'supported_until' => date('c', strtotime('+1 year', strtotime($purchase['created_at']))),
        'sold_at' => date('c', strtotime($purchase['created_at']))
    ]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Invalid purchase code']);
}

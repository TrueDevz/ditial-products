<?php
// config/razorpay.php
// Fetch Settings from Database
try {
    $stmt = $pdo->query("SELECT name, val FROM settings WHERE name IN ('razorpay_key_id', 'razorpay_key_secret')");
    $db_settings = [];
    foreach ($stmt->fetchAll() as $row) {
        $db_settings[$row['name']] = $row['val'];
    }
} catch (PDOException $e) {
    $db_settings = [];
}

define('RAZORPAY_KEY_ID', $db_settings['razorpay_key_id'] ?? 'rzp_test_YOUR_KEY_ID');
define('RAZORPAY_KEY_SECRET', $db_settings['razorpay_key_secret'] ?? 'YOUR_KEY_SECRET');
?>

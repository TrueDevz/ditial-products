<?php
// config/db.php

$host = 'localhost';
$db   = 'digital_marketplace';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     // Fetch global settings
     $stmt = $pdo->query("SELECT name, val FROM settings WHERE name IN ('site_name', 'currency_symbol', 'currency_code')");
     $global_settings = [];
     while ($row = $stmt->fetch()) {
         $global_settings[$row['name']] = $row['val'];
     }
} catch (\PDOException $e) {
     $global_settings = [];
}

define('SITE_NAME', $global_settings['site_name'] ?? 'DigitalMarket');
define('APP_CURRENCY_SYMBOL', $global_settings['currency_symbol'] ?? '$');
define('CURRENCY_CODE', $global_settings['currency_code'] ?? 'USD');

// Base URL for the project
define('BASE_URL', 'https://pixvibestudios.in');
?>

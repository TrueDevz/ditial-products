<?php
// ============================================================
// License Server — Database Config
// This is a SEPARATE database from the buyer's app.
// It stores all activations across ALL buyers.
// ============================================================
define('LS_DB_HOST', 'localhost');
define('LS_DB_USER', 'YOUR_DB_USER');
define('LS_DB_PASS', 'YOUR_DB_PASSWORD');
define('LS_DB_NAME', 'shortnews_licenses'); // a separate DB just for licenses

class LicenseDB {
    public function connect(): PDO {
        $dsn = "mysql:host=" . LS_DB_HOST . ";dbname=" . LS_DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, LS_DB_USER, LS_DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    }
}

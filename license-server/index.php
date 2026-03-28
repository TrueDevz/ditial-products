<?php
// ============================================================
// ShortNews License Server — Main Endpoint
// Supports multiple products. Deploy on YOUR domain only.
//
// POST params: action, product_id, product_key, purchase_code, domain
// ============================================================

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/settings.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('X-Content-Type-Options: nosniff');

// ── Rate limiting (simple IP-based) ──────────────────────────
$ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$cacheDir = __DIR__ . '/cache';
if (!is_dir($cacheDir)) mkdir($cacheDir, 0750, true);
$rateFile = $cacheDir . '/rl_' . md5($ip) . '.json';
$rateData = file_exists($rateFile) ? json_decode(file_get_contents($rateFile), true) : ['count' => 0, 'time' => time()];
if (time() - $rateData['time'] > 60) {
    $rateData = ['count' => 0, 'time' => time()];
}
$rateData['count']++;
file_put_contents($rateFile, json_encode($rateData));
if ($rateData['count'] > RATE_LIMIT_PER_MINUTE) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many requests. Try again in a minute.']);
    exit;
}

// ── Identify Product ──────────────────────────────────────────
$productId  = trim($_POST['product_id']  ?? '');
$clientKey  = trim($_POST['product_key'] ?? $_SERVER['HTTP_X_PRODUCT_KEY'] ?? '');
$products   = PRODUCTS;

if (empty($productId) || !isset($products[$productId])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unknown product ID.']);
    exit;
}

// ── Validate product secret ───────────────────────────────────
if ($clientKey !== $products[$productId]['secret']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized request.']);
    exit;
}

// ── Route ─────────────────────────────────────────────────────
$action = $_POST['action'] ?? 'check';
$db     = new LicenseDB();
$conn   = $db->connect();

if ($action === 'check') {
    handleCheck($conn, $productId);
} elseif ($action === 'status') {
    handleStatus($conn, $productId);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}

// ── Handler: Verify & Activate ────────────────────────────────
function handleCheck(PDO $conn, string $productId): void
{
    $code   = trim($_POST['purchase_code'] ?? '');
    $domain = trim($_POST['domain'] ?? '');

    if (strlen($code) < 10 || empty($domain)) {
        echo json_encode(['success' => false, 'message' => 'Missing purchase_code or domain.']);
        return;
    }

    // Check our DB for existing activation for THIS product
    $stmt = $conn->prepare("SELECT * FROM activations WHERE purchase_code = ? AND product_id = ?");
    $stmt->execute([$code, $productId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        if ($existing['domain'] !== $domain) {
            echo json_encode([
                'success' => false,
                'message' => "This purchase code is already activated on: {$existing['domain']}. One license = one domain.",
            ]);
            return;
        }
        // Same domain re-activation — OK
        echo json_encode([
            'success' => true,
            'message' => 'License already active for this domain.',
            'buyer'   => $existing['buyer_username'],
        ]);
        return;
    }

    // Not in DB — verify with Envato
    $result = verifyWithEnvato($code);
    if (!$result['success']) {
        echo json_encode($result);
        return;
    }

    $buyer = $result['buyer'];

    // Store activation with product_id + domain lock
    $insert = $conn->prepare(
        "INSERT INTO activations (product_id, purchase_code, buyer_username, domain, activated_at)
         VALUES (?, ?, ?, ?, NOW())"
    );
    $insert->execute([$productId, $code, $buyer, $domain]);

    // Log
    $log = $conn->prepare(
        "INSERT INTO activation_logs (product_id, purchase_code, domain, action, ip_address, created_at)
         VALUES (?, ?, ?, 'activate', ?, NOW())"
    );
    $log->execute([$productId, $code, $domain, $_SERVER['REMOTE_ADDR'] ?? '']);

    echo json_encode(['success' => true, 'message' => 'License activated successfully!', 'buyer' => $buyer]);
}

// ── Handler: Status Check ─────────────────────────────────────
function handleStatus(PDO $conn, string $productId): void
{
    $code   = trim($_POST['purchase_code'] ?? '');
    $domain = trim($_POST['domain'] ?? '');

    if (empty($code) || empty($domain)) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
        return;
    }

    $stmt = $conn->prepare(
        "SELECT * FROM activations WHERE purchase_code = ? AND product_id = ? AND domain = ?"
    );
    $stmt->execute([$code, $productId, $domain]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($row
        ? ['success' => true, 'active' => true, 'buyer' => $row['buyer_username'], 'activated' => $row['activated_at']]
        : ['success' => true, 'active' => false]
    );
}

// ── Envato API Verification ───────────────────────────────────
function verifyWithEnvato(string $code): array
{
    $token = ENVATO_TOKEN;
    
    // ── DEV TEST BYPASS ───────────────────────────────────────
    // Allows testing the system before publishing to CodeCanyon.
    if ($code === '0000-0000-0000-TEST') {
        return ['success' => true, 'buyer' => 'test_buyer'];
    }
    // ──────────────────────────────────────────────────────────

    if (empty($token) || $token === 'YOUR_ENVATO_TOKEN_HERE') {
        return ['success' => false, 'message' => 'License server not configured. Contact seller support.'];
    }

    $ch = curl_init('https://api.envato.com/v3/market/buyer/purchase?code=' . urlencode($code));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'User-Agent: MultiProduct-LicenseServer/2.0',
        ],
    ]);
    $body     = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) return ['success' => false, 'message' => 'Network error verifying license. Try again.'];

    $data = json_decode($body, true);
    if ($httpCode === 200 && isset($data['buyer'])) return ['success' => true, 'buyer' => $data['buyer']];
    if ($httpCode === 404) return ['success' => false, 'message' => 'Invalid purchase code. Please check and try again.'];
    if ($httpCode === 401) return ['success' => false, 'message' => 'License server auth error. Contact support.'];
    return ['success' => false, 'message' => "Verification failed (HTTP $httpCode). Try again."];
}

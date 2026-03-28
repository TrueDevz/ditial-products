<?php
// sampleProduct/index.php
require_once 'config.php';
require_once 'LicenseManager.php';

$message = '';
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_code'])) {
    $code = trim($_POST['purchase_code']);
    if (!empty($code)) {
        $result = LicenseManager::verify($code);
        if ($result['status'] === 200) {
            $message = "✅ License Valid!";
        } else {
            $message = "❌ Invalid License: " . ($result['data']['error'] ?? 'Unknown error');
        }
    } else {
        $message = "⚠️ Please enter a purchase code.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample Product - License Check</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #2563eb; --bg: #f8fafc; --white: #ffffff; --text: #1e293b; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 4rem 1rem; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: var(--white); padding: 3rem; border-radius: 1.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1); width: 100%; max-width: 500px; }
        h1 { margin-top: 0; font-weight: 800; font-size: 1.875rem; letter-spacing: -0.025em; color: #0f172a; }
        p.desc { color: #64748b; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; }
        input { width: 100%; padding: 0.75rem 1rem; border: 2px solid #e2e8f0; border-radius: 0.75rem; font-family: inherit; font-size: 1rem; box-sizing: border-box; transition: border-color 0.2s; }
        input:focus { outline: none; border-color: var(--primary); }
        button { background: var(--primary); color: white; border: none; padding: 0.875rem 2rem; border-radius: 0.75rem; font-weight: 700; width: 100%; cursor: pointer; transition: transform 0.1s, background 0.2s; }
        button:active { transform: scale(0.98); }
        .status { padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; font-weight: 600; text-align: center; }
        .status.success { background: #dcfce7; color: #166534; }
        .status.error { background: #fee2e2; color: #991b1b; }
        .result-box { margin-top: 2rem; background: #1e293b; color: #cbd5e1; padding: 1.5rem; border-radius: 1rem; font-family: monospace; font-size: 0.8125rem; overflow-x: auto; }
        .result-title { color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Verify License</h1>
        <p class="desc">Enter your purchase code from <b>Pixvibe Studios</b> to activate your product.</p>

        <?php if ($message): ?>
            <div class="status <?php echo ($result && $result['status'] === 200) ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Purchase Code</label>
                <input type="text" name="purchase_code" placeholder="xxxx-xxxx-xxxx-xxxx" value="<?php echo isset($_POST['purchase_code']) ? htmlspecialchars($_POST['purchase_code']) : ''; ?>" required>
            </div>
            <button type="submit">Verify Now</button>
        </form>

        <?php if ($result): ?>
            <div class="result-box">
                <div class="result-title">Raw API Response (Status: <?php echo $result['status']; ?>)</div>
                <pre><?php echo json_encode($result['data'], JSON_PRETTY_PRINT); ?></pre>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

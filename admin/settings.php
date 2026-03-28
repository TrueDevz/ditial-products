<?php
// admin/settings.php
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$success = '';

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $name => $val) {
        $val = trim(html_entity_decode($val, ENT_QUOTES, 'UTF-8'));
        // Check if setting exists
        $check = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE name = ?");
        $check->execute([$name]);
        if ($check->fetchColumn() > 0) {
            $stmt = $pdo->prepare("UPDATE settings SET val = ? WHERE name = ?");
            $stmt->execute([$val, $name]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO settings (name, val) VALUES (?, ?)");
            $stmt->execute([$name, $val]);
        }
    }
    $success = 'Settings updated successfully.';
}

// Fetch all settings
try {
    $stmt = $pdo->query("SELECT * FROM settings");
    $settings_rows = $stmt->fetchAll();
    $settings = [];
    foreach ($settings_rows as $row) {
        $settings[$row['name']] = $row['val'];
    }
} catch (PDOException $e) {
    $settings = [];
    $error = "Warning: Settings table not found. Please ensure database is fully migrated.";
}
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
    <div style="display: flex; gap: 3rem;">
        <?php include 'sidebar.php'; ?>

        <main style="flex: 1;">
            <h1 style="font-weight: 800; margin-bottom: 2.5rem;">Site Settings</h1>

            <?php if ($success): ?>
                <div style="background: #dcfce7; color: #15803d; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem;"><?php echo $success; ?></div>
            <?php endif; ?>

            <div style="background: white; padding: 3rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                <form action="" method="POST">
                    <h3 style="margin-bottom: 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">General Configuration</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Site Name</label>
                            <input type="text" name="settings[site_name]" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Support Email</label>
                            <input type="email" name="settings[site_email]" value="<?php echo htmlspecialchars($settings['site_email'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Currency Symbol</label>
                            <input type="text" name="settings[currency_symbol]" value="<?php echo htmlspecialchars($settings['currency_symbol'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Currency Code</label>
                            <input type="text" name="settings[currency_code]" value="<?php echo htmlspecialchars($settings['currency_code'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;">
                        </div>
                    </div>

                    <h3 style="margin-bottom: 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">Razorpay API Settings</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Razorpay Key ID</label>
                            <input type="text" name="settings[razorpay_key_id]" value="<?php echo htmlspecialchars($settings['razorpay_key_id'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Razorpay Key Secret</label>
                            <input type="password" name="settings[razorpay_key_secret]" value="<?php echo htmlspecialchars($settings['razorpay_key_secret'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;">
                        </div>
                    </div>

                    <h3 style="margin-bottom: 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">License Server Integration (Self-Hosted)</h3>
                    <div style="background: #f8fafc; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 3rem;">
                        <div style="margin-bottom: 2rem;">
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Purchase Verification API URL</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="text" value="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . BASE_URL . "/v3/market/buyer/purchase"; ?>" style="flex: 1; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px; background: #f1f5f9; font-family: monospace;" readonly>
                                <button type="button" class="btn btn-outline" onclick="navigator.clipboard.writeText(this.previousElementSibling.value); alert('URL Copied!')">Copy URL</button>
                            </div>
                            <small style="color: #64748b;">Use this URL in your apps or in your <strong>License Server</strong> to verify purchase codes.</small>
                        </div>

                        <div style="max-width: 500px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Your Custom License Token (Bearer Token)</label>
                            <input type="text" name="settings[envato_token]" value="<?php echo htmlspecialchars($settings['envato_token'] ?? 'OUR_SECRET_TOKEN'); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px;">
                            <small style="color: var(--gray);">This must match the <code>ENVATO_TOKEN</code> in your license server's <code>config/settings.php</code>.</small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem; font-size: 1.125rem;">
                        <i class="bi bi-save"></i> Save All Settings
                    </button>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

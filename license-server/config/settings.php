<?php
// ============================================================
// License Server — Configuration
// ⚠️  Fill in ALL values before deploying!
// ============================================================

// Your Envato Personal Token (ONE token works for ALL your products)
// Get from: https://build.envato.com → Create Token
// Required permission: "View the user's purchases of the app creator"
define('ENVATO_TOKEN', 'YOUR_ENVATO_TOKEN_HERE');

// Rate limiting: max requests per IP per minute (shared across all products)
define('RATE_LIMIT_PER_MINUTE', 10);

// Admin Dashboard Password (change this!)
define('ADMIN_PASSWORD', 'admin123');

// ── Product Registry ─────────────────────────────────────────
// Add one entry per CodeCanyon product you publish.
// Each product has a unique ID and its own secret key.
// The secret in license_config.php of the buyer's files MUST match here.
//
// Generate strong secrets at: https://www.random.org/strings/
// (use 32+ chars, letters + numbers)
// ─────────────────────────────────────────────────────────────
define('PRODUCTS', [

    'shortnews' => [
        'name' => 'ShortNews — Flutter News App',
        'secret' => 'bMe1ErueMF5Ntg3f9X1yVmcBekGroLDA',
    ],
    'epaper' => [
        'name' => 'ePaper — Flutter News App',
        'secret' => 'bMe1ErueMF5Ntg3f9X1yVmcBekGroLDB',
    ],

    // ── Add future products below ──────────────────────────
    // 'jobapp' => [
    //     'name'   => 'JobApp — Flutter Job Board',
    //     'secret' => 'JOBAPP_SECRET_CHANGE_THIS_MIN_32_CHARS',
    // ],
    //
    // 'ecommerce' => [
    //     'name'   => 'ShopApp — Flutter eCommerce',
    //     'secret' => 'SHOPAPP_SECRET_CHANGE_THIS_MIN_32_CHARS',
    // ],

]);

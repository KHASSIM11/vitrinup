<?php
// ============================================================
// Configuration — Boutique Chaussures
// ============================================================

// Variables d'environnement (non versionné)
$envFile = __DIR__ . '/.env.php';
if (file_exists($envFile)) {
    require $envFile;
}

// Base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'u640824467_vitrinup');
define('DB_NAME', 'u640824467_vitrinup');

if (!defined('DB_PASS')) {
    define('DB_PASS', isset($env['DB_PASS']) ? $env['DB_PASS'] : '');
}

// Site
define('URL_ROOT',   'https://vitrinup.stokup.net');
define('SITE_NAME',  'Chaussures Maroc');          // ← à personnaliser avec le vrai nom
define('WHATSAPP',   '212604273455');              // ← numéro WhatsApp de ton ami (format international sans +)

// Upload images
define('UPLOAD_DIR', __DIR__ . '/../uploads/produits/');
define('UPLOAD_URL', URL_ROOT . '/uploads/produits/');

// Environnement
define('DEBUG', false); // true en local, false en production
?>
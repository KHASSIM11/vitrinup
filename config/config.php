<?php
// Charger les variables d'environnement depuis un fichier non versionné
$envFile = __DIR__ . '/.env.php';
if (file_exists($envFile)) {
    require $envFile; // Ce fichier doit définir $env['DB_PASS'] ou la constante DB_PASS
}

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'u640824467_vitrinup');

// Définir le mot de passe de la base de données
if (!defined('DB_PASS')) {
    if (isset($env['DB_PASS'])) {
        define('DB_PASS', $env['DB_PASS']);
    } else {
        // Valeur par défaut (vide) si le fichier .env.php n'est pas présent
        define('DB_PASS', '');
    }
}
define('DB_NAME', 'u640824467_vitrinup');

// URL de base de l'application (utile pour les liens absolus)
define('URL_ROOT', 'https://vitrinup.stokup.net'); // URL de production
define('SITE_NAME', 'Vitrinup');

// Autres configurations (clés API, etc.)
// define('WHATSAPP_API_KEY', 'votre_cle_api_whatsapp');
?>

<?php
// 1. Définir URL_ROOT et SITE_NAME
// Détecte automatiquement l'URL de base si possible, sinon utilise une valeur par défaut.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script = $_SERVER['SCRIPT_NAME'];
$baseUrl = rtrim(dirname($script), '/\\');
define('URL_ROOT', $protocol . $host . $baseUrl);
define('SITE_NAME', 'Vitrinup');

// 2. Inclure la configuration
require_once __DIR__ . '/config/config.php';

// 3. Inclure la classe Database
require_once __DIR__ . '/core/Database.php';

// 4. Inclure les classes de base (Controller, App)
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/App.php';

// Instancier l'application et la lancer
$app = new App();
$app->run();

?>

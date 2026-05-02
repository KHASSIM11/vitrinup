<?php
// ============================================================
// Point d'entrée unique — Boutique Chaussures
// ============================================================

session_start();

// Activation temporaire de l'affichage des erreurs PHP pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuration
require_once __DIR__ . '/config/config.php';

// Core
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/App.php';

// Lancement
$app = new App();
$app->run();
?>

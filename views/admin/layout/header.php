<?php
/**
 * Layout partagé admin — en-tête + sidebar
 * Variables attendues :
 *   $pageTitle  (string) — titre de l'onglet
 *   $activePage (string) — 'dashboard' | 'produits' | 'categories' | 'commandes' | 'stocks'
 *   $adminNom   (string) — nom de l'admin (fourni par le controller)
 */
$_nav = [
    'dashboard'  => ['url' => '/admin',            'icon' => '📊', 'label' => 'Dashboard'],
    'produits'   => ['url' => '/admin/produits',   'icon' => '👟', 'label' => 'Produits'],
    'categories' => ['url' => '/admin/categories', 'icon' => '🗂️', 'label' => 'Catégories'],
    'commandes'  => ['url' => '/admin/commandes',  'icon' => '📦', 'label' => 'Commandes'],
    'stocks'     => ['url' => '/admin/stocks',     'icon' => '📋', 'label' => 'Stocks'],
];
$_activePage = $activePage ?? '';
$_adminNom   = $adminNom ?? '';
$_initial    = strtoupper(substr($_adminNom ?: 'A', 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — <?= htmlspecialchars(SITE_NAME) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/css/admin.css?v=4">
</head>
<body>

<!-- Barre supérieure mobile (hamburger + marque) -->
<div class="mobile-topbar">
    <button class="hamburger" aria-label="Ouvrir le menu">☰</button>
    <div class="mobile-brand"><?= htmlspecialchars(SITE_NAME) ?></div>
</div>

<!-- Overlay sombre quand le menu est ouvert -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR — tiroir depuis la gauche -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand"><?= htmlspecialchars(SITE_NAME) ?></div>
        <button class="sidebar-close" id="sidebarClose" aria-label="Fermer le menu">✕</button>
    </div>
    <div class="admin-info">
        <span class="avatar"><?= $_initial ?></span>
        <div class="admin-details">
            <div class="admin-name"><?= htmlspecialchars($_adminNom) ?></div>
            <div class="admin-role">Administrateur</div>
        </div>
    </div>
    <nav>
        <div class="nav-label">Navigation</div>
        <?php foreach ($_nav as $key => $item): ?>
        <a href="<?= URL_ROOT . $item['url'] ?>"<?= $_activePage === $key ? ' class="active"' : '' ?>>
            <span class="icon"><?= $item['icon'] ?></span> <?= $item['label'] ?>
        </a>
        <?php endforeach; ?>
        <a href="<?= URL_ROOT ?>" target="_blank">
            <span class="icon">🌐</span> Voir le site
        </a>
    </nav>
    <div class="logout">
        <a href="<?= URL_ROOT ?>/admin/logout"><span>🚪</span> <span>Déconnexion</span></a>
    </div>
</aside>

<main class="main">
<script>
const URL_ROOT = '<?= URL_ROOT ?>';
const UPLOAD_URL = '<?= UPLOAD_URL ?>';
const STOCK_SEUIL_ALERTE = <?= STOCK_SEUIL_ALERTE ?>;
</script>

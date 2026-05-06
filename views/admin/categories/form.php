<?php
/**
 * @var array|null $categorie Catégorie à modifier (null si ajout)
 * @var string     $adminNom  Nom de l'admin connecté
 * @var string     $error     Message d'erreur éventuel
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $categorie ? 'Modifier' : 'Ajouter' ?> une catégorie — Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/css/admin.css">
</head>
<body>

<aside class="sidebar">
    <div class="brand"><?= htmlspecialchars(SITE_NAME) ?></div>
    <button class="hamburger" aria-label="Menu">☰</button>
    <div class="admin-info">👤 <?= htmlspecialchars($adminNom) ?></div>
    <nav>
        <a href="<?= URL_ROOT ?>/admin"><span>📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits"><span>👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories" class="active"><span>🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes"><span>📦</span> Commandes</a>
        <a href="<?= URL_ROOT ?>/admin/stocks"><span>📋</span> Stocks</a>
        <a href="<?= URL_ROOT ?>" target="_blank"><span>🌐</span> Voir le site</a>
    </nav>
    <div class="logout"><a href="<?= URL_ROOT ?>/admin/logout">🚪 Déconnexion</a></div>
</aside>

<main class="main">
    <div class="page-header">
        <h1><?= $categorie ? '✏️ Modifier la catégorie' : '➕ Ajouter une catégorie' ?></h1>
        <a href="<?= URL_ROOT ?>/admin/categories" class="btn-back">← Retour à la liste</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST"
              action="<?= $categorie ? URL_ROOT . '/admin/categories/modifier/' . $categorie['id'] : URL_ROOT . '/admin/categories/ajouter' ?>">

            <div class="form-group">
                <label>Nom de la catégorie *</label>
                <input type="text" name="nom" required value="<?= htmlspecialchars($categorie['nom'] ?? '') ?>" placeholder="Ex: Baskets, Sandales, Bottes...">
            </div>

            <div class="form-group">
                <label>Ordre d'affichage</label>
                <input type="number" name="ordre" min="0" value="<?= $categorie['ordre'] ?? 0 ?>">
                <div class="help">Plus le chiffre est petit, plus la catégorie apparaît en premier.</div>
            </div>

            <button type="submit" class="btn-submit">
                <?= $categorie ? '💾 ENREGISTRER' : '➕ AJOUTER' ?>
            </button>
        </form>
    </div>
</main>

<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>
</body>
</html>

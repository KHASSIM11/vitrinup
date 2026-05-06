<?php
/** @var array $produits Liste des produits */
/** @var int $page Page courante */
/** @var int $totalPages Nombre total de pages */
/** @var int $total Nombre total de produits */
/** @var string $adminNom Nom de l'admin connecté */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produits — Admin</title>
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
        <a href="<?= URL_ROOT ?>/admin/produits" class="active"><span>👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories"><span>🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes"><span>📦</span> Commandes</a>
        <a href="<?= URL_ROOT ?>/admin/stocks"><span>📋</span> Stocks</a>
        <a href="<?= URL_ROOT ?>" target="_blank"><span>🌐</span> Voir le site</a>
    </nav>
    <div class="logout"><a href="<?= URL_ROOT ?>/admin/logout">🚪 Déconnexion</a></div>
</aside>

<main class="main">
    <div class="page-header">
        <h1>👟 Produits</h1>
        <a href="<?= URL_ROOT ?>/admin/produits/ajouter" class="btn-add">+ Ajouter un produit</a>
    </div>

    <div class="section">
        <?php if (empty($produits)): ?>
            <p class="empty-msg">Aucun produit. <a href="<?= URL_ROOT ?>/admin/produits/ajouter">Ajouter le premier</a></p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Genre</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produits as $p): ?>
                        <tr>
                            <td>
                                <?php if ($p['image']): ?>
                                    <img src="<?= htmlspecialchars(UPLOAD_URL . $p['image']) ?>" class="product-img" alt="">
                                <?php else: ?>
                                    <div class="no-img">👟</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($p['nom']) ?></strong></td>
                            <td><?= htmlspecialchars($p['categorie_nom'] ?? '—') ?></td>
                            <td>
                                <?php if ($p['prix_promo']): ?>
                                    <span style="color:#e53935;font-weight:700"><?= number_format($p['prix_promo'], 0) ?> DH</span>
                                    <del style="color:#aaa;font-size:0.85rem"><?= number_format($p['prix'], 0) ?> DH</del>
                                <?php else: ?>
                                    <?= number_format($p['prix'], 0) ?> DH
                                <?php endif; ?>
                            </td>
                            <td><?= ucfirst($p['genre']) ?></td>
                            <td><span class="badge badge-<?= $p['statut'] ?>"><?= ucfirst($p['statut']) ?></span></td>
                            <td>
                                <a href="<?= URL_ROOT ?>/admin/produits/modifier/<?= $p['id'] ?>" class="btn-action btn-edit">✏️ Modifier</a>
                                <a href="<?= URL_ROOT ?>/admin/produits/supprimer/<?= $p['id'] ?>"
                                   class="btn-action btn-delete"
                                   onclick="return confirm('Supprimer ce produit ?')">🗑️ Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>">‹ Précédent</a>
                <?php else: ?>
                    <span class="disabled">‹ Précédent</span>
                <?php endif; ?>

                <?php
                $debut = max(1, $page - 2);
                $fin   = min($totalPages, $page + 2);
                if ($debut > 1): ?>
                    <a href="?page=1">1</a>
                    <?php if ($debut > 2): ?><span class="page-info">…</span><?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $debut; $i <= $fin; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($fin < $totalPages): ?>
                    <?php if ($fin < $totalPages - 1): ?><span class="page-info">…</span><?php endif; ?>
                    <a href="?page=<?= $totalPages ?>"><?= $totalPages ?></a>
                <?php endif; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>">Suivant ›</a>
                <?php else: ?>
                    <span class="disabled">Suivant ›</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>
</body>

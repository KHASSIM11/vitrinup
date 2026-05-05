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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; color: #1a1a1a; }
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: 240px; height: 100vh;
            background: #0a0a0a; color: #f5f0eb;
            display: flex; flex-direction: column; z-index: 100;
        }
        .sidebar .brand { padding: 25px 20px; font-size: 1.2rem; font-weight: 700; color: #c9a84c; letter-spacing: 2px; border-bottom: 1px solid #1a1a1a; }
        .sidebar .admin-info { padding: 15px 20px; font-size: 0.8rem; color: #666; border-bottom: 1px solid #1a1a1a; }
        .sidebar nav { flex: 1; padding: 20px 0; }
        .sidebar nav a { display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #888; text-decoration: none; font-size: 0.9rem; transition: all 0.2s; }
        .sidebar nav a:hover, .sidebar nav a.active { background: #141414; color: #c9a84c; border-left: 3px solid #c9a84c; }
        .sidebar .logout { padding: 20px; border-top: 1px solid #1a1a1a; }
        .sidebar .logout a { color: #666; text-decoration: none; font-size: 0.85rem; }
        .sidebar .logout a:hover { color: #ff6b6b; }
        .main { margin-left: 240px; padding: 30px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .page-header h1 { font-size: 1.8rem; }
        .btn-add {
            background: #c9a84c; color: #0a0a0a;
            padding: 10px 22px; border-radius: 6px;
            text-decoration: none; font-weight: 700;
            font-size: 0.9rem; transition: background 0.2s;
        }
        .btn-add:hover { background: #e0bb6a; }
        .section { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 12px; font-size: 0.8rem; color: #888; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #eee; }
        td { padding: 12px; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }
        .product-img { width: 55px; height: 55px; object-fit: cover; border-radius: 6px; background: #eee; }
        .no-img { width: 55px; height: 55px; background: #eee; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-actif { background: #e8f5e9; color: #2e7d32; }
        .badge-inactif { background: #ffebee; color: #c62828; }
        .btn-action {
            padding: 6px 14px; border-radius: 5px; font-size: 0.8rem;
            font-weight: 600; text-decoration: none; border: none; cursor: pointer;
            transition: opacity 0.2s; margin-right: 5px;
        }
        .btn-edit { background: #e3f2fd; color: #1565c0; }
        .btn-edit:hover { opacity: 0.8; }
        .btn-delete { background: #ffebee; color: #c62828; }
        .btn-delete:hover { opacity: 0.8; }
        .empty-msg { text-align: center; color: #aaa; padding: 40px; }
        .pagination {
            display: flex; justify-content: center; align-items: center;
            gap: 6px; margin-top: 25px; flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 36px; height: 36px; padding: 0 10px;
            border: 1px solid #ddd; border-radius: 6px;
            font-size: 0.85rem; color: #333; text-decoration: none;
            transition: all 0.2s;
        }
        .pagination a:hover { border-color: #c9a84c; color: #c9a84c; }
        .pagination .active { background: #c9a84c; color: #0a0a0a; border-color: #c9a84c; font-weight: 700; }
        .pagination .disabled { opacity: 0.3; cursor: not-allowed; }
        .pagination .page-info { color: #888; border: none; padding: 0 4px; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand"><?= htmlspecialchars(SITE_NAME) ?></div>
    <div class="admin-info">👤 <?= htmlspecialchars($adminNom) ?></div>
    <nav>
        <a href="<?= URL_ROOT ?>/admin"><span>📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits" class="active"><span>👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories"><span>🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes"><span>📦</span> Commandes</a>
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

</body>

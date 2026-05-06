<?php
/** @var array $produits Liste des produits */
/** @var int $page Page courante */
/** @var int $totalPages Nombre total de pages */
/** @var int $total Nombre total de produits */
/** @var string $adminNom Nom de l'admin connecté */
$pageTitle  = 'Produits';
$activePage = 'produits';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h1>👟 Produits</h1>
    <a href="<?= URL_ROOT ?>/admin/produits/ajouter" class="btn-add">+ Ajouter un produit</a>
</div>

<div class="card">
    <?php if (empty($produits)): ?>
        <p class="empty-msg">Aucun produit. <a href="<?= URL_ROOT ?>/admin/produits/ajouter">Ajouter le premier</a></p>
    <?php else: ?>
        <div class="table-wrapper">
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
                                    <span class="price-promo"><?= number_format($p['prix_promo'], 0) ?> DH</span>
                                    <del class="price-original"><?= number_format($p['prix'], 0) ?> DH</del>
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
        </div>

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

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

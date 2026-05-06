<?php
/**
 * @var array  $categories Liste des catégories
 * @var string $adminNom   Nom de l'admin connecté
 */
$pageTitle  = 'Catégories';
$activePage = 'categories';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h1>🗂️ Catégories</h1>
    <a href="<?= URL_ROOT ?>/admin/categories/ajouter" class="btn-add">+ Ajouter une catégorie</a>
</div>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="flash-message flash-success">✅ <?= htmlspecialchars($_SESSION['flash_success']) ?></div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="flash-message flash-error">❌ <?= htmlspecialchars($_SESSION['flash_error']) ?></div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="card">
    <?php if (empty($categories)): ?>
        <p class="empty-msg">Aucune catégorie. <a href="<?= URL_ROOT ?>/admin/categories/ajouter">Ajouter la première</a></p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Ordre</th>
                        <th>Nom</th>
                        <th>Slug</th>
                        <th>Produits</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $cat['ordre'] ?></td>
                            <td><strong><?= htmlspecialchars($cat['nom']) ?></strong></td>
                            <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                            <td><span class="badge-produits"><?= $cat['nb_produits'] ?> produit(s)</span></td>
                            <td>
                                <a href="<?= URL_ROOT ?>/admin/categories/modifier/<?= $cat['id'] ?>" class="btn-action btn-edit">✏️ Modifier</a>
                                <a href="<?= URL_ROOT ?>/admin/categories/supprimer/<?= $cat['id'] ?>"
                                   class="btn-action btn-delete"
                                   onclick="return confirm('Supprimer cette catégorie ?')">🗑️ Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

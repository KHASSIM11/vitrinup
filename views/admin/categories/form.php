<?php
/**
 * @var array|null $categorie Catégorie à modifier (null si ajout)
 * @var string     $adminNom  Nom de l'admin connecté
 * @var string     $error     Message d'erreur éventuel
 */
$pageTitle  = $categorie ? 'Modifier la catégorie' : 'Ajouter une catégorie';
$activePage = 'categories';
require_once __DIR__ . '/../layout/header.php';
?>

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

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

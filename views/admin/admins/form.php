<?php
/** @var array|null $admin  Admin à modifier (null = création) */
/** @var string     $error  Message d'erreur */
/** @var string     $adminNom Nom de l'admin connecté */
$isEdit    = isset($admin) && $admin;
$pageTitle  = $isEdit ? 'Modifier l\'administrateur' : 'Ajouter un administrateur';
$activePage = 'admins';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h1><?= $isEdit ? '✏️ Modifier l\'administrateur' : '➕ Ajouter un administrateur' ?></h1>
    <a href="<?= URL_ROOT ?>/admin/admins" class="btn-back">← Retour à la liste</a>
</div>

<?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST"
      action="<?= $isEdit ? URL_ROOT . '/admin/admins/modifier/' . $admin['id'] : URL_ROOT . '/admin/admins/ajouter' ?>">

    <div class="card" style="max-width:560px;">
        <h2>👤 Informations du compte</h2>

        <div class="form-group">
            <label>Nom complet *</label>
            <input type="text" name="nom" required
                   value="<?= htmlspecialchars($admin['nom'] ?? '') ?>"
                   placeholder="Ex : Mohamed Alami">
        </div>

        <div class="form-group">
            <label>Adresse email *</label>
            <input type="email" name="email" required
                   value="<?= htmlspecialchars($admin['email'] ?? '') ?>"
                   placeholder="admin@boutique.com">
        </div>

        <div class="form-group">
            <label><?= $isEdit ? 'Nouveau mot de passe' : 'Mot de passe *' ?></label>
            <input type="password" name="password"
                   <?= $isEdit ? '' : 'required' ?>
                   minlength="6"
                   placeholder="<?= $isEdit ? 'Laisser vide pour ne pas changer' : 'Minimum 6 caractères' ?>">
            <?php if ($isEdit): ?>
                <small class="text-muted">Laisser vide pour conserver le mot de passe actuel.</small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label><?= $isEdit ? 'Confirmer le nouveau mot de passe' : 'Confirmer le mot de passe *' ?></label>
            <input type="password" name="confirm"
                   <?= $isEdit ? '' : 'required' ?>
                   minlength="6"
                   placeholder="Répéter le mot de passe">
        </div>

        <div style="display:flex;gap:12px;margin-top:8px;">
            <button type="submit" class="btn-submit">
                <?= $isEdit ? '💾 Enregistrer' : '➕ Créer l\'administrateur' ?>
            </button>
            <a href="<?= URL_ROOT ?>/admin/admins" class="btn-back" style="padding:10px 20px;border:1px solid #ddd;border-radius:8px;">
                Annuler
            </a>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

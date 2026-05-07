<?php
/** @var array  $admins  Liste des administrateurs */
/** @var int    $adminId ID de l'admin connecté */
/** @var string $adminNom Nom de l'admin connecté */
$pageTitle  = 'Administrateurs';
$activePage = 'admins';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h1>👥 Administrateurs</h1>
    <a href="<?= URL_ROOT ?>/admin/admins/ajouter" class="btn-add">+ Ajouter un admin</a>
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
    <?php if (empty($admins)): ?>
        <p class="empty-msg">Aucun administrateur.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Créé le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $a): ?>
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <span class="avatar" style="width:32px;height:32px;border-radius:50%;background:var(--gold);color:var(--dark);display:inline-flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;flex-shrink:0;">
                                        <?= strtoupper(substr($a['nom'], 0, 1)) ?>
                                    </span>
                                    <strong><?= htmlspecialchars($a['nom']) ?></strong>
                                    <?php if ($a['id'] == $adminId): ?>
                                        <span class="badge badge-actif" style="font-size:0.65rem;">Vous</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($a['email']) ?></td>
                            <td><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
                            <td>
                                <a href="<?= URL_ROOT ?>/admin/admins/modifier/<?= $a['id'] ?>" class="btn-action btn-edit">✏️ Modifier</a>
                                <?php if ($a['id'] != $adminId): ?>
                                    <a href="<?= URL_ROOT ?>/admin/admins/supprimer/<?= $a['id'] ?>"
                                       class="btn-action btn-delete"
                                       onclick="return confirm('Supprimer cet administrateur ?')">🗑️ Supprimer</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="card" style="border-left:3px solid var(--gold);">
    <h2>ℹ️ Informations</h2>
    <p class="text-muted" style="font-size:0.88rem;line-height:1.7;">
        Tous les administrateurs ont accès à l'ensemble du panel (produits, catégories, commandes, stocks).<br>
        Vous ne pouvez pas supprimer votre propre compte ni le dernier administrateur existant.
    </p>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<?php
/**
 * @var array  $commandes      Liste des commandes
 * @var string $statutActif    Statut actuellement filtré
 * @var array  $statsParStatut Statistiques par statut
 * @var int    $page           Page courante
 * @var int    $totalPages     Nombre total de pages
 * @var int    $total          Nombre total de commandes
 * @var string $adminNom       Nom de l'admin connecté
 */
$pageTitle  = 'Commandes';
$activePage = 'commandes';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h1>📦 Commandes</h1>
</div>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="flash-message flash-success">✅ <?= htmlspecialchars($_SESSION['flash_success']) ?></div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="flash-message flash-error">❌ <?= htmlspecialchars($_SESSION['flash_error']) ?></div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<!-- Filtres par statut -->
<div class="filtres-statut">
    <?php
    $statuts = [
        ''         => 'Toutes',
        'nouveau'  => 'Nouveau',
        'vu'       => 'Vu',
        'confirme' => 'Confirmé',
        'annule'   => 'Annulé',
    ];
    $totalToutes = array_sum($statsParStatut);
    ?>
    <?php foreach ($statuts as $val => $label): ?>
        <?php $count = $val === '' ? $totalToutes : ($statsParStatut[$val] ?? 0); ?>
        <a href="<?= URL_ROOT ?>/admin/commandes<?= $val ? '?statut=' . $val : '' ?>"
           class="btn-filtre <?= $statutActif === $val ? 'actif' : '' ?>">
            <?= $label ?>
            <span class="count"><?= $count ?></span>
        </a>
    <?php endforeach; ?>
</div>

<div class="card">
    <?php if (empty($commandes)): ?>
        <p class="empty-msg">Aucune commande trouvée.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Produit</th>
                        <th>Taille</th>
                        <th>Qté</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $cmd): ?>
                        <tr>
                            <td><strong>#<?= $cmd['id'] ?></strong></td>
                            <td>
                                <div class="client-info">
                                    <strong><?= htmlspecialchars($cmd['client_nom'] ?? '—') ?></strong>
                                    <?php if (!empty($cmd['client_tel'])): ?>
                                        <small><?= htmlspecialchars($cmd['client_tel']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($cmd['produit_nom']) ?></td>
                            <td><?= htmlspecialchars($cmd['taille'] ?? '—') ?></td>
                            <td><strong><?= intval($cmd['quantite'] ?? 1) ?></strong></td>
                            <td>
                                <span class="badge badge-<?= $cmd['statut'] ?>">
                                    <?= ucfirst($cmd['statut']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($cmd['created_at'])) ?></td>
                            <td>
                                <a href="<?= URL_ROOT ?>/admin/commandes/voir/<?= $cmd['id'] ?>" class="btn-action btn-view">👁️ Voir</a>
                                <a href="<?= URL_ROOT ?>/admin/commandes/supprimer/<?= $cmd['id'] ?>"
                                   class="btn-action btn-delete"
                                   onclick="return confirm('Supprimer cette commande ?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php
            $baseUrl = URL_ROOT . '/admin/commandes' . (!empty($statutActif) ? '?statut=' . $statutActif : '');
            $sep = !empty($statutActif) ? '&' : '?';
            ?>
            <?php if ($page > 1): ?>
                <a href="<?= $baseUrl . $sep ?>page=<?= $page - 1 ?>">‹ Précédent</a>
            <?php else: ?>
                <span class="disabled">‹ Précédent</span>
            <?php endif; ?>

            <?php
            $debut = max(1, $page - 2);
            $fin   = min($totalPages, $page + 2);
            if ($debut > 1): ?>
                <a href="<?= $baseUrl . $sep ?>page=1">1</a>
                <?php if ($debut > 2): ?><span class="page-info">…</span><?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $debut; $i <= $fin; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= $baseUrl . $sep ?>page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($fin < $totalPages): ?>
                <?php if ($fin < $totalPages - 1): ?><span class="page-info">…</span><?php endif; ?>
                <a href="<?= $baseUrl . $sep ?>page=<?= $totalPages ?>"><?= $totalPages ?></a>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="<?= $baseUrl . $sep ?>page=<?= $page + 1 ?>">Suivant ›</a>
            <?php else: ?>
                <span class="disabled">Suivant ›</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

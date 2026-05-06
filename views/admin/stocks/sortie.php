<?php
/**
 * @var array  $commandes      Liste des commandes en cours
 * @var array  $statsCommandes Stats des commandes par statut
 * @var string $adminNom       Nom de l'admin connecté
 */
$pageTitle  = 'Sortie de stock';
$activePage = 'stocks';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1>📤 Sortie de stock</h1>
        <div class="subtitle">Gérer les sorties liées aux commandes clients — confirmation et annulation</div>
    </div>
    <a href="<?= URL_ROOT ?>/admin/stocks" class="btn-back">← Retour aux stocks</a>
</div>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="flash-message flash-success">✅ <?= htmlspecialchars($_SESSION['flash_success']) ?></div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="flash-message flash-error">❌ <?= htmlspecialchars($_SESSION['flash_error']) ?></div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<!-- Stats commandes -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🆕</div>
        <div class="label">Nouvelles</div>
        <div class="value orange"><?= intval($statsCommandes['nouveau'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👁️</div>
        <div class="label">Vues</div>
        <div class="value gold"><?= intval($statsCommandes['vu'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="label">Confirmées</div>
        <div class="value green"><?= intval($statsCommandes['confirme'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="label">Total en attente</div>
        <div class="value"><?= array_sum($statsCommandes) ?></div>
    </div>
</div>

<!-- Commandes en attente -->
<div class="card">
    <h2>📋 Commandes en attente de traitement</h2>
    <?php if (empty($commandes)): ?>
        <div class="empty-msg">
            <div class="empty-icon">✅</div>
            <p>Aucune commande en attente. Tout est à jour !</p>
        </div>
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
                        <th>Stock actuel</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $c):
                        $stockActuel  = intval($c['stock_actuel']);
                        $quantiteCmd  = intval($c['quantite'] ?? 1);
                        $classeStock  = $stockActuel <= 0 ? 'rupture' : ($stockActuel <= STOCK_SEUIL_ALERTE ? 'faible' : 'ok');
                    ?>
                        <tr class="<?= $stockActuel <= 0 ? 'row-rupture' : ($stockActuel <= STOCK_SEUIL_ALERTE ? 'row-faible' : '') ?>">
                            <td><strong>#<?= intval($c['id']) ?></strong></td>
                            <td>
                                <div class="client-info">
                                    <strong><?= htmlspecialchars($c['client_nom'] ?? 'Anonyme') ?></strong>
                                    <?php if ($c['client_tel']): ?>
                                        <small><?= htmlspecialchars($c['client_tel']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($c['produit_nom']) ?></strong>
                                <?php if ($c['marque']): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($c['marque']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($c['taille']) ?></strong></td>
                            <td><strong><?= $quantiteCmd ?></strong></td>
                            <td>
                                <span class="stock-actuel <?= $classeStock ?>">
                                    <?= $stockActuel ?> unité<?= $stockActuel > 1 ? 's' : '' ?>
                                </span>
                            </td>
                            <td><span class="badge badge-<?= $c['statut'] ?>"><?= ucfirst($c['statut']) ?></span></td>
                            <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></small></td>
                            <td>
                                <?php if ($c['statut'] === 'confirme'): ?>
                                    <form method="POST" action="<?= URL_ROOT ?>/admin/stocks/ajouterSortie" style="display:inline">
                                        <input type="hidden" name="commande_id" value="<?= intval($c['id']) ?>">
                                        <input type="hidden" name="action" value="annuler">
                                        <button type="submit" class="btn-action btn-annuler" data-confirm="Annuler cette commande ? Le stock sera remis.">↩ Annuler</button>
                                    </form>
                                <?php elseif ($c['statut'] === 'nouveau' || $c['statut'] === 'vu'): ?>
                                    <form method="POST" action="<?= URL_ROOT ?>/admin/stocks/ajouterSortie" style="display:inline">
                                        <input type="hidden" name="commande_id" value="<?= intval($c['id']) ?>">
                                        <input type="hidden" name="action" value="confirmer">
                                        <button type="submit" class="btn-action btn-confirmer" data-confirm="Confirmer cette commande ? Le stock a déjà été déduit à la commande.">✅ Confirmer</button>
                                    </form>
                                    <form method="POST" action="<?= URL_ROOT ?>/admin/stocks/ajouterSortie" style="display:inline;margin-left:4px;">
                                        <input type="hidden" name="commande_id" value="<?= intval($c['id']) ?>">
                                        <input type="hidden" name="action" value="annuler">
                                        <button type="submit" class="btn-action btn-annuler" data-confirm="Annuler cette commande ?">✕ Annuler</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

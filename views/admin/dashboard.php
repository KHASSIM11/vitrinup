<?php
/** @var string $adminNom Nom de l'admin connecté */
/** @var int $totalActifs Nombre de produits actifs */
/** @var int $totalInactifs Nombre de produits inactifs */
/** @var int $totalCommandes Nombre total de commandes */
/** @var int $nouvellesCommandes Nombre de nouvelles commandes */
/** @var array $dernieresCmds Dernières commandes */
/** @var array $stockFaible Produits avec stock faible */
/** @var array $ruptureStock Produits en rupture */
/** @var int $nbStockFaible Nombre de produits en stock faible */
/** @var int $nbRupture Nombre de produits en rupture */
$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require_once __DIR__ . '/layout/header.php';
?>

<div class="page-header">
    <div>
        <h1>📊 Dashboard</h1>
        <div class="subtitle">Bienvenue, <?= htmlspecialchars($adminNom) ?> 👋</div>
    </div>
</div>

<!-- STATS PRODUITS & COMMANDES -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="label">Produits actifs</div>
        <div class="value gold"><?= $totalActifs ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">❌</div>
        <div class="label">Produits inactifs</div>
        <div class="value"><?= $totalInactifs ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="label">Total commandes</div>
        <div class="value green"><?= $totalCommandes ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🆕</div>
        <div class="label">Nouvelles commandes</div>
        <div class="value red"><?= $nouvellesCommandes ?></div>
    </div>
</div>

<!-- STATS STOCKS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">⚠️</div>
        <div class="label">Stock faible (≤<?= STOCK_SEUIL_ALERTE ?>)</div>
        <div class="value orange"><?= $nbStockFaible ?></div>
        <div class="stat-change down">⚡ À réapprovisionner</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🚫</div>
        <div class="label">En rupture</div>
        <div class="value red"><?= $nbRupture ?></div>
        <div class="stat-change down">⛔ Action urgente</div>
    </div>
</div>

<!-- ALERTES STOCK FAIBLE -->
<?php if (!empty($stockFaible)): ?>
<div class="card">
    <h2>⚠️ Alertes stock faible (≤ <?= STOCK_SEUIL_ALERTE ?> unités)</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Stock total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stockFaible as $p): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($p['nom']) ?></strong>
                            <?php if ($p['marque']): ?>
                                <br><small class="text-muted"><?= htmlspecialchars($p['marque']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="text-warning-bold"><?= intval($p['stock_total']) ?> unité(s)</span>
                        </td>
                        <td>
                            <a href="<?= URL_ROOT ?>/admin/stocks?search=<?= urlencode($p['nom']) ?>" class="text-info">Gérer le stock</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- PRODUITS EN RUPTURE -->
<?php if (!empty($ruptureStock)): ?>
<div class="card">
    <h2>🚫 Produits en rupture de stock</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ruptureStock as $p): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($p['nom']) ?></strong>
                            <?php if ($p['marque']): ?>
                                <br><small class="text-muted"><?= htmlspecialchars($p['marque']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= URL_ROOT ?>/admin/stocks?search=<?= urlencode($p['nom']) ?>" class="text-info">Ajouter du stock</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- DERNIÈRES COMMANDES -->
<div class="card">
    <h2>📦 Dernières commandes</h2>
    <?php if (empty($dernieresCmds)): ?>
        <p class="empty-msg">Aucune commande pour l'instant.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produit</th>
                        <th>Client</th>
                        <th>Taille</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dernieresCmds as $cmd): ?>
                        <tr>
                            <td><?= $cmd['id'] ?></td>
                            <td><?= htmlspecialchars($cmd['produit_nom']) ?></td>
                            <td><?= htmlspecialchars($cmd['client_nom'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($cmd['taille'] ?? '—') ?></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($cmd['statut']) ?>">
                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $cmd['statut']))) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($cmd['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>

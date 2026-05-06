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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/css/admin.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="brand"><?= htmlspecialchars(SITE_NAME) ?></div>
    <button class="hamburger" aria-label="Menu">☰</button>
    <div class="admin-info">👤 <?= htmlspecialchars($adminNom) ?></div>
    <nav>
        <a href="<?= URL_ROOT ?>/admin" class="active"><span class="icon">📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits"><span class="icon">👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories"><span class="icon">🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes"><span class="icon">📦</span> Commandes</a>
        <a href="<?= URL_ROOT ?>/admin/stocks"><span class="icon">📋</span> Stocks</a>
        <a href="<?= URL_ROOT ?>" target="_blank"><span class="icon">🌐</span> Voir le site</a>
    </nav>
    <div class="logout">
        <a href="<?= URL_ROOT ?>/admin/logout">🚪 Déconnexion</a>
    </div>
</aside>

<!-- MAIN -->
<main class="main">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Bienvenue, <?= htmlspecialchars($adminNom) ?> 👋</p>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Produits actifs</div>
            <div class="value gold"><?= $totalActifs ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Produits inactifs</div>
            <div class="value"><?= $totalInactifs ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Total commandes</div>
            <div class="value green"><?= $totalCommandes ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Nouvelles commandes</div>
            <div class="value red"><?= $nouvellesCommandes ?></div>
        </div>
    </div>

    <!-- STATS STOCKS -->
    <div class="stats-grid" style="margin-top:10px;">
        <div class="stat-card">
            <div class="label">⚠️ Stock faible (≤<?= STOCK_SEUIL_ALERTE ?>)</div>
            <div class="value" style="color:#ff9800"><?= $nbStockFaible ?></div>
        </div>
        <div class="stat-card">
            <div class="label">🚫 En rupture</div>
            <div class="value red"><?= $nbRupture ?></div>
        </div>
    </div>

    <!-- ALERTES STOCK FAIBLE -->
    <?php if (!empty($stockFaible)): ?>
    <div class="section" style="margin-top:25px;">
        <h2>⚠️ Alertes stock faible (≤ <?= STOCK_SEUIL_ALERTE ?> unités)</h2>
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
                                <br><small style="color:#888"><?= htmlspecialchars($p['marque']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="color:#e65100;font-weight:700;"><?= intval($p['stock_total']) ?> unité(s)</span>
                        </td>
                        <td>
                            <a href="<?= URL_ROOT ?>/admin/stocks?search=<?= urlencode($p['nom']) ?>" style="color:#1565c0;">Gérer le stock</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- PRODUITS EN RUPTURE -->
    <?php if (!empty($ruptureStock)): ?>
    <div class="section" style="margin-top:25px;">
        <h2>🚫 Produits en rupture de stock</h2>
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
                                <br><small style="color:#888"><?= htmlspecialchars($p['marque']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= URL_ROOT ?>/admin/stocks?search=<?= urlencode($p['nom']) ?>" style="color:#1565c0;">Ajouter du stock</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- DERNIÈRES COMMANDES -->
    <div class="section" style="margin-top:25px;">
        <h2>📦 Dernières commandes</h2>
        <?php if (empty($dernieresCmds)): ?>
            <p class="empty-msg">Aucune commande pour l'instant.</p>
        <?php else: ?>
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
        <?php endif; ?>
    </div>
</main>

<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>
</body>

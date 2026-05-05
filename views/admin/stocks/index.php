<?php
/**
 * @var array  $produits          Liste des produits avec stock
 * @var array  $taillesParProduit Tailles détaillées par produit
 * @var string $search            Recherche en cours
 * @var string $statut            Filtre statut stock
 * @var int    $page              Page courante
 * @var int    $totalPages        Nombre total de pages
 * @var int    $total             Nombre total de produits
 * @var array  $stats             Stats globales (stock_faible, rupture, avec_stock)
 * @var array  $derniersMouvements Derniers mouvements
 * @var string $adminNom          Nom de l'admin connecté
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stocks — Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/css/admin.css">
</head>
<body>

<aside class="sidebar">
    <div class="brand">Vitrin<span>up</span></div>
    <div class="admin-info">
        <span class="avatar"><?= strtoupper(substr($adminNom ?? 'A', 0, 1)) ?></span>
        <?= htmlspecialchars($adminNom ?? '') ?>
    </div>
    <nav>
        <div class="nav-label">Navigation</div>
        <a href="<?= URL_ROOT ?>/admin"><span class="icon">📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits"><span class="icon">👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories"><span class="icon">🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes"><span class="icon">📦</span> Commandes</a>
        <a href="<?= URL_ROOT ?>/admin/stocks" class="active"><span class="icon">📋</span> Stocks</a>
        <a href="<?= URL_ROOT ?>" target="_blank"><span class="icon">🌐</span> Voir le site</a>
    </nav>
    <div class="logout"><a href="<?= URL_ROOT ?>/admin/logout"><span>🚪</span> <span>Déconnexion</span></a></div>
</aside>

<main class="main">
    <div class="page-header">
        <div>
            <h1>📋 Gestion des stocks</h1>
            <div class="subtitle">Seuil d'alerte : <strong><?= STOCK_SEUIL_ALERTE ?></strong> unités par taille</div>
        </div>
        <div class="header-actions">
            <a href="<?= URL_ROOT ?>/admin/stocks/exportCsv?<?= http_build_query(['search' => $search, 'statut' => $statut]) ?>" class="btn-export" id="exportCsv">📥 Export CSV</a>
        </div>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash-message flash-success">✅ <?= htmlspecialchars($_SESSION['flash_success']) ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flash-message flash-error">❌ <?= htmlspecialchars($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <!-- Cartes de navigation premium -->
    <div class="nav-cards">
        <a href="<?= URL_ROOT ?>/admin/stocks/entree" class="nav-card">
            <div class="icon">📥</div>
            <div class="title">Entrée de stock</div>
            <div class="desc">Ajouter du stock manuellement ou via réapprovisionnement</div>
        </a>
        <a href="<?= URL_ROOT ?>/admin/stocks/sortie" class="nav-card">
            <div class="icon">📤</div>
            <div class="title">Sortie de stock</div>
            <div class="desc">Gérer les sorties liées aux commandes clients</div>
        </a>
        <a href="<?= URL_ROOT ?>/admin/stocks/historique" class="nav-card">
            <div class="icon">📜</div>
            <div class="title">Historique</div>
            <div class="desc">Traçabilité complète des entrées et sorties</div>
        </a>
    </div>

    <!-- Stats premium -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="label">Avec stock</div>
            <div class="value green"><?= intval($stats['avec_stock'] ?? 0) ?></div>
            <div class="stat-change up">✓ Produits disponibles</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⚠️</div>
            <div class="label">Stock faible (≤<?= STOCK_SEUIL_ALERTE ?>)</div>
            <div class="value orange"><?= intval($stats['stock_faible'] ?? 0) ?></div>
            <div class="stat-change down">⚡ À réapprovisionner</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🚫</div>
            <div class="label">En rupture</div>
            <div class="value red"><?= intval($stats['rupture'] ?? 0) ?></div>
            <div class="stat-change down">⛔ Action urgente</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="label">Total produits</div>
            <div class="value gold"><?= intval($total) ?></div>
            <div class="stat-change up">📊 Dans le catalogue</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filtres">
        <form method="GET" action="<?= URL_ROOT ?>/admin/stocks">
            <input type="text" name="search" id="searchLive" placeholder="Rechercher un produit ou une marque..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit" class="btn-filtre actif">🔍 Rechercher</button>
            <a href="<?= URL_ROOT ?>/admin/stocks" class="btn-filtre">✕ Réinitialiser</a>
        </form>
        <div style="display:flex;gap:6px;flex-wrap:wrap;">
            <a href="<?= URL_ROOT ?>/admin/stocks" class="btn-filtre <?= empty($statut) ? 'actif' : '' ?>">Tous</a>
            <a href="<?= URL_ROOT ?>/admin/stocks?statut=faible" class="btn-filtre <?= $statut === 'faible' ? 'actif' : '' ?>">⚠️ Stock faible</a>
            <a href="<?= URL_ROOT ?>/admin/stocks?statut=rupture" class="btn-filtre <?= $statut === 'rupture' ? 'actif' : '' ?>">🚫 Rupture</a>
            <a href="<?= URL_ROOT ?>/admin/stocks?statut=ok" class="btn-filtre <?= $statut === 'ok' ? 'actif' : '' ?>">✅ OK</a>
        </div>
    </div>

    <!-- Tableau des stocks -->
    <div class="card">
        <div class="card-header">
            <h2>📋 Produits et niveaux de stock</h2>
            <span class="total-info"><?= $total ?> produit<?= $total > 1 ? 's' : '' ?></span>
        </div>

        <?php if (empty($produits)): ?>
            <div class="empty-msg">
                <div class="empty-icon">📦</div>
                <p>Aucun produit trouvé.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Produit</th>
                            <th>Statut</th>
                            <th>Stock total</th>
                            <th>Tailles / Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $p):
                            $stockTotal = intval($p['stock_total']);
                            $classeStock = $stockTotal === 0 ? 'stock-rupture' : ($stockTotal <= STOCK_SEUIL_ALERTE ? 'stock-faible' : 'stock-ok');
                            $classeRow = $stockTotal === 0 ? 'row-rupture' : ($stockTotal <= STOCK_SEUIL_ALERTE ? 'row-faible' : '');
                            $tailles = $taillesParProduit[$p['id']] ?? [];
                            // Barre de progression
                            $maxStock = 20;
                            $pct = min(100, ($stockTotal / $maxStock) * 100);
                            $barClass = $stockTotal === 0 ? 'rupture' : ($stockTotal <= STOCK_SEUIL_ALERTE ? 'faible' : 'ok');
                        ?>
                            <tr class="<?= $classeRow ?>">
                                <td>
                                    <?php if ($p['image']): ?>
                                        <img src="<?= htmlspecialchars(UPLOAD_URL . $p['image']) ?>" class="product-img" alt="">
                                    <?php else: ?>
                                        <div class="no-img">👟</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($p['nom']) ?></strong>
                                    <?php if ($p['marque']): ?>
                                        <br><small style="color:var(--text-muted)"><?= htmlspecialchars($p['marque']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-<?= $p['statut'] ?>"><?= ucfirst($p['statut']) ?></span></td>
                                <td>
                                    <span class="stock-badge <?= $classeStock ?>">
                                        <?= $stockTotal ?> unité<?= $stockTotal > 1 ? 's' : '' ?>
                                    </span>
                                    <div class="stock-bar">
                                        <div class="fill <?= $barClass ?>" style="width:<?= $pct ?>%"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tailles-list" id="tailles-<?= intval($p['id']) ?>">
                                        <?php if (!empty($tailles)): foreach ($tailles as $t): ?>
                                            <div class="taille-item">
                                                <span><?= htmlspecialchars($t['taille']) ?>:</span>
                                                <input type="number" class="stock-input" value="<?= intval($t['stock']) ?>"
                                                       min="0" data-taille-id="<?= intval($t['id']) ?>"
                                                       data-produit-id="<?= intval($p['id']) ?>">
                                                <button class="btn-del-taille" data-taille-id="<?= intval($t['id']) ?>" title="Supprimer">✕</button>
                                            </div>
                                        <?php endforeach; endif; ?>
                                    </div>
                                    <div class="add-taille-form">
                                        <input type="text" placeholder="Taille" id="new-taille-<?= intval($p['id']) ?>">
                                        <input type="number" placeholder="Stock" id="new-stock-<?= intval($p['id']) ?>" value="0" min="0">
                                        <button class="btn-add-taille" data-produit-id="<?= intval($p['id']) ?>">+ Ajouter</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php
                $queryParams = $_GET;
                unset($queryParams['page']);
                $queryStr = http_build_query($queryParams);
                $baseUrl = URL_ROOT . '/admin/stocks' . ($queryStr ? '?' . $queryStr : '');
                $sep = $queryStr ? '&' : '?';
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

    <!-- Derniers mouvements -->
    <?php if (!empty($derniersMouvements)): ?>
    <div class="card">
        <h2>🔄 Derniers mouvements</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Produit</th>
                        <th>Type</th>
                        <th>Quantité</th>
                        <th>Référence</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($derniersMouvements as $m):
                        $typeLabel = ['entree' => '📥 Entrée', 'sortie' => '📤 Sortie', 'commande' => '📦 Commande', 'annulation' => '↩ Annulation'];
                        $estPositif = in_array($m['type'], ['entree', 'annulation']);
                    ?>
                        <tr>
                            <td><small style="color:var(--text-muted)"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></small></td>
                            <td><strong><?= htmlspecialchars($m['produit_nom']) ?></strong></td>
                            <td><span class="type-badge type-<?= $m['type'] ?>"><?= $typeLabel[$m['type']] ?? $m['type'] ?></span></td>
                            <td><span class="<?= $estPositif ? 'qte-positive' : 'qte-negative' ?>"><?= $estPositif ? '+' : '-' ?><?= intval($m['quantite']) ?></span></td>
                            <td><small style="color:var(--text-muted)"><?= htmlspecialchars($m['reference'] ?? '-') ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</main>

<script>
const URL_ROOT = '<?= URL_ROOT ?>';
const STOCK_SEUIL_ALERTE = <?= STOCK_SEUIL_ALERTE ?>;
</script>
<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>
</body>
</html>

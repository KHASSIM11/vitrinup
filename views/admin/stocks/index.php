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
    <div class="brand"><?= htmlspecialchars(SITE_NAME) ?></div>
    <div class="admin-info">👤 <?= htmlspecialchars($adminNom ?? '') ?></div>
    <nav>
        <a href="<?= URL_ROOT ?>/admin"><span>📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits"><span>👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories"><span>🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes"><span>📦</span> Commandes</a>
        <a href="<?= URL_ROOT ?>/admin/stocks" class="active"><span>📋</span> Stocks</a>
        <a href="<?= URL_ROOT ?>" target="_blank"><span>🌐</span> Voir le site</a>
    </nav>
    <div class="logout"><a href="<?= URL_ROOT ?>/admin/logout">🚪 Déconnexion</a></div>
</aside>

<main class="main">
    <div class="page-header">
        <h1>📦 Gestion des stocks</h1>
        <span class="seuil-info">Seuil d'alerte : <strong><?= STOCK_SEUIL_ALERTE ?></strong> unités</span>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash-message flash-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flash-message flash-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <!-- Cartes de navigation -->
    <div class="nav-cards">
        <a href="<?= URL_ROOT ?>/admin/stocks/entree" class="nav-card">
            <div class="icon">📥</div>
            <div class="title">Entrée de stock</div>
            <div class="desc">Ajouter du stock manuellement</div>
        </a>
        <a href="<?= URL_ROOT ?>/admin/stocks/sortie" class="nav-card">
            <div class="icon">📤</div>
            <div class="title">Sortie de stock</div>
            <div class="desc">Gérer les sorties liées aux commandes</div>
        </a>
        <a href="<?= URL_ROOT ?>/admin/stocks/historique" class="nav-card">
            <div class="icon">📜</div>
            <div class="title">Historique</div>
            <div class="desc">Traçabilité des entrées et sorties</div>
        </a>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Avec stock</div>
            <div class="value green"><?= $stats['avec_stock'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Stock faible (≤<?= STOCK_SEUIL_ALERTE ?>)</div>
            <div class="value orange"><?= $stats['stock_faible'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="label">En rupture</div>
            <div class="value red"><?= $stats['rupture'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Total produits</div>
            <div class="value gold"><?= $total ?></div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filtres">
        <form method="GET" action="<?= URL_ROOT ?>/admin/stocks">
            <input type="text" name="search" placeholder="Rechercher un produit..." value="<?= htmlspecialchars($search ?? '') ?>">
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

    <!-- Tableau -->
    <div class="card">
        <?php if (empty($produits)): ?>
            <p class="empty-msg">Aucun produit trouvé.</p>
        <?php else: ?>
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
                        $tailles = $taillesParProduit[$p['id']] ?? [];
                    ?>
                        <tr>
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
                                    <br><small style="color:#888"><?= htmlspecialchars($p['marque']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-<?= $p['statut'] ?>"><?= ucfirst($p['statut']) ?></span></td>
                            <td>
                                <span class="stock-badge <?= $classeStock ?>">
                                    <?= $stockTotal ?> unité<?= $stockTotal > 1 ? 's' : '' ?>
                                </span>
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
</main>

<script>
const URL_ROOT = '<?= URL_ROOT ?>';
</script>
<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>
</body>
</html>

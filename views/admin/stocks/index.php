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
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; color: #1a1a1a; }
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: 240px; height: 100vh;
            background: #0a0a0a; color: #f5f0eb;
            display: flex; flex-direction: column; z-index: 100;
        }
        .sidebar .brand { padding: 25px 20px; font-size: 1.2rem; font-weight: 700; color: #c9a84c; letter-spacing: 2px; border-bottom: 1px solid #1a1a1a; }
        .sidebar .admin-info { padding: 15px 20px; font-size: 0.8rem; color: #666; border-bottom: 1px solid #1a1a1a; }
        .sidebar nav { flex: 1; padding: 20px 0; }
        .sidebar nav a { display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #888; text-decoration: none; font-size: 0.9rem; transition: all 0.2s; }
        .sidebar nav a:hover, .sidebar nav a.active { background: #141414; color: #c9a84c; border-left: 3px solid #c9a84c; }
        .sidebar .logout { padding: 20px; border-top: 1px solid #1a1a1a; }
        .sidebar .logout a { color: #666; text-decoration: none; font-size: 0.85rem; }
        .sidebar .logout a:hover { color: #ff6b6b; }
        .main { margin-left: 240px; padding: 30px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .page-header h1 { font-size: 1.8rem; }
        .page-header .seuil-info { font-size: 0.85rem; color: #888; }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }
        .stat-card .label {
            font-size: 0.75rem; color: #888;
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .stat-card .value {
            font-size: 1.8rem; font-weight: 700;
        }
        .stat-card .value.gold { color: #c9a84c; }
        .stat-card .value.green { color: #25D366; }
        .stat-card .value.red { color: #e53935; }
        .stat-card .value.orange { color: #ff9800; }

        /* Filtres */
        .filtres {
            display: flex; flex-wrap: wrap; gap: 10px;
            margin-bottom: 20px; align-items: center;
        }
        .filtres form { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; flex: 1; }
        .filtres input[type="text"] {
            padding: 9px 16px; border: 1px solid #ddd; border-radius: 6px;
            font-size: 0.9rem; flex: 1; min-width: 200px; outline: none;
        }
        .filtres input[type="text"]:focus { border-color: #c9a84c; }
        .btn-filtre {
            padding: 8px 16px; border-radius: 20px;
            font-size: 0.85rem; font-weight: 600;
            text-decoration: none; transition: all 0.2s;
            background: #f5f5f5; color: #666; border: none; cursor: pointer;
        }
        .btn-filtre:hover { background: #e0e0e0; }
        .btn-filtre.actif { background: #c9a84c; color: #0a0a0a; }
        .btn-filtre .count { background: rgba(0,0,0,0.1); padding: 1px 8px; border-radius: 10px; margin-left: 5px; font-size: 0.75rem; }
        .btn-filtre.actif .count { background: rgba(0,0,0,0.2); }

        /* Section */
        .section { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        .section h2 { font-size: 1.1rem; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee; }

        /* Tableau */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 12px; font-size: 0.8rem; color: #888; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #eee; }
        td { padding: 12px; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }
        .product-img { width: 45px; height: 45px; object-fit: cover; border-radius: 6px; background: #eee; }
        .no-img { width: 45px; height: 45px; background: #eee; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

        /* Badges stock */
        .stock-badge {
            display: inline-block; padding: 2px 10px;
            border-radius: 12px; font-size: 0.75rem; font-weight: 600;
        }
        .stock-ok { background: #e8f5e9; color: #2e7d32; }
        .stock-faible { background: #fff3e0; color: #e65100; }
        .stock-rupture { background: #ffebee; color: #c62828; }

        /* Tailles inline */
        .tailles-list {
            display: flex; flex-wrap: wrap; gap: 6px;
        }
        .taille-item {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 5px;
            font-size: 0.8rem; font-weight: 600;
            background: #f5f5f5; border: 1px solid #e0e0e0;
        }
        .taille-item .stock-input {
            width: 45px; padding: 2px 4px; text-align: center;
            border: 1px solid #ddd; border-radius: 3px;
            font-size: 0.8rem; font-weight: 600; outline: none;
        }
        .taille-item .stock-input:focus { border-color: #c9a84c; }
        .taille-item .stock-input.saved { border-color: #4caf50; background: #e8f5e9; transition: all 0.3s; }
        .taille-item .btn-del-taille {
            background: none; border: none; color: #ccc;
            cursor: pointer; font-size: 0.9rem; padding: 0 2px;
            transition: color 0.2s;
        }
        .taille-item .btn-del-taille:hover { color: #c62828; }

        /* Ajout taille */
        .add-taille-form {
            display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap;
        }
        .add-taille-form input {
            padding: 5px 10px; border: 1px solid #ddd; border-radius: 4px;
            font-size: 0.8rem; width: 70px; outline: none;
        }
        .add-taille-form input:focus { border-color: #c9a84c; }
        .add-taille-form button {
            padding: 5px 12px; border: none; border-radius: 4px;
            background: #c9a84c; color: #0a0a0a; font-weight: 600;
            font-size: 0.8rem; cursor: pointer;
        }
        .add-taille-form button:hover { background: #e0bb6a; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-actif { background: #e8f5e9; color: #2e7d32; }
        .badge-inactif { background: #ffebee; color: #c62828; }

        .empty-msg { text-align: center; color: #aaa; padding: 40px; }

        /* Pagination */
        .pagination {
            display: flex; justify-content: center; align-items: center;
            gap: 6px; margin-top: 25px; flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 36px; height: 36px; padding: 0 10px;
            border: 1px solid #ddd; border-radius: 6px;
            font-size: 0.85rem; color: #333; text-decoration: none;
            transition: all 0.2s;
        }
        .pagination a:hover { border-color: #c9a84c; color: #c9a84c; }
        .pagination .active { background: #c9a84c; color: #0a0a0a; border-color: #c9a84c; font-weight: 700; }
        .pagination .disabled { opacity: 0.3; cursor: not-allowed; }
        .pagination .page-info { color: #888; border: none; padding: 0 4px; }

        /* Flash */
        .flash-message { padding: 12px 18px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; }
        .flash-success { background: #e8f5e9; border: 1px solid #a5d6a7; color: #2e7d32; }
        .flash-error { background: #ffebee; border: 1px solid #ef9a9a; color: #c62828; }

        @media (max-width: 900px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand"><?= htmlspecialchars(SITE_NAME) ?></div>
    <div class="admin-info">👤 <?= htmlspecialchars($adminNom) ?></div>
    <nav>
        <a href="<?= URL_ROOT ?>/admin"><span>📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits"><span>👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/stocks" class="active"><span>📦</span> Stocks</a>
        <a href="<?= URL_ROOT ?>/admin/categories"><span>🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes"><span>📦</span> Commandes</a>
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
            <input type="text" name="search" placeholder="Rechercher un produit..." value="<?= htmlspecialchars($search) ?>">
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
    <div class="section">
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
                        $nbTailles  = intval($p['nb_tailles']);
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
                                <div class="tailles-list" id="tailles-<?= $p['id'] ?>">
                                    <?php foreach ($tailles as $t): ?>
                                        <div class="taille-item">
                                            <span><?= htmlspecialchars($t['taille']) ?>:</span>
                                            <input type="number" class="stock-input" value="<?= intval($t['stock']) ?>"
                                                   min="0" data-taille-id="<?= $t['id'] ?>"
                                                   data-produit-id="<?= $p['id'] ?>"
                                                   onchange="updateStock(this)">
                                            <button class="btn-del-taille" onclick="deleteTaille(<?= $t['id'] ?>, <?= $p['id'] ?>)" title="Supprimer">✕</button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <!-- Formulaire ajout taille -->
                                <div class="add-taille-form">
                                    <input type="text" placeholder="Taille" class="new-taille-input" id="new-taille-<?= $p['id'] ?>">
                                    <input type="number" placeholder="Stock" class="new-stock-input" id="new-stock-<?= $p['id'] ?>" value="0" min="0">
                                    <button onclick="addTaille(<?= $p['id'] ?>)">+ Ajouter</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
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
// ── Mise à jour stock (AJAX) ──
function updateStock(input) {
    const tailleId = input.dataset.tailleId;
    const stock = parseInt(input.value) || 0;

    fetch('<?= URL_ROOT ?>/admin/stocks/modifierStock', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'taille_id=' + tailleId + '&stock=' + stock
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            input.classList.add('saved');
            setTimeout(() => input.classList.remove('saved'), 1000);
        } else {
            alert('Erreur : ' + (data.error || 'Inconnue'));
        }
    })
    .catch(() => alert('Erreur réseau'));
}

// ── Ajouter une taille (AJAX) ──
function addTaille(produitId) {
    const tailleInput = document.getElementById('new-taille-' + produitId);
    const stockInput = document.getElementById('new-stock-' + produitId);
    const taille = tailleInput.value.trim();
    const stock = parseInt(stockInput.value) || 0;

    if (!taille) {
        alert('Veuillez saisir une taille');
        return;
    }

    fetch('<?= URL_ROOT ?>/admin/stocks/ajouterTaille', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'produit_id=' + produitId + '&taille=' + encodeURIComponent(taille) + '&stock=' + stock
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Recharger la page pour voir la nouvelle taille
            location.reload();
        } else {
            alert('Erreur : ' + (data.error || 'Inconnue'));
        }
    })
    .catch(() => alert('Erreur réseau'));
}

// ── Supprimer une taille (AJAX) ──
function deleteTaille(tailleId, produitId) {
    if (!confirm('Supprimer cette taille ?')) return;

    fetch('<?= URL_ROOT ?>/admin/stocks/supprimerTaille', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'taille_id=' + tailleId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur : ' + (data.error || 'Inconnue'));
        }
    })
    .catch(() => alert('Erreur réseau'));
}
</script>

</body>
</html>

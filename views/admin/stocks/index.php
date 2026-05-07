<?php
/**
 * @var array  $produits          Liste des produits avec stock
 * @var array  $taillesParProduit Tailles détaillées par produit
 * @var string $search            Recherche en cours
 * @var string $statut            Filtre statut stock
 * @var int    $page              Page courante
 * @var int    $totalPages        Nombre total de pages
 * @var int    $total             Nombre total de produits
 * @var array  $stats             Stats globales
 * @var array  $derniersMouvements Derniers mouvements
 * @var string $adminNom          Nom de l'admin connecté
 */
$pageTitle  = 'Stocks';
$activePage = 'stocks';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1>📋 Gestion des stocks</h1>
        <div class="subtitle">Seuil d'alerte : <strong><?= STOCK_SEUIL_ALERTE ?></strong> unités par taille</div>
    </div>
    <div class="header-actions">
        <a href="<?= URL_ROOT ?>/admin/stocks/exportPdf?<?= http_build_query(['search' => $search, 'statut' => $statut]) ?>" class="btn-export" id="exportPdf" target="_blank">📥 Export PDF</a>
        <button type="button" class="btn-reset-stock" id="btnResetStock">🗑️ Réinitialiser le stock</button>
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

<!-- Navigation rapide -->
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

<!-- Stats -->
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
    <div class="badge-group">
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
                        <th>Stock détaillé</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produits as $p):
                        $stockTotal = intval($p['stock_total']);
                        $classeStock = $stockTotal === 0 ? 'stock-rupture' : ($stockTotal <= STOCK_SEUIL_ALERTE ? 'stock-faible' : 'stock-ok');
                        $classeRow = $stockTotal === 0 ? 'row-rupture' : ($stockTotal <= STOCK_SEUIL_ALERTE ? 'row-faible' : '');
                        $maxStock = 20;
                        $pct = min(100, ($stockTotal / $maxStock) * 100);
                        $barClass = $stockTotal === 0 ? 'rupture' : ($stockTotal <= STOCK_SEUIL_ALERTE ? 'faible' : 'ok');
                        $tailles = $taillesParProduit[$p['id']] ?? [];
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
                                    <br><small class="text-muted"><?= htmlspecialchars($p['marque']) ?></small>
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
                                <?php if (!empty($tailles)): ?>
                                    <div class="stock-detail">
                                        <?php foreach ($tailles as $t):
                                            $s = intval($t['stock']);
                                            $cls = $s === 0 ? 'taille-rupture' : ($s <= STOCK_SEUIL_ALERTE ? 'taille-faible' : 'taille-ok');
                                        ?>
                                            <span class="taille-badge <?= $cls ?>">
                                                <span class="taille-label"><?= htmlspecialchars($t['taille']) ?></span>
                                                <span class="taille-qte"><?= $s ?><span class="unit">u</span></span>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
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
                        <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></small></td>
                        <td><strong><?= htmlspecialchars($m['produit_nom']) ?></strong></td>
                        <td><span class="type-badge type-<?= $m['type'] ?>"><?= $typeLabel[$m['type']] ?? $m['type'] ?></span></td>
                        <td><span class="<?= $estPositif ? 'qte-positive' : 'qte-negative' ?>"><?= $estPositif ? '+' : '-' ?><?= intval($m['quantite']) ?></span></td>
                        <td><small class="text-muted"><?= htmlspecialchars($m['reference'] ?? '-') ?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Modal réinitialisation stock -->
<div class="modal-overlay" id="resetModalOverlay" style="display:none;">
    <div class="modal" style="max-width:460px;">
        <div style="text-align:center;margin-bottom:16px;">
            <span style="font-size:2.5rem;line-height:1;">⚠️</span>
        </div>
        <h3 style="text-align:center;color:#c62828;">Réinitialisation complète du stock</h3>
        <div style="background:#fce4ec;border-left:4px solid #c62828;border-radius:4px;padding:12px 14px;margin:16px 0;font-size:0.88rem;line-height:1.7;color:#b71c1c;">
            <strong>Cette action est irréversible.</strong><br>
            Elle va supprimer :<br>
            • Toutes les quantités de stock (remises à 0)<br>
            • Tout l'historique des mouvements de stock
        </div>
        <div style="margin:16px 0;">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:0.9rem;">
                <input type="checkbox" id="resetConfirmCheck" style="width:16px;height:16px;cursor:pointer;">
                <span>Je comprends que cette action est irréversible</span>
            </label>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" id="btnResetCancel">Annuler</button>
            <form method="POST" action="<?= URL_ROOT ?>/admin/stocks/resetStock" style="display:inline;">
                <button type="submit" class="btn-danger" id="btnResetConfirm" disabled>🗑️ Tout réinitialiser</button>
            </form>
        </div>
    </div>
</div>

<style>
.btn-reset-stock {
    background: #c62828;
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    padding: 10px 18px;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s;
    font-family: inherit;
}
.btn-reset-stock:hover { background: #b71c1c; transform: translateY(-1px); }
</style>

<script>
(function () {
    var btn     = document.getElementById('btnResetStock');
    var overlay = document.getElementById('resetModalOverlay');
    var cancel  = document.getElementById('btnResetCancel');
    var check   = document.getElementById('resetConfirmCheck');
    var confirm = document.getElementById('btnResetConfirm');

    btn.addEventListener('click', function () {
        check.checked = false;
        confirm.disabled = true;
        overlay.style.display = 'flex';
    });

    function close() { overlay.style.display = 'none'; }
    cancel.addEventListener('click', close);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });

    check.addEventListener('change', function () {
        confirm.disabled = !this.checked;
    });
})();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

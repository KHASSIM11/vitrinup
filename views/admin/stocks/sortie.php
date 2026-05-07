<?php
/**
 * @var array  $commandes          Liste des commandes en cours
 * @var array  $statsCommandes     Stats des commandes par statut
 * @var array  $produits           Produits actifs (pour sortie manuelle)
 * @var array  $taillesParProduit  Tailles par produit
 * @var array  $dernieresSorties   Dernières sorties manuelles
 * @var string $adminNom           Nom de l'admin connecté
 */
$pageTitle  = 'Sortie de stock';
$activePage = 'stocks';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1>📤 Sortie de stock</h1>
        <div class="subtitle">Sorties manuelles et gestion des commandes clients</div>
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

<!-- ── SORTIE MANUELLE ───────────────────────────────── -->
<div class="entree-layout">
    <!-- Formulaire sortie manuelle -->
    <div class="card-entree">
        <div class="card-title">
            ✂️ Sortie manuelle
            <span class="title-badge">Perte · Vol · Défaut · Autre</span>
        </div>

        <form method="POST" action="<?= URL_ROOT ?>/admin/stocks/ajouterSortieManuelle" id="formSortieManuelle">

            <div class="product-selector">
                <label>👟 Produit</label>
                <select name="produit_id" id="smProduitSelect" required>
                    <option value="">— Sélectionnez un produit —</option>
                    <?php foreach ($produits as $p): ?>
                        <option value="<?= intval($p['id']) ?>">
                            <?= htmlspecialchars($p['nom']) ?>
                            <?= $p['marque'] ? ' (' . htmlspecialchars($p['marque']) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-top:14px;">
                <label>📏 Taille</label>
                <select name="taille_id" id="smTailleSelect" required disabled>
                    <option value="">— Sélectionnez d'abord un produit —</option>
                </select>
                <small class="help-text" id="smStockInfo" style="display:none;color:var(--text-muted);">Stock disponible : <strong id="smStockDispo">—</strong></small>
            </div>

            <div class="form-group" style="margin-top:14px;">
                <label>🔢 Quantité à sortir</label>
                <input type="number" name="quantite" id="smQuantite" min="1" value="1" required
                       placeholder="Ex : 2" style="max-width:140px;">
            </div>

            <div class="form-group" style="margin-top:14px;">
                <label>📋 Motif de la sortie *</label>
                <select name="motif" id="smMotif" required>
                    <option value="">— Choisissez un motif —</option>
                    <option value="Perte">📦 Perte</option>
                    <option value="Vol">🚨 Vol</option>
                    <option value="Article défectueux">🔧 Article défectueux</option>
                    <option value="Don / Cadeau">🎁 Don / Cadeau</option>
                    <option value="Correction d'inventaire">📊 Correction d'inventaire</option>
                    <option value="Autre">✏️ Autre</option>
                </select>
            </div>

            <div class="form-group" style="margin-top:14px;">
                <label>💬 Note (optionnel)</label>
                <input type="text" name="note" maxlength="200" placeholder="Précision supplémentaire...">
            </div>

            <div style="margin-top:18px;">
                <button type="submit" class="btn-submit" id="smSubmit" disabled>
                    📤 Enregistrer la sortie
                </button>
            </div>
        </form>
    </div>

    <!-- Feed dernières sorties manuelles -->
    <div class="card-feed">
        <div class="feed-title">
            🔄 Dernières sorties manuelles
            <span class="feed-counter"><?= count($dernieresSorties) ?></span>
        </div>
        <?php if (!empty($dernieresSorties)): ?>
            <div class="feed-list">
                <?php foreach ($dernieresSorties as $s): ?>
                    <div class="feed-item">
                        <div class="feed-qte" style="background:#fce4ec;color:#c62828;">−<?= intval($s['quantite']) ?></div>
                        <div class="feed-body">
                            <div class="feed-produit"><?= htmlspecialchars($s['produit_nom']) ?></div>
                            <div class="feed-taille">Taille <?= htmlspecialchars($s['taille']) ?></div>
                            <?php if ($s['reference']): ?>
                                <div class="feed-ref"><?= htmlspecialchars($s['reference']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="feed-time"><?= date('d/m H:i', strtotime($s['created_at'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="feed-empty">
                <div class="feed-empty-icon">📭</div>
                <p>Aucune sortie manuelle</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    var taillesData = <?= json_encode($taillesParProduit) ?>;

    var produitSel = document.getElementById('smProduitSelect');
    var tailleSel  = document.getElementById('smTailleSelect');
    var stockInfo  = document.getElementById('smStockInfo');
    var stockDispo = document.getElementById('smStockDispo');
    var qteInput   = document.getElementById('smQuantite');
    var submitBtn  = document.getElementById('smSubmit');
    var motifSel   = document.getElementById('smMotif');

    produitSel.addEventListener('change', function() {
        var pid = this.value;
        tailleSel.innerHTML = '<option value="">— Sélectionnez une taille —</option>';
        tailleSel.disabled = true;
        submitBtn.disabled = true;
        stockInfo.style.display = 'none';

        if (!pid || !taillesData[pid] || !taillesData[pid].length) return;

        taillesData[pid].forEach(function(t) {
            var opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = 'Taille ' + t.taille + ' — ' + t.stock + ' dispo';
            opt.dataset.stock = t.stock;
            tailleSel.appendChild(opt);
        });
        tailleSel.disabled = false;
    });

    tailleSel.addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        if (!opt || !opt.value) {
            stockInfo.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }
        var stock = parseInt(opt.dataset.stock, 10);
        stockDispo.textContent = stock;
        stockInfo.style.display = 'block';
        qteInput.max = stock;
        if (qteInput.value > stock) qteInput.value = stock || 1;
        checkReady();
    });

    function checkReady() {
        var ok = produitSel.value && tailleSel.value && motifSel.value && parseInt(qteInput.value, 10) > 0;
        submitBtn.disabled = !ok;
    }

    motifSel.addEventListener('change', checkReady);
    qteInput.addEventListener('input', checkReady);

    document.getElementById('formSortieManuelle').addEventListener('submit', function(e) {
        var opt = tailleSel.options[tailleSel.selectedIndex];
        var stock = opt ? parseInt(opt.dataset.stock, 10) : 0;
        var qte   = parseInt(qteInput.value, 10);
        if (qte > stock) {
            e.preventDefault();
            alert('Quantité (' + qte + ') supérieure au stock disponible (' + stock + ').');
        }
    });
})();
</script>

<hr style="border:none;border-top:1.5px solid #e8e8e8;margin:28px 0;">

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

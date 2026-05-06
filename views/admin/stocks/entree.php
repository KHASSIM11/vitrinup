<?php
/**
 * @var array  $produits          Liste des produits actifs
 * @var array  $taillesParProduit Tailles par produit
 * @var array  $dernieresEntrees  Dernières entrées enregistrées
 * @var string $adminNom          Nom de l'admin connecté
 */
$pageTitle  = 'Entrée de stock';
$activePage = 'stocks';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1>📥 Entrée de stock</h1>
        <div class="subtitle">Gérez les tailles et ajoutez du stock en un clic — interface temps réel</div>
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

<div class="entree-layout">
    <!-- COLONNE GAUCHE : Formulaire principal -->
    <div class="card-entree">
        <div class="card-title">
            🎯 Ajouter du stock
            <span class="title-badge">AJAX • Temps réel</span>
        </div>

        <div class="product-selector">
            <label>👟 Produit</label>
            <select id="produitSelect">
                <option value="">— Sélectionnez un produit —</option>
                <?php foreach ($produits as $p): ?>
                    <option value="<?= intval($p['id']) ?>" data-image="<?= htmlspecialchars($p['image'] ?? '') ?>">
                        <?= htmlspecialchars($p['nom']) ?>
                        <?= $p['marque'] ? ' (' . htmlspecialchars($p['marque']) . ')' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Panneau produit -->
        <div class="produit-panel" id="produitPanel">
            <div class="produit-header">
                <div class="ph-img-placeholder" id="phImgPlaceholder">👟</div>
                <img class="ph-img" id="phImg" src="" alt="" style="display:none;">
                <div class="ph-info">
                    <div class="ph-nom" id="phNom">—</div>
                    <div class="ph-marque" id="phMarque"></div>
                </div>
                <div class="ph-stats">
                    <strong id="phNbTailles">0</strong>
                    taille(s)
                </div>
            </div>

            <div id="taillesContainer">
                <div class="empty-tailles">
                    <div class="empty-icon">👟</div>
                    <p>Sélectionnez un produit</p>
                    <div class="empty-sub">pour gérer ses tailles et son stock</div>
                </div>
            </div>

            <!-- Ajout nouvelle taille -->
            <div class="add-taille-wrap" id="addTailleWrap" style="display:none;">
                <div class="at-label">➕ Nouvelle taille</div>
                <div class="at-row">
                    <input type="text" id="newTailleInput" placeholder="Taille (ex: 42)" maxlength="20">
                    <input type="number" id="newStockInput" placeholder="Stock" min="0" value="0">
                    <button class="btn-new-taille" id="btnAddTaille">➕ Ajouter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- COLONNE DROITE : Feed dernières entrées -->
    <div class="card-feed">
        <div class="feed-title">
            🔄 Dernières entrées
            <span class="feed-counter" id="feedCounter"><?= count($dernieresEntrees) ?></span>
        </div>

        <?php if (!empty($dernieresEntrees)): ?>
            <div class="feed-list" id="feedList">
                <?php foreach ($dernieresEntrees as $e): ?>
                    <div class="feed-item">
                        <div class="feed-qte">+<?= intval($e['quantite']) ?></div>
                        <div class="feed-body">
                            <div class="feed-produit"><?= htmlspecialchars($e['produit_nom']) ?></div>
                            <div class="feed-taille">Taille <?= htmlspecialchars($e['taille']) ?></div>
                            <?php if ($e['reference']): ?>
                                <div class="feed-ref"><?= htmlspecialchars($e['reference']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="feed-time"><?= date('d/m H:i', strtotime($e['created_at'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="feed-empty" id="feedEmpty">
                <div class="feed-empty-icon">📭</div>
                <p>Aucune entrée pour le moment</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
const taillesData = <?= json_encode($taillesParProduit) ?>;
const produitsData = <?= json_encode($produits) ?>;
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>

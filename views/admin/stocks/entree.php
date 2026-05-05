<?php
/**
 * @var array  $produits          Liste des produits actifs
 * @var array  $taillesParProduit Tailles par produit
 * @var array  $dernieresEntrees  Dernières entrées enregistrées
 * @var string $adminNom          Nom de l'admin connecté
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Entrée de stock — Admin</title>
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
            <h1>📥 Entrée de stock</h1>
            <div class="subtitle">Ajouter du stock manuellement pour un produit et une taille spécifique</div>
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

    <!-- Formulaire d'entrée -->
    <div class="card">
        <h2>📦 Nouvelle entrée</h2>
        <form method="POST" action="<?= URL_ROOT ?>/admin/stocks/ajouterEntree">
            <div class="form-row">
                <div class="form-group">
                    <label>Produit *</label>
                    <select name="produit_id" id="produitSelect" required>
                        <option value="">-- Choisir un produit --</option>
                        <?php foreach ($produits as $p): ?>
                            <option value="<?= intval($p['id']) ?>">
                                <?= htmlspecialchars($p['nom']) ?>
                                <?= $p['marque'] ? ' (' . htmlspecialchars($p['marque']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Taille *</label>
                    <select name="taille_id" id="tailleSelect" required>
                        <option value="">-- D'abord choisir un produit --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantité *</label>
                    <input type="number" name="quantite" min="1" value="1" required>
                </div>
            </div>
            <div class="form-group">
                <label>Référence / Note</label>
                <input type="text" name="reference" placeholder="Ex: Réapprovisionnement fournisseur, Retour client, Ajustement...">
                <div class="help-text">Optionnel — permet de tracer l'origine de l'entrée</div>
            </div>
            <button type="submit" class="btn-submit">📥 Ajouter l'entrée</button>
        </form>
    </div>

    <!-- Stock actuel par produit -->
    <div class="card">
        <h2>📊 Stock actuel par produit</h2>
        <?php if (empty($produits)): ?>
            <div class="empty-msg">
                <div class="empty-icon">📦</div>
                <p>Aucun produit actif trouvé.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Tailles / Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $p):
                            $tailles = $taillesParProduit[$p['id']] ?? [];
                        ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($p['nom']) ?></strong>
                                    <?php if ($p['marque']): ?>
                                        <br><small style="color:var(--text-muted)"><?= htmlspecialchars($p['marque']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($tailles)): ?>
                                        <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                            <?php foreach ($tailles as $t):
                                                $cls = intval($t['stock']) <= 0 ? 'stock-rupture' : (intval($t['stock']) <= STOCK_SEUIL_ALERTE ? 'stock-faible' : 'stock-ok');
                                            ?>
                                                <span class="stock-badge <?= $cls ?>">
                                                    <?= htmlspecialchars($t['taille']) ?>: <?= intval($t['stock']) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color:var(--text-muted);font-size:0.85rem;">Aucune taille définie</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Dernières entrées -->
    <?php if (!empty($dernieresEntrees)): ?>
    <div class="card">
        <h2>🕐 Dernières entrées enregistrées</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Produit</th>
                        <th>Taille</th>
                        <th>Quantité</th>
                        <th>Stock avant</th>
                        <th>Stock après</th>
                        <th>Référence</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dernieresEntrees as $e): ?>
                        <tr>
                            <td><small style="color:var(--text-muted)"><?= date('d/m/Y H:i', strtotime($e['created_at'])) ?></small></td>
                            <td><strong><?= htmlspecialchars($e['produit_nom']) ?></strong></td>
                            <td><?= htmlspecialchars($e['taille']) ?></td>
                            <td><span class="qte-positive">+<?= intval($e['quantite']) ?></span></td>
                            <td><?= intval($e['stock_avant']) ?></td>
                            <td><?= intval($e['stock_apres']) ?></td>
                            <td><small style="color:var(--text-muted)"><?= htmlspecialchars($e['reference'] ?? '-') ?></small></td>
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
const taillesData = <?= json_encode($taillesParProduit) ?>;
</script>
<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>
</body>
</html>

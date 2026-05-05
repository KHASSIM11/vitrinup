<?php
/**
 * @var array  $produits          Liste des produits actifs
 * @var array  $taillesParProduit Tailles par produit
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
        <h1>📥 Entrée de stock</h1>
        <a href="<?= URL_ROOT ?>/admin/stocks" class="btn-back">← Retour aux stocks</a>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash-message flash-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flash-message flash-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="card">
        <h2>Ajouter du stock</h2>
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
                <label>Référence / Note (optionnel)</label>
                <input type="text" name="reference" placeholder="Ex: Réapprovisionnement fournisseur, Retour client...">
            </div>
            <button type="submit" class="btn-submit">📥 Ajouter l'entrée</button>
        </form>
    </div>

    <div class="card">
        <h2>Stock actuel par produit</h2>
        <?php if (empty($produits)): ?>
            <p class="empty-msg">Aucun produit actif trouvé.</p>
        <?php else: ?>
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
                                    <br><small style="color:#888"><?= htmlspecialchars($p['marque']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($tailles)): ?>
                                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                        <?php foreach ($tailles as $t): ?>
                                            <span class="taille-item">
                                                <?= htmlspecialchars($t['taille']) ?>:
                                                <span style="color:<?= intval($t['stock']) <= 0 ? '#c62828' : (intval($t['stock']) <= STOCK_SEUIL_ALERTE ? '#e65100' : '#2e7d32') ?>">
                                                    <?= intval($t['stock']) ?>
                                                </span>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color:#aaa;font-size:0.85rem;">Aucune taille définie</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>

<script>
const URL_ROOT = '<?= URL_ROOT ?>';
const taillesData = <?= json_encode($taillesParProduit) ?>;
</script>
<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>
</body>
</html>

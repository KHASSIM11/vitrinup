<?php
/**
 * @var array  $produits          Liste des produits actifs
 * @var array  $taillesParProduit Tailles par produit
 * @var string $error             Message d'erreur
 * @var string $success           Message de succès
 * @var string $adminNom          Nom de l'admin connecté
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Entrée de stock — Admin</title>
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
        .btn-back { color: #888; text-decoration: none; font-size: 0.9rem; }
        .btn-back:hover { color: #c9a84c; }

        .card { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .card h2 { font-size: 1.1rem; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #eee; color: #444; }

        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 0.85rem; color: #666; margin-bottom: 6px; font-weight: 600; }
        select, input[type="number"], input[type="text"] {
            width: 100%; padding: 10px 14px;
            border: 1px solid #ddd; border-radius: 6px;
            font-size: 0.95rem; color: #1a1a1a;
            outline: none; transition: border-color 0.2s;
            background: #fff;
        }
        select:focus, input:focus { border-color: #c9a84c; }

        .btn-submit {
            padding: 12px 30px;
            background: #c9a84c; color: #0a0a0a;
            border: none; border-radius: 6px;
            font-size: 1rem; font-weight: 700; cursor: pointer;
            transition: background 0.2s;
        }
        .btn-submit:hover { background: #e0bb6a; }

        .flash-message { padding: 12px 18px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; }
        .flash-success { background: #e8f5e9; border: 1px solid #a5d6a7; color: #2e7d32; }
        .flash-error { background: #ffebee; border: 1px solid #ef9a9a; color: #c62828; }

        .stock-info { font-size: 0.85rem; color: #888; margin-top: 4px; }

        .form-row { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px; align-items: end; }

        @media (max-width: 900px) {
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
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
                    <select name="produit_id" id="produitSelect" required onchange="chargerTailles()">
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

    <!-- Liste des produits avec stock actuel -->
    <div class="card">
        <h2>Stock actuel par produit</h2>
        <?php if (empty($produits)): ?>
            <p style="color:#888;text-align:center;padding:20px;">Aucun produit actif trouvé.</p>
        <?php else: ?>
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left;padding:8px 10px;font-size:0.8rem;color:#888;border-bottom:2px solid #eee;">Produit</th>
                        <th style="text-align:left;padding:8px 10px;font-size:0.8rem;color:#888;border-bottom:2px solid #eee;">Tailles / Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produits as $p):
                        $tailles = $taillesParProduit[$p['id']] ?? [];
                    ?>
                        <tr>
                            <td style="padding:10px;border-bottom:1px solid #f5f5f5;">
                                <strong><?= htmlspecialchars($p['nom']) ?></strong>
                                <?php if ($p['marque']): ?>
                                    <br><small style="color:#888"><?= htmlspecialchars($p['marque']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td style="padding:10px;border-bottom:1px solid #f5f5f5;">
                                <?php if (!empty($tailles)): ?>
                                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                        <?php foreach ($tailles as $t): ?>
                                            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:5px;font-size:0.8rem;font-weight:600;background:#f5f5f5;border:1px solid #e0e0e0;">
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
// Données des tailles par produit (passées depuis PHP)
const taillesData = <?= json_encode($taillesParProduit) ?>;

function chargerTailles() {
    const produitId = document.getElementById('produitSelect').value;
    const tailleSelect = document.getElementById('tailleSelect');
    tailleSelect.innerHTML = '';

    if (!produitId) {
        tailleSelect.innerHTML = '<option value="">-- D\'abord choisir un produit --</option>';
        return;
    }

    const tailles = taillesData[produitId] || [];
    if (tailles.length === 0) {
        tailleSelect.innerHTML = '<option value="">-- Aucune taille disponible --</option>';
        return;
    }

    tailles.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t.id;
        opt.textContent = t.taille + ' (stock actuel: ' + t.stock + ')';
        tailleSelect.appendChild(opt);
    });
}
</script>

</body>
</html>

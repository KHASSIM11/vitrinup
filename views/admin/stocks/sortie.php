<?php
/**
 * @var array  $commandes Liste des commandes en cours
 * @var string $adminNom  Nom de l'admin connecté
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sortie de stock — Admin</title>
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

        .flash-message { padding: 12px 18px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; }
        .flash-success { background: #e8f5e9; border: 1px solid #a5d6a7; color: #2e7d32; }
        .flash-error { background: #ffebee; border: 1px solid #ef9a9a; color: #c62828; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 12px; font-size: 0.8rem; color: #888; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #eee; }
        td { padding: 12px; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-nouveau { background: #e3f2fd; color: #1565c0; }
        .badge-vu { background: #fff3e0; color: #e65100; }
        .badge-confirme { background: #e8f5e9; color: #2e7d32; }
        .badge-annule { background: #ffebee; color: #c62828; }

        .btn-action {
            padding: 6px 14px; border: none; border-radius: 5px;
            font-size: 0.8rem; font-weight: 600; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-confirmer { background: #e8f5e9; color: #2e7d32; }
        .btn-confirmer:hover { background: #c8e6c9; }
        .btn-annuler { background: #ffebee; color: #c62828; }
        .btn-annuler:hover { background: #ffcdd2; }

        .stock-info { font-size: 0.8rem; color: #888; }
        .stock-actuel { font-weight: 600; }
        .stock-actuel.ok { color: #2e7d32; }
        .stock-actuel.faible { color: #e65100; }
        .stock-actuel.rupture { color: #c62828; }

        .empty-msg { text-align: center; color: #aaa; padding: 40px; }

        @media (max-width: 900px) {
            table { font-size: 0.85rem; }
            td, th { padding: 8px; }
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
        <h1>📤 Sortie de stock</h1>
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
        <h2>Commandes en attente</h2>
        <?php if (empty($commandes)): ?>
            <p class="empty-msg">Aucune commande en attente.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Produit</th>
                        <th>Taille</th>
                        <th>Stock actuel</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $c):
                        $stockActuel = intval($c['stock_actuel']);
                        $classeStock = $stockActuel <= 0 ? 'rupture' : ($stockActuel <= STOCK_SEUIL_ALERTE ? 'faible' : 'ok');
                    ?>
                        <tr>
                            <td><strong>#<?= intval($c['id']) ?></strong></td>
                            <td>
                                <?= htmlspecialchars($c['client_nom'] ?? 'Anonyme') ?>
                                <?php if ($c['client_tel']): ?>
                                    <br><small style="color:#888"><?= htmlspecialchars($c['client_tel']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($c['produit_nom']) ?></strong>
                                <?php if ($c['marque']): ?>
                                    <br><small style="color:#888"><?= htmlspecialchars($c['marque']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($c['taille']) ?></td>
                            <td>
                                <span class="stock-actuel <?= $classeStock ?>">
                                    <?= $stockActuel ?> unité<?= $stockActuel > 1 ? 's' : '' ?>
                                </span>
                            </td>
                            <td><span class="badge badge-<?= $c['statut'] ?>"><?= ucfirst($c['statut']) ?></span></td>
                            <td><small style="color:#888"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></small></td>
                            <td>
                                <?php if ($c['statut'] === 'confirme'): ?>
                                    <form method="POST" action="<?= URL_ROOT ?>/admin/stocks/ajouterSortie" style="display:inline">
                                        <input type="hidden" name="commande_id" value="<?= intval($c['id']) ?>">
                                        <input type="hidden" name="action" value="annuler">
                                        <button type="submit" class="btn-action btn-annuler" onclick="return confirm('Annuler cette commande ? Le stock sera remis.')">↩ Annuler</button>
                                    </form>
                                <?php elseif ($c['statut'] === 'nouveau' || $c['statut'] === 'vu'): ?>
                                    <form method="POST" action="<?= URL_ROOT ?>/admin/stocks/ajouterSortie" style="display:inline">
                                        <input type="hidden" name="commande_id" value="<?= intval($c['id']) ?>">
                                        <input type="hidden" name="action" value="confirmer">
                                        <button type="submit" class="btn-action btn-confirmer" onclick="return confirm('Confirmer cette commande ? 1 unité sera déduite du stock.')">✅ Confirmer</button>
                                    </form>
                                    <form method="POST" action="<?= URL_ROOT ?>/admin/stocks/ajouterSortie" style="display:inline;margin-left:4px;">
                                        <input type="hidden" name="commande_id" value="<?= intval($c['id']) ?>">
                                        <input type="hidden" name="action" value="annuler">
                                        <button type="submit" class="btn-action btn-annuler" onclick="return confirm('Annuler cette commande ?')">✕ Annuler</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>
</body>
</html>

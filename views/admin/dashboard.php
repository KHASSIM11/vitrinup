<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; color: #1a1a1a; }

        /* SIDEBAR */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: 240px; height: 100vh;
            background: #0a0a0a;
            color: #f5f0eb;
            display: flex; flex-direction: column;
            z-index: 100;
        }
        .sidebar .brand {
            padding: 25px 20px;
            font-size: 1.2rem; font-weight: 700;
            color: #c9a84c; letter-spacing: 2px;
            border-bottom: 1px solid #1a1a1a;
        }
        .sidebar .admin-info {
            padding: 15px 20px;
            font-size: 0.8rem; color: #666;
            border-bottom: 1px solid #1a1a1a;
        }
        .sidebar nav { flex: 1; padding: 20px 0; }
        .sidebar nav a {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 20px;
            color: #888; text-decoration: none;
            font-size: 0.9rem; transition: all 0.2s;
        }
        .sidebar nav a:hover, .sidebar nav a.active {
            background: #141414; color: #c9a84c;
            border-left: 3px solid #c9a84c;
        }
        .sidebar nav a .icon { font-size: 1.1rem; }
        .sidebar .logout {
            padding: 20px;
            border-top: 1px solid #1a1a1a;
        }
        .sidebar .logout a {
            color: #666; text-decoration: none;
            font-size: 0.85rem;
        }
        .sidebar .logout a:hover { color: #ff6b6b; }

        /* MAIN */
        .main { margin-left: 240px; padding: 30px; }

        .page-header {
            margin-bottom: 30px;
        }
        .page-header h1 { font-size: 1.8rem; color: #1a1a1a; }
        .page-header p { color: #888; margin-top: 5px; }

        /* STATS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }
        .stat-card .label {
            font-size: 0.8rem; color: #888;
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .stat-card .value {
            font-size: 2.2rem; font-weight: 700; color: #1a1a1a;
        }
        .stat-card .value.gold { color: #c9a84c; }
        .stat-card .value.green { color: #25D366; }
        .stat-card .value.red { color: #e53935; }

        /* TABLE */
        .section {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }
        .section h2 {
            font-size: 1.1rem; margin-bottom: 20px;
            padding-bottom: 15px; border-bottom: 1px solid #eee;
        }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left; padding: 10px 12px;
            font-size: 0.8rem; color: #888;
            text-transform: uppercase; letter-spacing: 1px;
            border-bottom: 1px solid #eee;
        }
        td { padding: 12px; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; }
        tr:last-child td { border-bottom: none; }
        .badge {
            display: inline-block; padding: 3px 10px;
            border-radius: 20px; font-size: 0.75rem; font-weight: 600;
        }
        .badge-nouveau { background: #fff3e0; color: #e65100; }
        .badge-confirme { background: #e8f5e9; color: #2e7d32; }
        .badge-annule { background: #ffebee; color: #c62828; }
        .empty-msg { text-align: center; color: #aaa; padding: 30px; }

        @media (max-width: 900px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="brand"><?= htmlspecialchars(SITE_NAME) ?></div>
    <div class="admin-info">👤 <?= htmlspecialchars($adminNom) ?></div>
    <nav>
        <a href="<?= URL_ROOT ?>/admin" class="active"><span class="icon">📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits"><span class="icon">👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories"><span class="icon">🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes"><span class="icon">📦</span> Commandes</a>
        <a href="<?= URL_ROOT ?>" target="_blank"><span class="icon">🌐</span> Voir le site</a>
    </nav>
    <div class="logout">
        <a href="<?= URL_ROOT ?>/admin/logout">🚪 Déconnexion</a>
    </div>
</aside>

<!-- MAIN -->
<main class="main">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Bienvenue, <?= htmlspecialchars($adminNom) ?> 👋</p>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Produits actifs</div>
            <div class="value gold"><?= $totalActifs ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Produits inactifs</div>
            <div class="value"><?= $totalInactifs ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Total commandes</div>
            <div class="value green"><?= $totalCommandes ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Nouvelles commandes</div>
            <div class="value red"><?= $nouvellesCommandes ?></div>
        </div>
    </div>

    <!-- DERNIÈRES COMMANDES -->
    <div class="section">
        <h2>📦 Dernières commandes</h2>
        <?php if (empty($dernieresCmds)): ?>
            <p class="empty-msg">Aucune commande pour l'instant.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produit</th>
                        <th>Client</th>
                        <th>Taille</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dernieresCmds as $cmd): ?>
                        <tr>
                            <td><?= $cmd['id'] ?></td>
                            <td><?= htmlspecialchars($cmd['produit_nom']) ?></td>
                            <td><?= htmlspecialchars($cmd['client_nom'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($cmd['taille'] ?? '—') ?></td>
                            <td>
                                <span class="badge badge-<?= $cmd['statut'] ?>">
                                    <?= ucfirst($cmd['statut']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($cmd['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>

</body>
</html>

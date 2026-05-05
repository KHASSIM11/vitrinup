<?php
/**
 * @var array  $mouvements Liste des mouvements de stock
 * @var array  $produits   Liste des produits pour le filtre
 * @var string $typeFiltre Filtre par type de mouvement
 * @var int    $produitId  Filtre par produit
 * @var int    $page       Page courante
 * @var int    $totalPages Nombre total de pages
 * @var int    $total      Nombre total de mouvements
 * @var string $adminNom   Nom de l'admin connecté
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des stocks — Admin</title>
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

        /* Filtres */
        .filtres {
            display: flex; flex-wrap: wrap; gap: 10px;
            margin-bottom: 20px; align-items: center;
        }
        .filtres form { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; flex: 1; }
        .filtres select, .filtres input[type="text"] {
            padding: 9px 14px; border: 1px solid #ddd; border-radius: 6px;
            font-size: 0.9rem; outline: none; background: #fff;
        }
        .filtres select:focus, .filtres input:focus { border-color: #c9a84c; }
        .btn-filtre {
            padding: 8px 16px; border-radius: 20px;
            font-size: 0.85rem; font-weight: 600;
            text-decoration: none; transition: all 0.2s;
            background: #f5f5f5; color: #666; border: none; cursor: pointer;
        }
        .btn-filtre:hover { background: #e0e0e0; }
        .btn-filtre.actif { background: #c9a84c; color: #0a0a0a; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 12px; font-size: 0.8rem; color: #888; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #eee; }
        td { padding: 12px; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }

        .type-badge {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: 0.75rem; font-weight: 600;
        }
        .type-entree { background: #e8f5e9; color: #2e7d32; }
        .type-sortie { background: #fff3e0; color: #e65100; }
        .type-commande { background: #e3f2fd; color: #1565c0; }
        .type-annulation { background: #f3e5f5; color: #7b1fa2; }

        .qte-positive { color: #2e7d32; font-weight: 700; }
        .qte-negative { color: #c62828; font-weight: 700; }

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

        .total-info { font-size: 0.85rem; color: #888; margin-bottom: 15px; }

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
        <h1>📜 Historique des mouvements</h1>
        <a href="<?= URL_ROOT ?>/admin/stocks" class="btn-back">← Retour aux stocks</a>
    </div>

    <!-- Filtres -->
    <div class="card">
        <form method="GET" action="<?= URL_ROOT ?>/admin/stocks/historique" class="filtres">
            <select name="type">
                <option value="">Tous les types</option>
                <option value="entree" <?= $typeFiltre === 'entree' ? 'selected' : '' ?>>📥 Entrée</option>
                <option value="sortie" <?= $typeFiltre === 'sortie' ? 'selected' : '' ?>>📤 Sortie</option>
                <option value="commande" <?= $typeFiltre === 'commande' ? 'selected' : '' ?>>📦 Commande</option>
                <option value="annulation" <?= $typeFiltre === 'annulation' ? 'selected' : '' ?>>↩ Annulation</option>
            </select>
            <select name="produit_id">
                <option value="">Tous les produits</option>
                <?php foreach ($produits as $p): ?>
                    <option value="<?= intval($p['id']) ?>" <?= $produitId == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-filtre actif">🔍 Filtrer</button>
            <a href="<?= URL_ROOT ?>/admin/stocks/historique" class="btn-filtre">✕ Réinitialiser</a>
        </form>
    </div>

    <!-- Tableau -->
    <div class="card">
        <div class="total-info"><?= $total ?> mouvement<?= $total > 1 ? 's' : '' ?> trouvé<?= $total > 1 ? 's' : '' ?></div>

        <?php if (empty($mouvements)): ?>
            <p class="empty-msg">Aucun mouvement trouvé.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Produit</th>
                        <th>Taille</th>
                        <th>Type</th>
                        <th>Quantité</th>
                        <th>Stock avant</th>
                        <th>Stock après</th>
                        <th>Référence</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mouvements as $m):
                        $typeLabel = [
                            'entree' => '📥 Entrée',
                            'sortie' => '📤 Sortie',
                            'commande' => '📦 Commande',
                            'annulation' => '↩ Annulation'
                        ];
                        $qte = intval($m['quantite']);
                        $estPositif = in_array($m['type'], ['entree', 'annulation']);
                    ?>
                        <tr>
                            <td><small style="color:#888"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></small></td>
                            <td>
                                <strong><?= htmlspecialchars($m['produit_nom']) ?></strong>
                                <?php if ($m['marque']): ?>
                                    <br><small style="color:#888"><?= htmlspecialchars($m['marque']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($m['taille']) ?></td>
                            <td><span class="type-badge type-<?= $m['type'] ?>"><?= $typeLabel[$m['type']] ?? $m['type'] ?></span></td>
                            <td>
                                <span class="<?= $estPositif ? 'qte-positive' : 'qte-negative' ?>">
                                    <?= $estPositif ? '+' : '-' ?><?= $qte ?>
                                </span>
                            </td>
                            <td><?= intval($m['stock_avant']) ?></td>
                            <td><?= intval($m['stock_apres']) ?></td>
                            <td><small style="color:#888"><?= htmlspecialchars($m['reference'] ?? '-') ?></small></td>
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
                $baseUrl = URL_ROOT . '/admin/stocks/historique' . ($queryStr ? '?' . $queryStr : '');
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
</body>
</html>

<?php
/**
 * @var array  $commandes      Liste des commandes
 * @var string $statutActif    Statut actuellement filtré
 * @var array  $statsParStatut Statistiques par statut
 * @var int    $page           Page courante
 * @var int    $totalPages     Nombre total de pages
 * @var int    $total          Nombre total de commandes
 * @var string $adminNom       Nom de l'admin connecté
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commandes — Admin</title>
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
        .section { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }

        .filtres-statut {
            display: flex; flex-wrap: wrap; gap: 8px;
            margin-bottom: 25px; padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .btn-filtre {
            padding: 8px 18px; border-radius: 20px;
            font-size: 0.85rem; font-weight: 600;
            text-decoration: none; transition: all 0.2s;
            background: #f5f5f5; color: #666;
        }
        .btn-filtre:hover { background: #e0e0e0; }
        .btn-filtre.actif { background: #c9a84c; color: #0a0a0a; }
        .btn-filtre .count {
            background: rgba(0,0,0,0.1); padding: 1px 8px;
            border-radius: 10px; margin-left: 5px; font-size: 0.75rem;
        }
        .btn-filtre.actif .count { background: rgba(0,0,0,0.2); }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 12px; font-size: 0.8rem; color: #888; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #eee; }
        td { padding: 12px; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }
        .badge {
            display: inline-block; padding: 4px 12px;
            border-radius: 20px; font-size: 0.75rem; font-weight: 600;
        }
        .badge-nouveau { background: #fff3e0; color: #e65100; }
        .badge-vu { background: #e3f2fd; color: #1565c0; }
        .badge-confirme { background: #e8f5e9; color: #2e7d32; }
        .badge-annule { background: #ffebee; color: #c62828; }
        .btn-action {
            padding: 6px 14px; border-radius: 5px; font-size: 0.8rem;
            font-weight: 600; text-decoration: none; border: none; cursor: pointer;
            transition: opacity 0.2s; margin-right: 5px;
        }
        .btn-view { background: #e8f5e9; color: #2e7d32; }
        .btn-view:hover { opacity: 0.8; }
        .btn-delete { background: #ffebee; color: #c62828; }
        .btn-delete:hover { opacity: 0.8; }
        .empty-msg { text-align: center; color: #aaa; padding: 40px; }
        .flash { max-width: 1200px; margin: 0 auto 20px; }
        .flash-message { padding: 12px 18px; border-radius: 8px; margin-bottom: 10px; font-size: 0.9rem; }
        .flash-success { background: #e8f5e9; border: 1px solid #a5d6a7; color: #2e7d32; }
        .flash-error { background: #ffebee; border: 1px solid #ef9a9a; color: #c62828; }
        .client-info { font-size: 0.85rem; color: #666; }
        .client-info strong { color: #1a1a1a; }
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
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand"><?= htmlspecialchars(SITE_NAME) ?></div>
    <div class="admin-info">👤 <?= htmlspecialchars($adminNom) ?></div>
    <nav>
        <a href="<?= URL_ROOT ?>/admin"><span>📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits"><span>👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories"><span>🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes" class="active"><span>📦</span> Commandes</a>
        <a href="<?= URL_ROOT ?>" target="_blank"><span>🌐</span> Voir le site</a>
    </nav>
    <div class="logout"><a href="<?= URL_ROOT ?>/admin/logout">🚪 Déconnexion</a></div>
</aside>

<main class="main">
    <div class="page-header">
        <h1>📦 Commandes</h1>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash"><div class="flash-message flash-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flash"><div class="flash-message flash-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="section">
        <div class="filtres-statut">
            <?php
            $statuts = [
                '' => 'Toutes',
                'nouveau' => 'Nouveau',
                'vu' => 'Vu',
                'confirme' => 'Confirmé',
                'annule' => 'Annulé',
            ];
            $totalToutes = array_sum($statsParStatut);
            ?>
            <?php foreach ($statuts as $val => $label): ?>
                <?php $count = $val === '' ? $totalToutes : ($statsParStatut[$val] ?? 0); ?>
                <a href="<?= URL_ROOT ?>/admin/commandes<?= $val ? '?statut=' . $val : '' ?>"
                   class="btn-filtre <?= $statutActif === $val ? 'actif' : '' ?>">
                    <?= $label ?>
                    <span class="count"><?= $count ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($commandes)): ?>
            <p class="empty-msg">Aucune commande trouvée.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Produit</th>
                        <th>Taille</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $cmd): ?>
                        <tr>
                            <td><strong>#<?= $cmd['id'] ?></strong></td>
                            <td>
                                <div class="client-info">
                                    <strong><?= htmlspecialchars($cmd['client_nom'] ?? '—') ?></strong>
                                    <?php if (!empty($cmd['client_tel'])): ?>
                                        <br><?= htmlspecialchars($cmd['client_tel']) ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($cmd['produit_nom']) ?></td>
                            <td><?= htmlspecialchars($cmd['taille'] ?? '—') ?></td>
                            <td>
                                <span class="badge badge-<?= $cmd['statut'] ?>">
                                    <?= ucfirst($cmd['statut']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($cmd['created_at'])) ?></td>
                            <td>
                                <a href="<?= URL_ROOT ?>/admin/commandes/voir/<?= $cmd['id'] ?>" class="btn-action btn-view">👁️ Voir</a>
                                <a href="<?= URL_ROOT ?>/admin/commandes/supprimer/<?= $cmd['id'] ?>"
                                   class="btn-action btn-delete"
                                   onclick="return confirm('Supprimer cette commande ?')">🗑️</a>
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
                $baseUrl = URL_ROOT . '/admin/commandes' . (!empty($statutActif) ? '?statut=' . $statutActif : '');
                ?>
                <?php if ($page > 1): ?>
                    <a href="<?= $baseUrl ?>&page=<?= $page - 1 ?>">‹ Précédent</a>
                <?php else: ?>
                    <span class="disabled">‹ Précédent</span>
                <?php endif; ?>

                <?php
                $debut = max(1, $page - 2);
                $fin   = min($totalPages, $page + 2);
                if ($debut > 1): ?>
                    <a href="<?= $baseUrl ?>&page=1">1</a>
                    <?php if ($debut > 2): ?><span class="page-info">…</span><?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $debut; $i <= $fin; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($fin < $totalPages): ?>
                    <?php if ($fin < $totalPages - 1): ?><span class="page-info">…</span><?php endif; ?>
                    <a href="<?= $baseUrl ?>&page=<?= $totalPages ?>"><?= $totalPages ?></a>
                <?php endif; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= $baseUrl ?>&page=<?= $page + 1 ?>">Suivant ›</a>
                <?php else: ?>
                    <span class="disabled">Suivant ›</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

</body>

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
        <h1>📜 Historique des mouvements</h1>
        <a href="<?= URL_ROOT ?>/admin/stocks" class="btn-back">← Retour aux stocks</a>
    </div>

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

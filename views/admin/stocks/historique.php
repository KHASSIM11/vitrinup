<?php
/**
 * @var array  $mouvements Liste des mouvements de stock
 * @var array  $produits   Liste des produits pour le filtre
 * @var string $typeFiltre Filtre par type de mouvement
 * @var int    $produitId  Filtre par produit
 * @var int    $page       Page courante
 * @var int    $totalPages Nombre total de pages
 * @var int    $total      Nombre total de mouvements
 * @var array  $statsHisto Stats par type de mouvement
 * @var string $adminNom   Nom de l'admin connecté
 */
$pageTitle  = 'Historique des stocks';
$activePage = 'stocks';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1>📜 Historique des mouvements</h1>
        <div class="subtitle">Traçabilité complète des entrées, sorties, commandes et annulations</div>
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

<!-- Stats historiques -->
<?php if (!empty($statsHisto)): ?>
<div class="stats-grid">
    <?php
    $typeLabels = [
        'entree'     => ['label' => '📥 Entrées',     'class' => 'green'],
        'sortie'     => ['label' => '📤 Sorties',     'class' => 'orange'],
        'commande'   => ['label' => '📦 Commandes',   'class' => 'gold'],
        'annulation' => ['label' => '↩ Annulations', 'class' => 'red'],
    ];
    foreach ($statsHisto as $s):
        $info = $typeLabels[$s['type']] ?? ['label' => $s['type'], 'class' => ''];
    ?>
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="label"><?= $info['label'] ?></div>
            <div class="value <?= $info['class'] ?>"><?= intval($s['cnt']) ?></div>
            <div class="stat-change up"><?= intval($s['total_qte']) ?> unités</div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Filtres -->
<div class="card">
    <form method="GET" action="<?= URL_ROOT ?>/admin/stocks/historique" class="filtres">
        <select name="type">
            <option value="">Tous les types</option>
            <option value="entree"     <?= $typeFiltre === 'entree'     ? 'selected' : '' ?>>📥 Entrée</option>
            <option value="sortie"     <?= $typeFiltre === 'sortie'     ? 'selected' : '' ?>>📤 Sortie</option>
            <option value="commande"   <?= $typeFiltre === 'commande'   ? 'selected' : '' ?>>📦 Commande</option>
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

<!-- Tableau des mouvements -->
<div class="card">
    <div class="card-header">
        <h2>📋 Mouvements de stock</h2>
        <span class="total-info"><?= $total ?> mouvement<?= $total > 1 ? 's' : '' ?> trouvé<?= $total > 1 ? 's' : '' ?></span>
    </div>

    <?php if (empty($mouvements)): ?>
        <div class="empty-msg">
            <div class="empty-icon">📜</div>
            <p>Aucun mouvement trouvé.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
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
                            'entree'     => '📥 Entrée',
                            'sortie'     => '📤 Sortie',
                            'commande'   => '📦 Commande',
                            'annulation' => '↩ Annulation'
                        ];
                        $qte        = intval($m['quantite']);
                        $estPositif = in_array($m['type'], ['entree', 'annulation']);
                    ?>
                        <tr>
                            <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></small></td>
                            <td>
                                <strong><?= htmlspecialchars($m['produit_nom']) ?></strong>
                                <?php if ($m['marque']): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($m['marque']) ?></small>
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
                            <td><small class="text-muted"><?= htmlspecialchars($m['reference'] ?? '-') ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

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

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

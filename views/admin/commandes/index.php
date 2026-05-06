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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/css/admin.css">
</head>
<body>

<aside class="sidebar">
    <div class="brand"><?= htmlspecialchars(SITE_NAME) ?></div>
    <button class="hamburger" aria-label="Menu">☰</button>
    <div class="admin-info">👤 <?= htmlspecialchars($adminNom) ?></div>
    <nav>
        <a href="<?= URL_ROOT ?>/admin"><span>📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits"><span>👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories"><span>🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes" class="active"><span>📦</span> Commandes</a>
        <a href="<?= URL_ROOT ?>/admin/stocks"><span>📋</span> Stocks</a>
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
                        <th>Qté</th>
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
                            <td><strong><?= intval($cmd['quantite'] ?? 1) ?></strong></td>
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

<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>
</body>

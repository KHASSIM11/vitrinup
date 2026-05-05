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

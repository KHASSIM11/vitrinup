<?php
/**
 * @var array  $commande Détail de la commande
 * @var string $adminNom Nom de l'admin connecté
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande #<?= $commande['id'] ?> — Admin</title>
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
        <h1>📦 Commande #<?= $commande['id'] ?></h1>
        <a href="<?= URL_ROOT ?>/admin/commandes" class="btn-back">← Retour aux commandes</a>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash"><div class="flash-message flash-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <div class="grid-2">
        <!-- Infos commande -->
        <div class="card">
            <h2>📋 Informations commande</h2>
            <div class="info-row">
                <span class="label">Numéro</span>
                <span class="value">#<?= $commande['id'] ?></span>
            </div>
            <div class="info-row">
                <span class="label">Date</span>
                <span class="value"><?= date('d/m/Y H:i', strtotime($commande['created_at'])) ?></span>
            </div>
            <div class="info-row">
                <span class="label">Statut actuel</span>
                <span class="value">
                    <span class="badge badge-<?= $commande['statut'] ?>">
                        <?= ucfirst($commande['statut']) ?>
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="label">Produit</span>
                <span class="value">
                    <a href="<?= URL_ROOT ?>/produit/<?= $commande['produit_slug'] ?>" target="_blank">
                        <?= htmlspecialchars($commande['produit_nom']) ?>
                    </a>
                </span>
            </div>
            <div class="info-row">
                <span class="label">Taille</span>
                <span class="value"><?= htmlspecialchars($commande['taille'] ?? '—') ?></span>
            </div>
            <div class="info-row">
                <span class="label">Quantité</span>
                <span class="value"><?= intval($commande['quantite'] ?? 1) ?></span>
            </div>

            <!-- Changement de statut -->
            <form method="POST" action="<?= URL_ROOT ?>/admin/commandes/statut/<?= $commande['id'] ?>" class="statut-form">
                <select name="statut">
                    <?php
                    $statuts = [
                        'nouveau' => 'Nouveau',
                        'vu' => 'Vu',
                        'confirme' => 'Confirmé',
                        'annule' => 'Annulé',
                    ];
                    foreach ($statuts as $val => $label):
                    ?>
                        <option value="<?= $val ?>" <?= $commande['statut'] === $val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-update">Mettre à jour</button>
            </form>
        </div>

        <!-- Infos client -->
        <div class="card">
            <h2>👤 Informations client</h2>
            <div class="info-row">
                <span class="label">Nom</span>
                <span class="value"><?= htmlspecialchars($commande['client_nom'] ?? '—') ?></span>
            </div>
            <div class="info-row">
                <span class="label">Téléphone</span>
                <span class="value">
                    <?php if (!empty($commande['client_tel'])): ?>
                        <a href="tel:<?= htmlspecialchars($commande['client_tel']) ?>">
                            <?= htmlspecialchars($commande['client_tel']) ?>
                        </a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </span>
            </div>

            <?php if (!empty($commande['message'])): ?>
                <div style="margin-top:20px;padding-top:20px;border-top:1px solid #eee;">
                    <h3 style="font-size:0.9rem;color:#888;margin-bottom:10px;">📝 Message</h3>
                    <p style="font-size:0.9rem;color:#1a1a1a;"><?= nl2br(htmlspecialchars($commande['message'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- Bouton WhatsApp -->
            <?php if (!empty($commande['client_tel'])): ?>
                <div style="margin-top:20px;padding-top:20px;border-top:1px solid #eee;">
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $commande['client_tel']) ?>?text=Bonjour%20<?= urlencode($commande['client_nom'] ?? '') ?>%2C%20je%20vous%20contacte%20au%20sujet%20de%20votre%20commande%20%23<?= $commande['id'] ?>"
                       target="_blank" class="btn-whatsapp">
                        📲 Contacter sur WhatsApp
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>
</body>
</html>

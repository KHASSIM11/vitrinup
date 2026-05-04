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

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .card { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        .card h2 { font-size: 1rem; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #eee; color: #444; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: #888; }
        .info-row .value { font-weight: 600; color: #1a1a1a; }

        .badge { display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .badge-nouveau { background: #fff3e0; color: #e65100; }
        .badge-vu { background: #e3f2fd; color: #1565c0; }
        .badge-confirme { background: #e8f5e9; color: #2e7d32; }
        .badge-annule { background: #ffebee; color: #c62828; }

        .statut-form { display: flex; gap: 10px; align-items: center; margin-top: 15px; }
        .statut-form select { flex: 1; padding: 10px 14px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; outline: none; }
        .statut-form select:focus { border-color: #c9a84c; }
        .btn-update { padding: 10px 20px; background: #c9a84c; color: #0a0a0a; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        .btn-update:hover { background: #e0bb6a; }

        .btn-whatsapp { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #25D366; color: #fff; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: background 0.2s; }
        .btn-whatsapp:hover { background: #1ebe5d; }

        .flash { max-width: 1200px; margin: 0 auto 20px; }
        .flash-message { padding: 12px 18px; border-radius: 8px; margin-bottom: 10px; font-size: 0.9rem; }
        .flash-success { background: #e8f5e9; border: 1px solid #a5d6a7; color: #2e7d32; }

        @media (max-width: 900px) { .grid-2 { grid-template-columns: 1fr; } }
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

</body>
</html>

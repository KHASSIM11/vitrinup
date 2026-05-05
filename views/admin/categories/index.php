<?php
/**
 * @var array  $categories Liste des catégories
 * @var string $adminNom   Nom de l'admin connecté
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catégories — Admin</title>
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
        .btn-add {
            background: #c9a84c; color: #0a0a0a;
            padding: 10px 22px; border-radius: 6px;
            text-decoration: none; font-weight: 700;
            font-size: 0.9rem; transition: background 0.2s;
        }
        .btn-add:hover { background: #e0bb6a; }
        .section { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 12px; font-size: 0.8rem; color: #888; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #eee; }
        td { padding: 12px; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-produits { background: #e3f2fd; color: #1565c0; }
        .btn-action {
            padding: 6px 14px; border-radius: 5px; font-size: 0.8rem;
            font-weight: 600; text-decoration: none; border: none; cursor: pointer;
            transition: opacity 0.2s; margin-right: 5px;
        }
        .btn-edit { background: #e3f2fd; color: #1565c0; }
        .btn-edit:hover { opacity: 0.8; }
        .btn-delete { background: #ffebee; color: #c62828; }
        .btn-delete:hover { opacity: 0.8; }
        .empty-msg { text-align: center; color: #aaa; padding: 40px; }
        .flash {
            max-width: 1200px; margin: 0 auto 20px;
        }
        .flash-message {
            padding: 12px 18px; border-radius: 8px; margin-bottom: 10px; font-size: 0.9rem;
        }
        .flash-success { background: #e8f5e9; border: 1px solid #a5d6a7; color: #2e7d32; }
        .flash-error { background: #ffebee; border: 1px solid #ef9a9a; color: #c62828; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand"><?= htmlspecialchars(SITE_NAME) ?></div>
    <div class="admin-info">👤 <?= htmlspecialchars($adminNom) ?></div>
    <nav>
        <a href="<?= URL_ROOT ?>/admin"><span>📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits"><span>👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories" class="active"><span>🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes"><span>📦</span> Commandes</a>
        <a href="<?= URL_ROOT ?>" target="_blank"><span>🌐</span> Voir le site</a>
    </nav>
    <div class="logout"><a href="<?= URL_ROOT ?>/admin/logout">🚪 Déconnexion</a></div>
</aside>

<main class="main">
    <div class="page-header">
        <h1>🗂️ Catégories</h1>
        <a href="<?= URL_ROOT ?>/admin/categories/ajouter" class="btn-add">+ Ajouter une catégorie</a>
    </div>

    <!-- Flash messages -->
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flash">
            <div class="flash-message flash-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="section">
        <?php if (empty($categories)): ?>
            <p class="empty-msg">Aucune catégorie. <a href="<?= URL_ROOT ?>/admin/categories/ajouter">Ajouter la première</a></p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Ordre</th>
                        <th>Nom</th>
                        <th>Slug</th>
                        <th>Produits</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $cat['ordre'] ?></td>
                            <td><strong><?= htmlspecialchars($cat['nom']) ?></strong></td>
                            <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                            <td><span class="badge badge-produits"><?= $cat['nb_produits'] ?> produit(s)</span></td>
                            <td>
                                <a href="<?= URL_ROOT ?>/admin/categories/modifier/<?= $cat['id'] ?>" class="btn-action btn-edit">✏️ Modifier</a>
                                <a href="<?= URL_ROOT ?>/admin/categories/supprimer/<?= $cat['id'] ?>"
                                   class="btn-action btn-delete"
                                   onclick="return confirm('Supprimer cette catégorie ?')">🗑️ Supprimer</a>
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

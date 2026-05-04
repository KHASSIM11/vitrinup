<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $categorie ? 'Modifier' : 'Ajouter' ?> une catégorie — Admin</title>
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
        .main { margin-left: 240px; padding: 30px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .page-header h1 { font-size: 1.8rem; }
        .btn-back { color: #888; text-decoration: none; font-size: 0.9rem; }
        .btn-back:hover { color: #c9a84c; }
        .card { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); max-width: 600px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 0.85rem; color: #666; margin-bottom: 6px; font-weight: 600; }
        input[type="text"], input[type="number"] {
            width: 100%; padding: 10px 14px;
            border: 1px solid #ddd; border-radius: 6px;
            font-size: 0.95rem; color: #1a1a1a;
            outline: none; transition: border-color 0.2s;
        }
        input:focus { border-color: #c9a84c; }
        .btn-submit {
            width: 100%; padding: 14px;
            background: #c9a84c; color: #0a0a0a;
            border: none; border-radius: 6px;
            font-size: 1rem; font-weight: 700; cursor: pointer;
            transition: background 0.2s; letter-spacing: 1px;
        }
        .btn-submit:hover { background: #e0bb6a; }
        .error { background: #ffebee; border: 1px solid #ffcdd2; color: #c62828; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; }
        .help { font-size: 0.8rem; color: #aaa; margin-top: 4px; }
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
        <h1><?= $categorie ? '✏️ Modifier la catégorie' : '➕ Ajouter une catégorie' ?></h1>
        <a href="<?= URL_ROOT ?>/admin/categories" class="btn-back">← Retour à la liste</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST"
              action="<?= $categorie ? URL_ROOT . '/admin/categories/modifier/' . $categorie['id'] : URL_ROOT . '/admin/categories/ajouter' ?>">

            <div class="form-group">
                <label>Nom de la catégorie *</label>
                <input type="text" name="nom" required value="<?= htmlspecialchars($categorie['nom'] ?? '') ?>" placeholder="Ex: Baskets, Sandales, Bottes...">
            </div>

            <div class="form-group">
                <label>Ordre d'affichage</label>
                <input type="number" name="ordre" min="0" value="<?= $categorie['ordre'] ?? 0 ?>">
                <div class="help">Plus le chiffre est petit, plus la catégorie apparaît en premier.</div>
            </div>

            <button type="submit" class="btn-submit">
                <?= $categorie ? '💾 ENREGISTRER' : '➕ AJOUTER' ?>
            </button>
        </form>
    </div>
</main>

</body>
</html>

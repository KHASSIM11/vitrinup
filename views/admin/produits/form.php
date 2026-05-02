<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $produit ? 'Modifier' : 'Ajouter' ?> un produit — Admin</title>
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
        .form-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
        .card { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .card h2 { font-size: 1rem; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #eee; color: #444; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 0.85rem; color: #666; margin-bottom: 6px; font-weight: 600; }
        input[type="text"], input[type="number"], textarea, select {
            width: 100%; padding: 10px 14px;
            border: 1px solid #ddd; border-radius: 6px;
            font-size: 0.95rem; color: #1a1a1a;
            outline: none; transition: border-color 0.2s;
            background: #fff;
        }
        input:focus, textarea:focus, select:focus { border-color: #c9a84c; }
        textarea { resize: vertical; min-height: 100px; }
        .prix-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        /* Upload */
        .upload-zone {
            border: 2px dashed #ddd; border-radius: 8px;
            padding: 30px; text-align: center; cursor: pointer;
            transition: border-color 0.2s; background: #fafafa;
        }
        .upload-zone:hover { border-color: #c9a84c; }
        .upload-zone input { display: none; }
        .upload-zone .icon { font-size: 2rem; margin-bottom: 8px; }
        .upload-zone p { color: #888; font-size: 0.85rem; }
        .preview-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
        .preview-img { position: relative; }
        .preview-img img { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #eee; }
        .preview-img .del-img {
            position: absolute; top: -5px; right: -5px;
            background: #e53935; color: #fff; border: none;
            border-radius: 50%; width: 20px; height: 20px;
            font-size: 0.7rem; cursor: pointer; display: flex;
            align-items: center; justify-content: center;
        }
        /* Tailles */
        .tailles-container { display: flex; flex-direction: column; gap: 8px; }
        .taille-row { display: grid; grid-template-columns: 1fr 1fr auto; gap: 8px; align-items: center; }
        .taille-row input { margin: 0; }
        .btn-del-taille { background: #ffebee; color: #c62828; border: none; border-radius: 4px; padding: 8px 10px; cursor: pointer; font-size: 0.85rem; }
        .btn-add-taille {
            background: #f0f0f0; color: #444; border: none;
            border-radius: 6px; padding: 8px 16px; cursor: pointer;
            font-size: 0.85rem; margin-top: 8px; width: 100%;
        }
        .btn-add-taille:hover { background: #e0e0e0; }
        /* Submit */
        .btn-submit {
            width: 100%; padding: 14px;
            background: #c9a84c; color: #0a0a0a;
            border: none; border-radius: 6px;
            font-size: 1rem; font-weight: 700; cursor: pointer;
            transition: background 0.2s; letter-spacing: 1px;
        }
        .btn-submit:hover { background: #e0bb6a; }
        .toggle-statut { display: flex; gap: 10px; }
        .toggle-statut label { display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: normal; }
        .error { background: #ffebee; border: 1px solid #ffcdd2; color: #c62828; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand"><?= htmlspecialchars(SITE_NAME) ?></div>
    <div class="admin-info">👤 <?= htmlspecialchars($adminNom) ?></div>
    <nav>
        <a href="<?= URL_ROOT ?>/admin"><span>📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits" class="active"><span>👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories"><span>🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes"><span>📦</span> Commandes</a>
        <a href="<?= URL_ROOT ?>" target="_blank"><span>🌐</span> Voir le site</a>
    </nav>
    <div class="logout"><a href="<?= URL_ROOT ?>/admin/logout">🚪 Déconnexion</a></div>
</aside>

<main class="main">
    <div class="page-header">
        <h1><?= $produit ? '✏️ Modifier le produit' : '➕ Ajouter un produit' ?></h1>
        <a href="<?= URL_ROOT ?>/admin/produits" class="btn-back">← Retour à la liste</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data"
          action="<?= $produit ? URL_ROOT . '/admin/produits/modifier/' . $produit['id'] : URL_ROOT . '/admin/produits/ajouter' ?>">

        <div class="form-grid">
            <!-- COLONNE GAUCHE -->
            <div>
                <!-- Infos principales -->
                <div class="card">
                    <h2>📝 Informations</h2>
                    <div class="form-group">
                        <label>Nom du produit *</label>
                        <input type="text" name="nom" required value="<?= htmlspecialchars($produit['nom'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Marque</label>
                        <input type="text" name="marque" value="<?= htmlspecialchars($produit['marque'] ?? '') ?>" placeholder="Nike, Adidas, Puma...">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description"><?= htmlspecialchars($produit['description'] ?? '') ?></textarea>
                    </div>
                    <div class="prix-grid">
                        <div class="form-group">
                            <label>Prix (DH) *</label>
                            <input type="number" name="prix" step="0.01" min="0" required value="<?= $produit['prix'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Prix promo (DH)</label>
                            <input type="number" name="prix_promo" step="0.01" min="0" value="<?= $produit['prix_promo'] ?? '' ?>" placeholder="Laisser vide si aucune promo">
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="card">
                    <h2>📸 Photos</h2>

                    <?php if (!empty($images)): ?>
                        <div class="preview-grid" style="margin-bottom:15px">
                            <?php foreach ($images as $img): ?>
                                <div class="preview-img">
                                    <img src="<?= htmlspecialchars(UPLOAD_URL . $img['chemin']) ?>" alt="">
                                    <form method="POST" action="<?= URL_ROOT ?>/admin/produits/supprimerImage" style="display:inline">
                                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                                        <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
                                        <button type="submit" class="del-img" onclick="return confirm('Supprimer cette image ?')">×</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="upload-zone" onclick="document.getElementById('images').click()">
                        <input type="file" id="images" name="images[]" multiple accept="image/*" onchange="previewImages(this)">
                        <div class="icon">📷</div>
                        <p>Cliquer pour ajouter des photos<br><small>JPG, PNG, WEBP — max 2MB chacune</small></p>
                    </div>
                    <div class="preview-grid" id="newPreviews"></div>
                </div>

                <!-- Tailles -->
                <div class="card">
                    <h2>📐 Tailles & Stock</h2>
                    <div class="tailles-container" id="taillesContainer">
                        <?php
                        $taillesExistantes = !empty($tailles) ? $tailles : [];
                        foreach ($taillesExistantes as $t):
                        ?>
                            <div class="taille-row">
                                <input type="text" name="tailles[]" value="<?= htmlspecialchars($t['taille']) ?>" placeholder="Ex: 42">
                                <input type="number" name="stocks[]" value="<?= $t['stock'] ?>" min="0" placeholder="Stock">
                                <button type="button" class="btn-del-taille" onclick="this.parentElement.remove()">✕</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn-add-taille" onclick="ajouterTaille()">+ Ajouter une taille</button>
                </div>
            </div>

            <!-- COLONNE DROITE -->
            <div>
                <div class="card">
                    <h2>🗂️ Catégorie & Genre</h2>
                    <div class="form-group">
                        <label>Catégorie *</label>
                        <select name="categorie_id" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= isset($produit['categorie_id']) && $produit['categorie_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Genre</label>
                        <select name="genre">
                            <?php foreach (['homme' => 'Homme', 'femme' => 'Femme', 'enfant' => 'Enfant', 'mixte' => 'Mixte'] as $val => $label): ?>
                                <option value="<?= $val ?>" <?= isset($produit['genre']) && $produit['genre'] === $val ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="card">
                    <h2>⚙️ Statut</h2>
                    <div class="toggle-statut">
                        <label>
                            <input type="radio" name="statut" value="actif"
                                <?= !$produit || $produit['statut'] === 'actif' ? 'checked' : '' ?>>
                            ✅ Actif
                        </label>
                        <label>
                            <input type="radio" name="statut" value="inactif"
                                <?= isset($produit['statut']) && $produit['statut'] === 'inactif' ? 'checked' : '' ?>>
                            ❌ Inactif
                        </label>
                    </div>
                </div>

                <div class="card">
                    <button type="submit" class="btn-submit">
                        <?= $produit ? '💾 ENREGISTRER' : '➕ AJOUTER LE PRODUIT' ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
// Prévisualisation des nouvelles images
function previewImages(input) {
    const container = document.getElementById('newPreviews');
    container.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'preview-img';
            div.innerHTML = `<img src="${e.target.result}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;">`;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

// Ajouter une ligne taille
function ajouterTaille() {
    const container = document.getElementById('taillesContainer');
    const div = document.createElement('div');
    div.className = 'taille-row';
    div.innerHTML = `
        <input type="text" name="tailles[]" placeholder="Ex: 42">
        <input type="number" name="stocks[]" value="0" min="0" placeholder="Stock">
        <button type="button" class="btn-del-taille" onclick="this.parentElement.remove()">✕</button>
    `;
    container.appendChild(div);
}
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= isset($produit) && $produit ? 'Modifier' : 'Ajouter' ?> un produit — Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/css/admin.css">
</head>
<body>

<aside class="sidebar">
    <div class="brand"><?= htmlspecialchars(SITE_NAME) ?></div>
    <button class="hamburger" aria-label="Menu">☰</button>
    <div class="admin-info">👤 <?= htmlspecialchars($adminNom ?? '') ?></div>
    <nav>
        <a href="<?= URL_ROOT ?>/admin"><span>📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits" class="active"><span>👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories"><span>🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes"><span>📦</span> Commandes</a>
        <a href="<?= URL_ROOT ?>/admin/stocks"><span>📋</span> Stocks</a>
        <a href="<?= URL_ROOT ?>" target="_blank"><span>🌐</span> Voir le site</a>
    </nav>
    <div class="logout"><a href="<?= URL_ROOT ?>/admin/logout">🚪 Déconnexion</a></div>
</aside>

<main class="main">
    <div class="page-header">
        <h1><?= isset($produit) && $produit ? '✏️ Modifier le produit' : '➕ Ajouter un produit' ?></h1>
        <a href="<?= URL_ROOT ?>/admin/produits" class="btn-back">← Retour à la liste</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data"
          action="<?= isset($produit) && $produit ? URL_ROOT . '/admin/produits/modifier/' . $produit['id'] : URL_ROOT . '/admin/produits/ajouter' ?>">

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
                                        <input type="hidden" name="produit_id" value="<?= isset($produit) ? $produit['id'] : '' ?>">
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
            </div>

            <!-- COLONNE DROITE -->
            <div>
                <div class="card">
                    <h2>🗂️ Catégorie & Genre</h2>
                    <div class="form-group">
                        <label>Catégorie *</label>
                        <select name="categorie_id" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach (($categories ?? []) as $cat): ?>
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
                                <?= !isset($produit) || !$produit || $produit['statut'] === 'actif' ? 'checked' : '' ?>>
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
                        <?= isset($produit) && $produit ? '💾 ENREGISTRER' : '➕ AJOUTER LE PRODUIT' ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</main>

<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>

</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --bg-dark:    #0a0a0a;
            --bg-card:    #141414;
            --bg-light:   #f5f0eb;
            --accent:     #c9a84c;
            --accent-hover: #e0bb6a;
            --text-light: #f5f0eb;
            --text-dark:  #0a0a0a;
            --text-muted: #888;
            --promo:      #e53935;
            --border:     #222;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--bg-dark);
            color: var(--text-light);
            min-height: 100vh;
        }

        a { color: var(--accent); text-decoration: none; }

        /* ── HEADER ── */
        header {
            background: #0a0a0a;
            border-bottom: 1px solid var(--border);
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: 2px;
        }
        header nav {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        header nav a {
            color: var(--text-light);
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        header nav a:hover { color: var(--accent); }
        
        /* Panier icon */
        .panier-icon .badge {
            background: var(--accent);
            color: var(--text-dark);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 5px;
        }

        /* ── BARRE DE RECHERCHE ── */
        .search-section {
            background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-dark) 100%);
            padding: 60px 40px;
            text-align: center;
        }
        .search-section h1 {
            font-size: 2rem;
            margin-bottom: 30px;
            color: var(--accent);
        }
        .search-form {
            max-width: 700px;
            margin: 0 auto;
            position: relative;
        }
        .search-input-wrapper {
            display: flex;
            background: var(--bg-dark);
            border: 2px solid var(--border);
            border-radius: 50px;
            overflow: hidden;
            transition: border-color 0.3s;
        }
        .search-input-wrapper:focus-within {
            border-color: var(--accent);
        }
        .search-input-wrapper input {
            flex: 1;
            padding: 18px 30px;
            background: transparent;
            border: none;
            color: var(--text-light);
            font-size: 1.1rem;
            outline: none;
        }
        .search-input-wrapper input::placeholder {
            color: var(--text-muted);
        }
        .search-input-wrapper button {
            padding: 0 30px;
            background: var(--accent);
            border: none;
            color: var(--text-dark);
            font-size: 1.2rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .search-input-wrapper button:hover {
            background: var(--accent-hover);
        }
        .search-hints {
            margin-top: 20px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .search-hints span {
            display: inline-block;
            margin: 5px;
            padding: 5px 15px;
            background: var(--bg-card);
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .search-hints span:hover {
            background: var(--accent);
            color: var(--text-dark);
        }

        /* ── RÉSULTATS ── */
        .results-section {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .results-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        .results-header h2 {
            font-size: 1.3rem;
            color: var(--text-light);
        }
        .results-header .count {
            color: var(--accent);
            font-weight: 700;
        }
        .results-header .term {
            color: var(--accent);
            font-style: italic;
        }

        /* ── GRILLE PRODUITS ── */
        .produits-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
        .product-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.25s, border-color 0.25s;
            text-decoration: none;
            color: inherit;
        }
        .product-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent);
        }
        .product-card .img-wrap {
            position: relative;
            height: 240px;
            overflow: hidden;
            background: #111;
        }
        .product-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }
        .product-card:hover img {
            transform: scale(1.05);
        }
        .badge-genre {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--accent);
            color: var(--text-dark);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }
        .badge-promo {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--promo);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .product-card .info {
            padding: 16px;
        }
        .product-card .marque {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .product-card h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        .product-card .prix {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .prix-promo {
            color: var(--promo);
            font-weight: 700;
            font-size: 1.1rem;
        }
        .prix-normal {
            color: var(--accent);
            font-weight: 700;
            font-size: 1.1rem;
        }
        .prix-barre {
            color: var(--text-muted);
            text-decoration: line-through;
            font-size: 0.9rem;
        }

        /* ── AUCUN RÉSULTAT ── */
        .no-results {
            text-align: center;
            padding: 60px 20px;
        }
        .no-results .icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        .no-results h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .no-results p {
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        /* ── SUGGESTIONS ── */
        .suggestions {
            margin-top: 40px;
        }
        .suggestions h3 {
            text-align: center;
            margin-bottom: 25px;
            color: var(--text-muted);
        }

        /* ── FOOTER ── */
        footer {
            text-align: center;
            padding: 30px;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 1100px) {
            .produits-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 768px) {
            header { padding: 15px 20px; }
            .search-section { padding: 40px 20px; }
            .results-section { padding: 30px 20px; }
            .produits-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
            .product-card .img-wrap { height: 180px; }
        }
        @media (max-width: 480px) {
            .produits-grid { grid-template-columns: 1fr; }
            .search-input-wrapper input { padding: 15px 20px; font-size: 1rem; }
            .search-input-wrapper button { padding: 0 20px; }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header>
    <a href="<?= URL_ROOT ?>" class="logo"><?= htmlspecialchars(SITE_NAME) ?></a>
    <nav>
        <a href="<?= URL_ROOT ?>">Accueil</a>
        <a href="<?= URL_ROOT ?>/catalogue">Catalogue</a>
        <a href="<?= URL_ROOT ?>/panier" class="panier-icon">
            🛒 Panier<?= $panierCount > 0 ? '<span class="badge">' . $panierCount . '</span>' : '' ?>
        </a>
    </nav>
</header>

<!-- BARRE DE RECHERCHE -->
<section class="search-section">
    <h1>🔍 Rechercher un produit</h1>
    <form class="search-form" action="<?= URL_ROOT ?>/recherche" method="GET">
        <div class="search-input-wrapper">
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Nom, marque, catégorie..." autocomplete="off" autofocus>
            <button type="submit">🔍</button>
        </div>
    </form>
    <div class="search-hints">
        Recherches populaires : 
        <span onclick="document.querySelector('input[name=q]').value='Nike';document.querySelector('form').submit()">Nike</span>
        <span onclick="document.querySelector('input[name=q]').value='Adidas';document.querySelector('form').submit()">Adidas</span>
        <span onclick="document.querySelector('input[name=q]').value='Running';document.querySelector('form').submit()">Running</span>
        <span onclick="document.querySelector('input[name=q]').value='Homme';document.querySelector('form').submit()">Homme</span>
    </div>
</section>

<!-- RÉSULTATS -->
<section class="results-section">
    <?php if (!empty($q)): ?>
        
        <div class="results-header">
            <?php if ($nombreResultats > 0): ?>
                <h2><span class="count"><?= $nombreResultats ?></span> résultat(s) pour <span class="term">"<?= htmlspecialchars($q) ?>"</span></h2>
            <?php else: ?>
                <h2>Aucun résultat pour <span class="term">"<?= htmlspecialchars($q) ?>"</span></h2>
            <?php endif; ?>
        </div>
        
        <?php if ($nombreResultats > 0): ?>
            <div class="produits-grid">
                <?php foreach ($resultats as $produit): 
                    $image = $produit['image'] 
                        ? UPLOAD_URL . $produit['image'] 
                        : 'https://via.placeholder.com/400x300/141414/c9a84c?text=Photo';
                ?>
                    <a href="<?= URL_ROOT ?>/produit/<?= $produit['slug'] ?>" class="product-card">
                        <div class="img-wrap">
                            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                            <?php if (!empty($produit['genre'])): ?>
                                <span class="badge-genre"><?= htmlspecialchars($produit['genre']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($produit['prix_promo'])): ?>
                                <span class="badge-promo">PROMO</span>
                            <?php endif; ?>
                        </div>
                        <div class="info">
                            <?php if (!empty($produit['marque'])): ?>
                                <div class="marque"><?= htmlspecialchars($produit['marque']) ?></div>
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($produit['nom']) ?></h3>
                            <div class="prix">
                                <?php if (!empty($produit['prix_promo'])): ?>
                                    <span class="prix-promo"><?= number_format($produit['prix_promo'], 0, ',', ' ') ?> DH</span>
                                    <span class="prix-barre"><?= number_format($produit['prix'], 0, ',', ' ') ?> DH</span>
                                <?php else: ?>
                                    <span class="prix-normal"><?= number_format($produit['prix'], 0, ',', ' ') ?> DH</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
        <?php else: ?>
            
            <div class="no-results">
                <div class="icon">😕</div>
                <h3>Aucun produit trouvé</h3>
                <p>Essayez avec d'autres termes ou consultez nos suggestions ci-dessous.</p>
                <a href="<?= URL_ROOT ?>/catalogue" class="btn-primary" style="display:inline-block;background:var(--accent);color:var(--text-dark);padding:15px 40px;border-radius:8px;font-weight:600;text-decoration:none;">Voir tout le catalogue</a>
            </div>
            
            <?php if (!empty($suggestions)): ?>
                <div class="suggestions">
                    <h3>Vous pourriez aimer</h3>
                    <div class="produits-grid">
                        <?php foreach ($suggestions as $produit): 
                            $image = $produit['image'] 
                                ? UPLOAD_URL . $produit['image'] 
                                : 'https://via.placeholder.com/400x300/141414/c9a84c?text=Photo';
                        ?>
                            <a href="<?= URL_ROOT ?>/produit/<?= $produit['slug'] ?>" class="product-card">
                                <div class="img-wrap">
                                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                                </div>
                                <div class="info">
                                    <h3><?= htmlspecialchars($produit['nom']) ?></h3>
                                    <div class="prix">
                                        <?php if (!empty($produit['prix_promo'])): ?>
                                            <span class="prix-promo"><?= number_format($produit['prix_promo'], 0, ',', ' ') ?> DH</span>
                                        <?php else: ?>
                                            <span class="prix-normal"><?= number_format($produit['prix'], 0, ',', ' ') ?> DH</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
        <?php endif; ?>
        
    <?php else: ?>
        
        <div class="no-results">
            <div class="icon">🔍</div>
            <h3>Que recherchez-vous ?</h3>
            <p>Saisissez un nom de produit, une marque ou une catégorie dans la barre ci-dessus.</p>
        </div>
        
    <?php endif; ?>
</section>

<!-- FOOTER -->
<footer>
    <p><?= htmlspecialchars(SITE_NAME) ?> — <a href="https://wa.me/<?= WHATSAPP ?>">WhatsApp</a></p>
    <p>&copy; <?= date('Y') ?> Tous droits réservés.</p>
</footer>

</body>
</html>

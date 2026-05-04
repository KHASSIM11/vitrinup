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
            --success:    #25D366;
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
        
        /* Panier icon dans header */
        .panier-icon {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
        }
        .panier-icon .badge {
            background: var(--accent);
            color: var(--text-dark);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
        }

        /* ── PAGE TITLE ── */
        .page-title {
            padding: 40px;
            text-align: center;
        }
        .page-title h1 {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 10px;
        }
        .page-title p {
            color: var(--text-muted);
        }

        /* ── FLASH MESSAGES ── */
        .flash {
            max-width: 1200px;
            margin: 0 auto 20px;
            padding: 0 40px;
        }
        .flash-message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .flash-success {
            background: rgba(37, 211, 102, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
        }
        .flash-error {
            background: rgba(229, 57, 53, 0.1);
            border: 1px solid var(--promo);
            color: var(--promo);
        }

        /* ── PANIER VIDE ── */
        .panier-vide {
            text-align: center;
            padding: 80px 40px;
        }
        .panier-vide .icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .panier-vide h2 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .panier-vide p {
            color: var(--text-muted);
            margin-bottom: 30px;
        }
        .btn-primary {
            display: inline-block;
            background: var(--accent);
            color: var(--text-dark);
            padding: 15px 40px;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-primary:hover {
            background: var(--accent-hover);
        }

        /* ── PANIER CONTENU ── */
        .panier-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 40px 60px;
        }

        /* ── LISTE ARTICLES ── */
        .articles-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .article-card {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 20px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            align-items: center;
        }
        .article-image {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
            background: #111;
        }
        .article-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .article-info h3 {
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        .article-info .taille {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        .article-info .prix-unitaire {
            color: var(--accent);
            font-weight: 600;
        }
        .article-info .indisponible {
            color: var(--promo);
            font-size: 0.85rem;
            margin-top: 5px;
        }

        /* Quantité */
        .quantite-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantite-control button {
            width: 35px;
            height: 35px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-light);
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.2s;
        }
        .quantite-control button:hover {
            border-color: var(--accent);
            color: var(--accent);
        }
        .quantite-control span {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
        }

        /* Actions */
        .article-actions {
            text-align: right;
        }
        .prix-total {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 10px;
        }
        .btn-supprimer {
            color: var(--text-muted);
            font-size: 0.85rem;
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.2s;
        }
        .btn-supprimer:hover {
            color: var(--promo);
        }

        /* ── RÉCAPITULATIF ── */
        .recap-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 25px;
            position: sticky;
            top: 20px;
            height: fit-content;
        }
        .recap-card h2 {
            font-size: 1.2rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }
        .recap-ligne {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: var(--text-muted);
        }
        .recap-ligne.promo {
            color: var(--promo);
        }
        .recap-ligne.total {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-light);
            padding-top: 15px;
            border-top: 1px solid var(--border);
            margin-top: 15px;
        }
        .btn-vider {
            display: block;
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-muted);
            border-radius: 8px;
            margin: 20px 0;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-vider:hover {
            border-color: var(--promo);
            color: var(--promo);
        }
        .btn-commander {
            display: block;
            width: 100%;
            padding: 18px;
            background: var(--success);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            text-align: center;
        }
        .btn-commander:hover {
            background: #1ebe5d;
        }
        .info-livraison {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            font-size: 0.85rem;
            color: var(--text-muted);
            text-align: center;
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
        @media (max-width: 900px) {
            .panier-container {
                grid-template-columns: 1fr;
            }
            .recap-card {
                position: static;
            }
        }
        @media (max-width: 600px) {
            header { padding: 15px 20px; }
            .page-title, .flash { padding-left: 20px; padding-right: 20px; }
            .panier-container { padding: 0 20px 40px; }
            .article-card {
                grid-template-columns: 80px 1fr;
                grid-template-rows: auto auto;
            }
            .article-image {
                width: 80px;
                height: 80px;
            }
            .article-actions {
                grid-column: 1 / -1;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-top: 15px;
                border-top: 1px solid var(--border);
            }
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
            🛒 Panier
            <?php if ($nombreArticles > 0): ?>
                <span class="badge"><?= $nombreArticles ?></span>
            <?php endif; ?>
        </a>
    </nav>
</header>

<!-- PAGE TITLE -->
<div class="page-title">
    <h1>Mon Panier</h1>
    <p><?= $nombreArticles ?> article(s) dans votre panier</p>
</div>

<!-- FLASH MESSAGES -->
<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="flash">
        <div class="flash-message flash-success">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
        </div>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="flash">
        <div class="flash-message flash-error">
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
        </div>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<?php if ($panierVide): ?>

    <!-- PANIER VIDE -->
    <div class="panier-vide">
        <div class="icon">🛒</div>
        <h2>Votre panier est vide</h2>
        <p>Découvrez notre collection et ajoutez vos articles préférés !</p>
        <a href="<?= URL_ROOT ?>/catalogue" class="btn-primary">Voir le catalogue</a>
    </div>

<?php else: ?>

    <!-- PANIER AVEC ARTICLES -->
    <div class="panier-container">
        <!-- LISTE ARTICLES -->
        <div class="articles-list">
            <?php foreach ($articles as $cle => $article): ?>
                <div class="article-card">
                    <div class="article-image">
                        <?php if (!empty($article['image'])): ?>
                            <img src="<?= UPLOAD_URL . htmlspecialchars($article['image']) ?>" alt="">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/120x120/141414/c9a84c?text=Photo" alt="">
                        <?php endif; ?>
                    </div>
                    
                    <div class="article-info">
                        <h3><?= htmlspecialchars($article['nom']) ?></h3>
                        <div class="taille">Taille : <?= htmlspecialchars($article['taille']) ?></div>
                        <div class="prix-unitaire">
                            <?= number_format($article['prix'], 0, ',', ' ') ?> DH / unité
                        </div>
                        <?php if (!$article['disponible']): ?>
                            <div class="indisponible">⚠️ Produit indisponible</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="article-actions">
                        <div class="prix-total">
                            <?= number_format($article['prix'] * $article['quantite'], 0, ',', ' ') ?> DH
                        </div>
                        
                        <form method="POST" action="<?= URL_ROOT ?>/panier/modifier" style="display:inline;">
                            <div class="quantite-control">
                                <button type="submit" name="quantite" value="<?= $article['quantite'] - 1 ?>">−</button>
                                <span><?= $article['quantite'] ?></span>
                                <button type="submit" name="quantite" value="<?= $article['quantite'] + 1 ?>">+</button>
                            </div>
                            <input type="hidden" name="cle" value="<?= htmlspecialchars($cle) ?>">
                        </form>
                        
                        <form method="GET" action="<?= URL_ROOT ?>/panier/supprimer" style="display:inline;">
                            <input type="hidden" name="cle" value="<?= htmlspecialchars($cle) ?>">
                            <button type="submit" class="btn-supprimer">🗑️ Supprimer</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- RÉCAPITULATIF -->
        <div class="recap-card">
            <h2>Récapitulatif</h2>
            
            <div class="recap-ligne">
                <span>Sous-total</span>
                <span><?= number_format($total + $economie, 0, ',', ' ') ?> DH</span>
            </div>
            
            <?php if ($economie > 0): ?>
                <div class="recap-ligne promo">
                    <span>Économie</span>
                    <span>− <?= number_format($economie, 0, ',', ' ') ?> DH</span>
                </div>
            <?php endif; ?>
            
            <div class="recap-ligne total">
                <span>Total</span>
                <span><?= number_format($total, 0, ',', ' ') ?> DH</span>
            </div>
            
            <form method="GET" action="<?= URL_ROOT ?>/panier/vider">
                <button type="submit" class="btn-vider">Vider le panier</button>
            </form>
            
            <a href="<?= URL_ROOT ?>/panier/commander" class="btn-commander">
                📲 Commander via WhatsApp
            </a>
            
            <div class="info-livraison">
                🚚 Livraison gratuite à domicile<br>
                💳 Paiement à la livraison
            </div>
        </div>
    </div>

<?php endif; ?>

<!-- FOOTER -->
<footer>
    <p><?= htmlspecialchars(SITE_NAME) ?> — <a href="https://wa.me/<?= WHATSAPP ?>">WhatsApp</a></p>
    <p>&copy; <?= date('Y') ?> Tous droits réservés.</p>
</footer>

</body>
</html>

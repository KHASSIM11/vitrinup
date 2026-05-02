<?php
/**
 * Vue d'accueil moderne pour Vitrinup.
 * Variables attendues :
 *   - $title    : titre de la page
 *   - $produits : tableau des produits (id, nom, prix, prix_promo, image)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --bg-dark: #0a0a0a;
            --bg-light: #f5f0eb;
            --accent-gold: #c9a84c;
            --text-light: #f5f0eb;
            --text-dark: #0a0a0a;
            --price-promo: #e53935;
        }
        body {
            margin:0;
            font-family:Arial,Helvetica,sans-serif;
            background:var(--bg-dark);
            color:var(--text-light);
        }
        a { color:var(--accent-gold); text-decoration:none; }
        a:hover { text-decoration:underline; }

        /* HERO */
        .hero {
            height:100vh;
            background:linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)),url('https://via.placeholder.com/1920x1080/0a0a0a/f5f0eb?text=Hero');
            background-size:cover;
            background-position:center;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            text-align:center;
            padding:0 20px;
        }
        .hero h1 { font-size:3.5rem; margin:0; }
        .hero p { font-size:1.5rem; margin:20px 0; }
        .hero .btn {
            background:var(--accent-gold);
            color:var(--bg-dark);
            padding:12px 30px;
            border:none;
            border-radius:5px;
            font-weight:bold;
            cursor:pointer;
        }
        .hero .btn:hover { opacity:0.9; }

        /* PRODUCTS GRID */
        .products-section { padding:60px 20px; text-align:center; }
        .products-section h2 { margin-bottom:30px; font-size:2rem; }
        .products-grid {
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(250px,1fr));
            gap:20px;
            max-width:1200px;
            margin:0 auto;
        }
        .product-card {
            background:var(--bg-light);
            color:var(--text-dark);
            border-radius:8px;
            overflow:hidden;
            box-shadow:0 4px 12px rgba(0,0,0,0.3);
            display:flex;
            flex-direction:column;
        }
        .product-card img {
            width:100%;
            height:200px;
            object-fit:cover;
        }
        .product-card .info {
            padding:15px;
            flex-grow:1;
        }
        .product-card .info h3 { margin:0 0 10px; font-size:1.2rem; }
        .product-card .info .price {
            font-size:1.1rem;
            margin-bottom:10px;
        }
        .product-card .info .price .promo {
            color:var(--price-promo);
            font-weight:bold;
        }
        .product-card .whatsapp-btn {
            background:var(--accent-gold);
            color:var(--bg-dark);
            text-align:center;
            padding:10px;
            font-weight:bold;
        }
        .product-card .whatsapp-btn:hover { opacity:0.9; }

        /* CATEGORIES BANNER */
        .categories {
            display:flex;
            flex-wrap:wrap;
            margin:40px 0;
        }
        .category {
            flex:1 1 33.333%;
            position:relative;
            height:200px;
            background-size:cover;
            background-position:center;
            margin:5px;
            display:flex;
            align-items:center;
            justify-content:center;
            color:var(--text-light);
            font-size:1.5rem;
            font-weight:bold;
            text-shadow:0 2px 4px rgba(0,0,0,0.7);
        }
        .category::after {
            content:"";
            position:absolute;
            inset:0;
            background:rgba(0,0,0,0.4);
        }
        .category a {
            position:relative;
            z-index:2;
        }

        /* FOOTER */
        footer {
            background:var(--bg-dark);
            color:var(--text-light);
            text-align:center;
            padding:20px;
            font-size:0.9rem;
        }
        footer a.whatsapp {
            color:var(--accent-gold);
            font-weight:bold;
        }

        @media (max-width:768px) {
            .hero h1 {font-size:2.5rem;}
            .hero p {font-size:1.2rem;}
            .categories {flex-direction:column;}
            .category {flex:1 1 100%; height:150px;}
        }
    </style>
</head>
<body>

    <!-- HERO SECTION -->
    <section class="hero">
        <h1><?= htmlspecialchars(SITE_NAME) ?></h1>
        <p>Votre boutique de chaussures marocaine, moderne et luxueuse.</p>
        <a href="<?= URL_ROOT ?>/catalogue" class="btn">Voir la collection</a>
    </section>

    <!-- NOVELTIES SECTION -->
    <section class="products-section">
        <h2>Nouveautés</h2>
        <div class="products-grid">
            <?php foreach ($produits as $produit): ?>
                <div class="product-card">
                    <img src="<?= $produit['image'] ? htmlspecialchars(UPLOAD_URL . $produit['image']) : 'https://via.placeholder.com/400x300/0a0a0a/c9a84c?text=Photo' ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                    <div class="info">
                        <h3><?= htmlspecialchars($produit['nom']) ?></h3>
                        <div class="price">
                            <?php if (!empty($produit['prix_promo'])): ?>
                                <span class="promo"><?= number_format($produit['prix_promo'], 2, ',', ' ') ?> DH</span>
                                <del><?= number_format($produit['prix'], 2, ',', ' ') ?> DH</del>
                            <?php else: ?>
                                <?= number_format($produit['prix'], 2, ',', ' ') ?> DH
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                        $whatsappText = "Bonjour, je suis intéressé par : " . $produit['nom'];
                        $whatsappLink = "https://wa.me/" . WHATSAPP . "?text=" . rawurlencode($whatsappText);
                    ?>
                    <a href="<?= $whatsappLink ?>" target="_blank" class="whatsapp-btn">WhatsApp</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- CATEGORIES BANNER -->
    <section class="categories">
        <div class="category" style="background-image:url('https://via.placeholder.com/600x400/0a0a0a/f5f0eb?text=Homme');">
            <a href="<?= URL_ROOT ?>/catalogue?genre=homme">Homme</a>
        </div>
        <div class="category" style="background-image:url('https://via.placeholder.com/600x400/0a0a0a/f5f0eb?text=Femme');">
            <a href="<?= URL_ROOT ?>/catalogue?genre=femme">Femme</a>
        </div>
        <div class="category" style="background-image:url('https://via.placeholder.com/600x400/0a0a0a/f5f0eb?text=Enfant');">
            <a href="<?= URL_ROOT ?>/catalogue?genre=enfant">Enfant</a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p><?= htmlspecialchars(SITE_NAME) ?> – <a class="whatsapp" href="https://wa.me/<?= WHATSAPP ?>" target="_blank">WhatsApp : <?= WHATSAPP ?></a></p>
        <p>&copy; <?= date('Y') ?> Tous droits réservés.</p>
    </footer>

</body>
</html>

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
        /* Menu mobile */
        .mobile-menu-btn {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 21px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            z-index: 1002;
        }
        .mobile-menu-btn span {
            display: block;
            width: 100%;
            height: 3px;
            background: var(--text-light);
            border-radius: 3px;
            transition: all 0.3s ease;
        }
        .mobile-menu-btn.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }
        .mobile-menu-btn.active span:nth-child(2) {
            opacity: 0;
        }
        .mobile-menu-btn.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
        }
        
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            right: -100%;
            width: 80%;
            max-width: 300px;
            height: 100vh;
            background: var(--bg-dark);
            border-left: 1px solid #222;
            z-index: 1001;
            transition: right 0.3s ease;
            flex-direction: column;
            padding: 80px 25px 30px;
        }
        .mobile-menu.active {
            right: 0;
        }
        .mobile-menu a {
            color: var(--text-light);
            font-size: 1.1rem;
            padding: 15px 0;
            border-bottom: 1px solid #222;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .mobile-menu a .badge {
            background: var(--accent-gold);
            color: var(--text-dark);
            font-size: 0.8rem;
            padding: 3px 10px;
            border-radius: 15px;
        }
        
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
        }
        .overlay.active {
            display: block;
        }

        @media (max-width:768px) {
            .hero h1 {font-size:2.5rem;}
            .hero p {font-size:1.2rem;}
            .categories {flex-direction:column;}
            .category {flex:1 1 100%; height:150px;}
            header nav { display: none; }
            .mobile-menu-btn { display: flex; }
            .mobile-menu { display: flex; }
        }
    </style>
</head>
<body>

<?php
// Récupérer le nombre d'articles dans le panier
$panierCount = 0;
if (isset($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $item) {
        $panierCount += $item['quantite'];
    }
}
?>

    <!-- HEADER -->
    <header style="position:fixed;top:0;left:0;right:0;z-index:1000;background:rgba(10,10,10,0.95);border-bottom:1px solid #222;padding:15px 40px;display:flex;justify-content:space-between;align-items:center;">
        <a href="<?= URL_ROOT ?>" style="font-size:1.5rem;font-weight:700;color:#c9a84c;letter-spacing:2px;text-decoration:none;"><?= htmlspecialchars(SITE_NAME) ?></a>
        <nav>
            <a href="<?= URL_ROOT ?>" style="margin-left:25px;color:#f5f0eb;font-size:0.95rem;text-decoration:none;transition:color 0.2s;">Accueil</a>
            <a href="<?= URL_ROOT ?>/catalogue" style="margin-left:25px;color:#f5f0eb;font-size:0.95rem;text-decoration:none;transition:color 0.2s;">Catalogue</a>
            <a href="<?= URL_ROOT ?>/panier" style="margin-left:25px;color:#f5f0eb;font-size:0.95rem;text-decoration:none;transition:color 0.2s;">🛒 Panier<?= $panierCount > 0 ? ' <span style="background:#c9a84c;color:#0a0a0a;padding:2px 8px;border-radius:10px;font-size:0.75rem;font-weight:700;">' . $panierCount . '</span>' : '' ?></a>
        </nav>
        <button class="mobile-menu-btn" onclick="toggleMenu()" style="background:transparent;border:none;cursor:pointer;">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </header>
    
    <!-- Menu mobile -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="<?= URL_ROOT ?>" onclick="toggleMenu()">Accueil →</a>
        <a href="<?= URL_ROOT ?>/catalogue" onclick="toggleMenu()">Catalogue →</a>
        <a href="<?= URL_ROOT ?>/panier" onclick="toggleMenu()">🛒 Panier <?= $panierCount > 0 ? '<span class="badge">' . $panierCount . '</span>' : '→' ?></a>
    </div>
    <div class="overlay" id="overlay" onclick="toggleMenu()"></div>

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
                <a href="<?= URL_ROOT ?>/produit/<?= $produit['slug'] ?>" class="product-card" style="text-decoration:none;">
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
                    <span class="whatsapp-btn">Voir le produit</span>
                </a>
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
        <p><?= htmlspecialchars(SITE_NAME) ?></p>
        <p>&copy; <?= date('Y') ?> Tous droits réservés.</p>
    </footer>

<script>
function toggleMenu() {
    const menu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('overlay');
    const btn = document.querySelector('.mobile-menu-btn');
    menu.classList.toggle('active');
    overlay.classList.toggle('active');
    btn.classList.toggle('active');
    document.body.style.overflow = menu.classList.contains('active') ? 'hidden' : '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const menu = document.getElementById('mobileMenu');
        if (menu.classList.contains('active')) toggleMenu();
    }
});
</script>

</body>
</html>

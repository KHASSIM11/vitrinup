<?php
/** @var string $title Titre de la page */
/** @var array $produits Liste des 8 derniers produits */
/** @var array $promos Liste des produits en promo */
/** @var array $categories Liste des catégories */
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
            --bg-card: #141414;
            --bg-light: #f5f0eb;
            --accent: #c9a84c;
            --accent-hover: #e0bb6a;
            --text-light: #f5f0eb;
            --text-dark: #0a0a0a;
            --text-muted: #888;
            --promo: #e53935;
            --border: #222;
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
            position: fixed; top: 0; left: 0; right: 0;
            z-index: 1000;
            background: rgba(10,10,10,0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 15px 40px;
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
        header nav a {
            margin-left: 25px;
            color: var(--text-light);
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        header nav a:hover { color: var(--accent); }
        header nav a .badge {
            background: var(--accent);
            color: var(--text-dark);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 5px;
        }

        /* ── MOBILE MENU ── */
        .mobile-menu-btn {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px; height: 21px;
            background: transparent; border: none; cursor: pointer;
            padding: 0; z-index: 1002;
        }
        .mobile-menu-btn span {
            display: block; width: 100%; height: 3px;
            background: var(--text-light); border-radius: 3px;
            transition: all 0.3s ease;
        }
        .mobile-menu-btn.active span:nth-child(1) { transform: rotate(45deg) translate(6px,6px); }
        .mobile-menu-btn.active span:nth-child(2) { opacity: 0; }
        .mobile-menu-btn.active span:nth-child(3) { transform: rotate(-45deg) translate(7px,-7px); }
        .mobile-menu {
            display: none;
            position: fixed; top: 0; right: -100%;
            width: 80%; max-width: 300px; height: 100vh;
            background: var(--bg-dark);
            border-left: 1px solid var(--border);
            z-index: 1001;
            transition: right 0.3s ease;
            flex-direction: column;
            padding: 80px 25px 30px;
        }
        .mobile-menu.active { right: 0; }
        .mobile-menu a {
            color: var(--text-light); font-size: 1.1rem;
            padding: 15px 0; border-bottom: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
        }
        .mobile-menu a .badge {
            background: var(--accent); color: var(--text-dark);
            font-size: 0.8rem; padding: 3px 10px; border-radius: 15px;
        }
        .overlay {
            display: none;
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.7); z-index: 1000;
        }
        .overlay.active { display: block; }

        /* ── HERO ── */
        .hero {
            position: relative;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 20px;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
            z-index: 0;
        }
        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(ellipse at 20% 50%, rgba(201,168,76,0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 50%, rgba(201,168,76,0.05) 0%, transparent 50%);
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
        }
        .hero-badge {
            display: inline-block;
            background: rgba(201,168,76,0.15);
            border: 1px solid rgba(201,168,76,0.3);
            color: var(--accent);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.85rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 25px;
        }
        .hero h1 {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #f5f0eb 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            font-size: 1.2rem;
            color: var(--text-muted);
            margin-bottom: 35px;
            line-height: 1.6;
        }
        .hero-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .hero-actions .btn-primary {
            background: var(--accent);
            color: var(--text-dark);
            padding: 16px 40px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .hero-actions .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(201,168,76,0.3);
        }
        .hero-actions .btn-secondary {
            background: transparent;
            color: var(--text-light);
            padding: 16px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            border: 1px solid var(--border);
            transition: all 0.3s;
            cursor: pointer;
        }
        .hero-actions .btn-secondary:hover {
            border-color: var(--accent);
            color: var(--accent);
            transform: translateY(-2px);
        }
        .hero-scroll {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 0.75rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: bounce 2s infinite;
        }
        .hero-scroll .arrow {
            width: 20px; height: 20px;
            border-right: 2px solid var(--accent);
            border-bottom: 2px solid var(--accent);
            transform: rotate(45deg);
        }
        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(10px); }
        }

        /* ── SECTIONS ── */
        .section {
            padding: 80px 40px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }
        .section-header h2 {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 12px;
        }
        .section-header h2 span { color: var(--accent); }
        .section-header p {
            color: var(--text-muted);
            font-size: 1rem;
        }
        .section-header .line {
            width: 60px;
            height: 3px;
            background: var(--accent);
            margin: 15px auto 0;
            border-radius: 3px;
        }

        /* ── PROMOS ── */
        .promos-section {
            background: linear-gradient(180deg, var(--bg-dark) 0%, #0d0d0d 100%);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        .promos-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        /* ── PRODUCT CARDS ── */
        .product-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }
        .product-card:hover {
            transform: translateY(-6px);
            border-color: var(--accent);
            box-shadow: 0 12px 40px rgba(201,168,76,0.1);
        }
        .product-card .img-wrap {
            position: relative;
            height: 240px;
            overflow: hidden;
            background: #111;
        }
        .product-card .img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .product-card:hover .img-wrap img {
            transform: scale(1.08);
        }
        .product-card .badge-promo {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--promo);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 1px;
        }
        .product-card .badge-new {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--accent);
            color: var(--text-dark);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 1px;
        }
        .product-card .info {
            padding: 18px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .product-card .marque {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .product-card h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-light);
            line-height: 1.3;
        }
        .product-card .prix {
            margin-top: auto;
            padding-top: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .product-card .prix .promo-price {
            color: var(--promo);
            font-weight: 700;
            font-size: 1.1rem;
        }
        .product-card .prix .old-price {
            color: var(--text-muted);
            text-decoration: line-through;
            font-size: 0.9rem;
        }
        .product-card .prix .normal-price {
            color: var(--accent);
            font-weight: 700;
            font-size: 1.1rem;
        }
        .product-card .btn-card {
            display: block;
            background: var(--accent);
            color: var(--text-dark);
            text-align: center;
            padding: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        .product-card .btn-card:hover {
            background: var(--accent-hover);
        }

        /* ── NOUVEAUTÉS GRID ── */
        .nouveautes-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        /* ── CATÉGORIES ── */
        .categories-section {
            background: linear-gradient(180deg, #0d0d0d 0%, var(--bg-dark) 100%);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .category-card {
            position: relative;
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .category-card:hover {
            border-color: var(--accent);
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(201,168,76,0.1);
        }
        .category-card .bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
            z-index: 0;
        }
        .category-card .bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(201,168,76,0.1) 0%, transparent 50%);
            transition: opacity 0.3s;
        }
        .category-card:hover .bg::after {
            opacity: 0.5;
        }
        .category-card .content {
            position: relative;
            z-index: 1;
            text-align: center;
        }
        .category-card .content h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 8px;
        }
        .category-card .content p {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        .category-card .content .arrow {
            display: inline-block;
            margin-top: 12px;
            color: var(--accent);
            font-size: 1.2rem;
            transition: transform 0.3s;
        }
        .category-card:hover .content .arrow {
            transform: translateX(5px);
        }

        /* ── AVANTAGES ── */
        .avantages-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
        .avantage-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 35px 25px;
            text-align: center;
            transition: all 0.3s;
        }
        .avantage-card:hover {
            border-color: var(--accent);
            transform: translateY(-4px);
        }
        .avantage-card .icon {
            font-size: 2.5rem;
            margin-bottom: 18px;
        }
        .avantage-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-light);
        }
        .avantage-card p {
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* ── NEWSLETTER ── */
        .newsletter-section {
            background: linear-gradient(135deg, rgba(201,168,76,0.05) 0%, transparent 100%);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 60px 40px;
            text-align: center;
        }
        .newsletter-section h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        .newsletter-section p {
            color: var(--text-muted);
            margin-bottom: 25px;
        }
        .newsletter-form {
            display: flex;
            gap: 10px;
            max-width: 500px;
            margin: 0 auto;
            justify-content: center;
        }
        .newsletter-form input {
            flex: 1;
            padding: 14px 20px;
            border: 1px solid var(--border);
            border-radius: 50px;
            background: var(--bg-card);
            color: var(--text-light);
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .newsletter-form input:focus {
            border-color: var(--accent);
        }
        .newsletter-form button {
            padding: 14px 30px;
            background: var(--accent);
            color: var(--text-dark);
            border: none;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }
        .newsletter-form button:hover {
            background: var(--accent-hover);
        }
        .social-links {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }
        .social-links a {
            width: 45px;
            height: 45px;
            border: 1px solid var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            transition: all 0.2s;
            color: var(--text-muted);
        }
        .social-links a:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(201,168,76,0.1);
        }

        /* ── FOOTER ── */
        footer {
            padding: 40px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            border-top: 1px solid var(--border);
        }
        footer .footer-links {
            display: flex;
            gap: 25px;
            justify-content: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        footer .footer-links a {
            color: var(--text-muted);
            transition: color 0.2s;
        }
        footer .footer-links a:hover {
            color: var(--accent);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 1100px) {
            .nouveautes-grid,
            .promos-grid,
            .avantages-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            header { padding: 15px 20px; }
            header nav { display: none; }
            .mobile-menu-btn { display: flex; }
            .mobile-menu { display: flex; }
            .hero h1 { font-size: 2.5rem; }
            .hero p { font-size: 1rem; }
            .section { padding: 50px 20px; }
            .section-header h2 { font-size: 1.6rem; }
            .nouveautes-grid,
            .promos-grid,
            .avantages-grid { grid-template-columns: 1fr; }
            .categories-grid { grid-template-columns: 1fr; }
            .newsletter-form { flex-direction: column; }
            .newsletter-form input { width: 100%; }
        }
    </style>
</head>
<body>

<?php
$panierCount = 0;
if (isset($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $item) {
        $panierCount += $item['quantite'];
    }
}
?>

<!-- HEADER -->
<header>
    <a href="<?= URL_ROOT ?>" class="logo"><?= htmlspecialchars(SITE_NAME) ?></a>
    <nav>
        <a href="<?= URL_ROOT ?>">Accueil</a>
        <a href="<?= URL_ROOT ?>/catalogue">Catalogue</a>
        <a href="<?= URL_ROOT ?>/panier">🛒 Panier<?= $panierCount > 0 ? ' <span class="badge">' . $panierCount . '</span>' : '' ?></a>
    </nav>
    <button class="mobile-menu-btn" onclick="toggleMenu()">
        <span></span><span></span><span></span>
    </button>
</header>

<!-- Menu mobile -->
<div class="mobile-menu" id="mobileMenu">
    <a href="<?= URL_ROOT ?>" onclick="toggleMenu()">Accueil →</a>
    <a href="<?= URL_ROOT ?>/catalogue" onclick="toggleMenu()">Catalogue →</a>
    <a href="<?= URL_ROOT ?>/panier" onclick="toggleMenu()">🛒 Panier <?= $panierCount > 0 ? '<span class="badge">' . $panierCount . '</span>' : '→' ?></a>
</div>
<div class="overlay" id="overlay" onclick="toggleMenu()"></div>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-badge">✨ Nouvelle collection 2026</div>
        <h1>L'Élégance à<br>Chaque Pas</h1>
        <p>Découvrez notre sélection de chaussures haut de gamme,<br>alliant style, confort et qualité artisanale.</p>
        <div class="hero-actions">
            <a href="<?= URL_ROOT ?>/catalogue" class="btn-primary">Voir la collection</a>
            <a href="https://wa.me/<?= WHATSAPP ?>" target="_blank" class="btn-secondary">📱 Contactez-nous</a>
        </div>
    </div>
    <div class="hero-scroll">
        <span>Découvrir</span>
        <div class="arrow"></div>
    </div>
</section>

<!-- PROMOS -->
<?php if (!empty($promos)): ?>
<section class="promos-section">
    <div class="section">
        <div class="section-header">
            <h2>🔥 <span>Promotions</span> du moment</h2>
            <p>Des offres exceptionnelles sur une sélection de chaussures</p>
            <div class="line"></div>
        </div>
        <div class="promos-grid">
            <?php foreach ($promos as $produit):
                $img = $produit['image']
                    ? htmlspecialchars(UPLOAD_URL . $produit['image'])
                    : 'https://via.placeholder.com/400x300/141414/c9a84c?text=Photo';
            ?>
                <a href="<?= URL_ROOT ?>/produit/<?= $produit['slug'] ?>" class="product-card">
                    <div class="img-wrap">
                        <img src="<?= $img ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                        <span class="badge-promo">-<?= round((1 - $produit['prix_promo'] / $produit['prix']) * 100) ?>%</span>
                    </div>
                    <div class="info">
                        <?php if (!empty($produit['marque'])): ?>
                            <span class="marque"><?= htmlspecialchars($produit['marque']) ?></span>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($produit['nom']) ?></h3>
                        <div class="prix">
                            <span class="promo-price"><?= number_format($produit['prix_promo'], 0, ',', ' ') ?> DH</span>
                            <span class="old-price"><?= number_format($produit['prix'], 0, ',', ' ') ?> DH</span>
                        </div>
                    </div>
                    <span class="btn-card">Voir l'offre</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- NOUVEAUTÉS -->
<section class="section">
    <div class="section-header">
        <h2>🆕 <span>Nouveautés</span></h2>
        <p>Les dernières arrivées dans notre boutique</p>
        <div class="line"></div>
    </div>
    <div class="nouveautes-grid">
        <?php foreach ($produits as $produit):
            $img = $produit['image']
                ? htmlspecialchars(UPLOAD_URL . $produit['image'])
                : 'https://via.placeholder.com/400x300/141414/c9a84c?text=Photo';
        ?>
            <a href="<?= URL_ROOT ?>/produit/<?= $produit['slug'] ?>" class="product-card">
                <div class="img-wrap">
                    <img src="<?= $img ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                    <span class="badge-new">Nouveau</span>
                    <?php if (!empty($produit['prix_promo'])): ?>
                        <span class="badge-promo">PROMO</span>
                    <?php endif; ?>
                </div>
                <div class="info">
                    <?php if (!empty($produit['marque'])): ?>
                        <span class="marque"><?= htmlspecialchars($produit['marque']) ?></span>
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($produit['nom']) ?></h3>
                    <div class="prix">
                        <?php if (!empty($produit['prix_promo'])): ?>
                            <span class="promo-price"><?= number_format($produit['prix_promo'], 0, ',', ' ') ?> DH</span>
                            <span class="old-price"><?= number_format($produit['prix'], 0, ',', ' ') ?> DH</span>
                        <?php else: ?>
                            <span class="normal-price"><?= number_format($produit['prix'], 0, ',', ' ') ?> DH</span>
                        <?php endif; ?>
                    </div>
                </div>
                <span class="btn-card">Découvrir</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- CATÉGORIES -->
<?php if (!empty($categories)): ?>
<section class="categories-section">
    <div class="section">
        <div class="section-header">
            <h2>🗂️ <span>Catégories</span></h2>
            <p>Explorez notre collection par catégorie</p>
            <div class="line"></div>
        </div>
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="<?= URL_ROOT ?>/catalogue?categorie_id=<?= $cat['id'] ?>" class="category-card">
                    <div class="bg"></div>
                    <div class="content">
                        <h3><?= htmlspecialchars($cat['nom']) ?></h3>
                        <p><?= $cat['nb_produits'] ?> produit(s)</p>
                        <div class="arrow">→</div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- AVANTAGES -->
<section class="section">
    <div class="section-header">
        <h2>💎 <span>Pourquoi nous choisir</span></h2>
        <p>Ce qui fait la différence</p>
        <div class="line"></div>
    </div>
    <div class="avantages-grid">
        <div class="avantage-card">
            <div class="icon">🚚</div>
            <h3>Livraison rapide</h3>
            <p>Livraison dans toute la Maroc sous 24h à 72h. Suivi de commande disponible.</p>
        </div>
        <div class="avantage-card">
            <div class="icon">💳</div>
            <h3>Paiement à la livraison</h3>
            <p>Payez uniquement quand vous recevez votre commande. 100% sécurisé.</p>
        </div>
        <div class="avantage-card">
            <div class="icon">⭐</div>
            <h3>Qualité garantie</h3>
            <p>Des chaussures sélectionnées avec soin pour leur qualité et leur confort.</p>
        </div>
        <div class="avantage-card">
            <div class="icon">📱</div>
            <h3>Service client WhatsApp</h3>
            <p>Une question ? Contactez-nous directement sur WhatsApp, réponse sous 1h.</p>
        </div>
    </div>
</section>

<!-- NEWSLETTER / SOCIAL -->
<section class="newsletter-section">
    <h2>📬 Restez <span style="color:var(--accent)">connecté</span></h2>
    <p>Suivez-nous sur les réseaux sociaux pour ne rien manquer</p>
    <div class="social-links">
        <a href="https://wa.me/<?= WHATSAPP ?>" target="_blank" title="WhatsApp">📱</a>
        <a href="#" target="_blank" title="Instagram">📸</a>
        <a href="#" target="_blank" title="Facebook">👍</a>
        <a href="#" target="_blank" title="TikTok">🎵</a>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-links">
        <a href="<?= URL_ROOT ?>">Accueil</a>
        <a href="<?= URL_ROOT ?>/catalogue">Catalogue</a>
        <a href="https://wa.me/<?= WHATSAPP ?>" target="_blank">WhatsApp</a>
        <a href="<?= URL_ROOT ?>/panier">Panier</a>
    </div>
    <p><?= htmlspecialchars(SITE_NAME) ?> — &copy; <?= date('Y') ?> Tous droits réservés.</p>
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

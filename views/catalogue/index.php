<?php
/** @var string $title Titre de la page */
/** @var array $produits Liste des produits */
/** @var array $categories Liste des catégories */
/** @var string $filtreGenre Filtre genre actif */
/** @var string $filtreCategorie Filtre catégorie actif */
?>
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
            position: sticky;
            top: 0;
            z-index: 100;
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
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
        }
        
        /* Barre de recherche header */
        .header-search {
            flex: 1;
            max-width: 400px;
            margin: 0 30px;
        }
        .header-search div {
            display: flex;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 25px;
            overflow: hidden;
        }
        .header-search input {
            flex: 1;
            padding: 10px 20px;
            background: transparent;
            border: none;
            color: var(--text-light);
            outline: none;
        }
        .header-search button {
            padding: 0 20px;
            background: transparent;
            border: none;
            color: var(--accent);
            cursor: pointer;
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
            border-left: 1px solid var(--border);
            z-index: 1001;
            transition: right 0.3s ease;
            flex-direction: column;
            padding: 80px 25px 30px;
        }
        .mobile-menu.active {
            right: 0;
        }
        .mobile-menu form {
            display: flex;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 25px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .mobile-menu input {
            flex: 1;
            padding: 12px 20px;
            background: transparent;
            border: none;
            color: var(--text-light);
            outline: none;
        }
        .mobile-menu button {
            padding: 0 20px;
            background: transparent;
            border: none;
            color: var(--accent);
            cursor: pointer;
        }
        .mobile-menu a {
            color: var(--text-light);
            font-size: 1.1rem;
            padding: 15px 0;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .mobile-menu a .badge {
            background: var(--accent);
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

        /* ── PAGE TITLE ── */
        .page-title {
            padding: 50px 40px 20px;
            text-align: center;
        }
        .page-title h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .page-title p {
            color: var(--text-muted);
            margin-top: 8px;
        }

        /* ── FILTRES ── */
        .filtres {
            padding: 20px 40px 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--border);
        }

        .filtres-genre {
            display: flex;
            gap: 8px;
        }

        .btn-filtre {
            padding: 8px 20px;
            border: 1px solid var(--border);
            border-radius: 30px;
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-filtre:hover,
        .btn-filtre.actif {
            background: var(--accent);
            color: var(--text-dark);
            border-color: var(--accent);
            font-weight: 600;
        }

        .select-categorie {
            padding: 8px 16px;
            border: 1px solid var(--border);
            border-radius: 30px;
            background: transparent;
            color: var(--text-light);
            font-size: 0.9rem;
            cursor: pointer;
            outline: none;
        }
        .select-categorie option {
            background: #1a1a1a;
            color: var(--text-light);
        }

        /* ── GRILLE PRODUITS ── */
        .catalogue {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .produits-count {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 25px;
        }

        .produits-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        /* ── CARTE PRODUIT ── */
        .product-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.25s, border-color 0.25s;
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
            letter-spacing: 1px;
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
        .prix-promo {
            color: var(--promo);
            font-weight: 700;
            font-size: 1.1rem;
        }
        .prix-normal {
            color: var(--text-muted);
            text-decoration: line-through;
            font-size: 0.9rem;
        }
        .prix-seul {
            color: var(--accent);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .btn-whatsapp {
            display: block;
            background: #25D366;
            color: #fff;
            text-align: center;
            padding: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        .btn-whatsapp:hover {
            background: #1ebe5d;
            color: #fff;
        }

        /* ── AUCUN PRODUIT ── */
        .empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 20px;
            color: var(--text-muted);
        }
        .empty h2 { font-size: 1.5rem; margin-bottom: 10px; }

        /* ── FOOTER ── */
        footer {
            text-align: center;
            padding: 30px;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-top: 60px;
        }
        footer a { color: var(--accent); }

        /* ── RESPONSIVE ── */
        @media (max-width: 1100px) {
            .produits-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 768px) {
            header { padding: 15px 20px; }
            .header-search { display: none; }
            .desktop-nav { display: none; }
            .mobile-menu-btn { display: flex; }
            .mobile-menu { display: flex; }
            .page-title, .filtres, .catalogue { padding-left: 20px; padding-right: 20px; }
            .produits-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
            .product-card .img-wrap { height: 180px; }
        }
        @media (max-width: 480px) {
            .produits-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<?php
// Récupérer le nombre d'articles dans le panier
$panierCount = 0;
if (isset($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $item) {
        $panierCount += $item['quantite'];
    }
}
?>
<header>
    <a href="<?= URL_ROOT ?>" class="logo"><?= htmlspecialchars(SITE_NAME) ?></a>
    
    <!-- Barre de recherche desktop -->
    <form class="header-search" action="<?= URL_ROOT ?>/recherche" method="GET">
        <div>
            <input type="text" name="q" placeholder="Rechercher..." autocomplete="off">
            <button type="submit">🔍</button>
        </div>
    </form>
    
    <!-- Navigation desktop -->
    <nav class="desktop-nav">
        <a href="<?= URL_ROOT ?>">Accueil</a>
        <a href="<?= URL_ROOT ?>/catalogue">Catalogue</a>
        <a href="<?= URL_ROOT ?>/panier">🛒 Panier<?= $panierCount > 0 ? ' <span class="badge">' . $panierCount . '</span>' : '' ?></a>
    </nav>
    
    <!-- Bouton menu mobile -->
    <button class="mobile-menu-btn" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </button>
</header>

<!-- Menu mobile -->
<div class="mobile-menu" id="mobileMenu">
    <form action="<?= URL_ROOT ?>/recherche" method="GET">
        <input type="text" name="q" placeholder="Rechercher...">
        <button type="submit">🔍</button>
    </form>
    <a href="<?= URL_ROOT ?>" onclick="toggleMenu()">Accueil →</a>
    <a href="<?= URL_ROOT ?>/catalogue" onclick="toggleMenu()">Catalogue →</a>
    <a href="<?= URL_ROOT ?>/panier" onclick="toggleMenu()">🛒 Panier <?= $panierCount > 0 ? '<span class="badge">' . $panierCount . '</span>' : '→' ?></a>
</div>
<div class="overlay" id="overlay" onclick="toggleMenu()"></div>
</header>

<!-- TITRE -->
<div class="page-title">
    <h1>Notre Collection</h1>
    <p>Découvrez toutes nos chaussures</p>
</div>

<!-- FILTRES -->
<div class="filtres">
    <!-- Filtre catégorie -->
    <select class="select-categorie" id="selectCategorie" onchange="appliquerFiltre('categorie', this.value)">
        <option value="">Toutes les catégories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $filtreCategorie == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- CATALOGUE -->
<div class="catalogue">
    <p class="produits-count"><?= count($produits) ?> produit(s) trouvé(s)</p>

    <div class="produits-grid">
        <?php if (empty($produits)): ?>
            <div class="empty">
                <h2>Aucun produit trouvé</h2>
                <p>Essayez un autre filtre.</p>
            </div>
        <?php else: ?>
            <?php foreach ($produits as $produit): ?>
                <?php
                    $image = $produit['image']
                        ? htmlspecialchars(UPLOAD_URL . $produit['image'])
                        : 'https://via.placeholder.com/400x300/141414/c9a84c?text=Photo';
                    $whatsappText = "Bonjour, je suis intéressé par : " . $produit['nom'];
                    $whatsappLink = "https://wa.me/" . WHATSAPP . "?text=" . rawurlencode($whatsappText);
                ?>
                <a href="<?= URL_ROOT ?>/produit/<?= $produit['slug'] ?>" class="product-card" style="text-decoration:none;">
                    <div class="img-wrap">
                        <img src="<?= $image ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                        <?php if (!empty($produit['genre'])): ?>
                            <span class="badge-genre"><?= htmlspecialchars($produit['genre']) ?></span>
                        <?php endif; ?>
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
                                <span class="prix-promo"><?= number_format($produit['prix_promo'], 0, ',', ' ') ?> DH</span>
                                <span class="prix-normal"><?= number_format($produit['prix'], 0, ',', ' ') ?> DH</span>
                            <?php else: ?>
                                <span class="prix-seul"><?= number_format($produit['prix'], 0, ',', ' ') ?> DH</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="btn-whatsapp">
                        📲 Voir le produit
                    </span>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <p><?= htmlspecialchars(SITE_NAME) ?> — <a href="https://wa.me/<?= WHATSAPP ?>">WhatsApp</a></p>
    <p>&copy; <?= date('Y') ?> Tous droits réservés.</p>
</footer>

<script>
// Synchronisation des filtres : boutons genre + select catégorie
function appliquerFiltre(type, valeur) {
    const params = new URLSearchParams(window.location.search);
    
    if (type === 'genre') {
        if (valeur) {
            params.set('genre', valeur);
        } else {
            params.delete('genre');
        }
    } else if (type === 'categorie') {
        if (valeur) {
            params.set('categorie_id', valeur);
        } else {
            params.delete('categorie_id');
        }
    }
    
    // Construire l'URL
    let url = '<?= URL_ROOT ?>/catalogue';
    const qs = params.toString();
    if (qs) url += '?' + qs;
    
    window.location.href = url;
}

// Menu mobile
function toggleMenu() {
    const menu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('overlay');
    const btn = document.querySelector('.mobile-menu-btn');
    
    menu.classList.toggle('active');
    overlay.classList.toggle('active');
    btn.classList.toggle('active');
    
    document.body.style.overflow = menu.classList.contains('active') ? 'hidden' : '';
}

// Fermer avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const menu = document.getElementById('mobileMenu');
        if (menu.classList.contains('active')) {
            toggleMenu();
        }
    }
});
</script>

</body>
</html>

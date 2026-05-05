<?php
/** @var string $title Titre de la page */
/** @var array $produit Données du produit */
/** @var array $images Liste des images */
/** @var array $tailles Liste des tailles disponibles */
/** @var array $similaires Produits similaires */
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

        /* ── BREADCRUMB ── */
        .breadcrumb {
            padding: 20px 40px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .breadcrumb a { color: var(--text-muted); }
        .breadcrumb a:hover { color: var(--accent); }
        .breadcrumb span { color: var(--text-light); }

        /* ── PRODUCT DETAIL ── */
        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            padding: 20px 40px 60px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ── GALLERY ── */
        .gallery {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .main-image {
            width: 100%;
            aspect-ratio: 1;
            background: var(--bg-card);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--border);
            cursor: zoom-in;
            position: relative;
        }
        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .main-image:hover img {
            transform: scale(1.03);
        }
        .main-image .zoom-hint {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: rgba(0,0,0,0.6);
            color: #fff;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            backdrop-filter: blur(4px);
            pointer-events: none;
        }
        .thumbnails {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .thumbnail {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid var(--border);
            cursor: pointer;
            transition: all 0.2s;
            opacity: 0.6;
        }
        .thumbnail:hover, .thumbnail.active {
            border-color: var(--accent);
            opacity: 1;
        }
        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ── LIGHTBOX ── */
        .lightbox {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.92);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(8px);
        }
        .lightbox.open {
            display: flex;
        }
        .lightbox .lb-close {
            position: absolute;
            top: 25px;
            right: 35px;
            color: #fff;
            font-size: 2.5rem;
            cursor: pointer;
            transition: color 0.2s;
            z-index: 10;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            border: none;
        }
        .lightbox .lb-close:hover {
            color: var(--accent);
            background: rgba(255,255,255,0.2);
        }
        .lightbox .lb-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            pointer-events: none;
        }
        .lightbox .lb-nav button {
            pointer-events: auto;
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: none;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            font-size: 1.8rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .lightbox .lb-nav button:hover {
            background: var(--accent);
            color: #0a0a0a;
        }
        .lightbox .lb-image {
            max-width: 85vw;
            max-height: 85vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            animation: lbFadeIn 0.3s ease;
        }
        @keyframes lbFadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to   { opacity: 1; transform: scale(1); }
        }
        .lightbox .lb-counter {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
            background: rgba(0,0,0,0.5);
            padding: 6px 18px;
            border-radius: 20px;
        }
        .lightbox .lb-thumbs {
            position: absolute;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            padding: 10px 15px;
            background: rgba(0,0,0,0.5);
            border-radius: 12px;
            max-width: 90vw;
            overflow-x: auto;
        }
        .lightbox .lb-thumbs img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.2s;
            opacity: 0.5;
        }
        .lightbox .lb-thumbs img:hover,
        .lightbox .lb-thumbs img.active {
            border-color: var(--accent);
            opacity: 1;
        }
        /* Masquer scrollbar sur la lightbox */
        .lightbox .lb-thumbs::-webkit-scrollbar { height: 4px; }
        .lightbox .lb-thumbs::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 4px; }

        /* ── PRODUCT INFO ── */
        .product-info {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        .product-info .marque {
            color: var(--accent);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .product-info h1 {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .product-info .categorie {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .product-info .description {
            color: var(--text-muted);
            line-height: 1.7;
            font-size: 1rem;
        }

        /* ── PRIX ── */
        .prix-section {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .prix-promo {
            font-size: 2rem;
            font-weight: 700;
            color: var(--promo);
        }
        .prix-normal {
            font-size: 1.5rem;
            color: var(--text-light);
        }
        .prix-barre {
            font-size: 1.2rem;
            color: var(--text-muted);
            text-decoration: line-through;
        }
        .badge-promo {
            background: var(--promo);
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* ── TAILLES ── */
        .tailles-section h3 {
            font-size: 1rem;
            margin-bottom: 12px;
            color: var(--text-light);
        }
        .tailles-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .taille-btn {
            min-width: 55px;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: transparent;
            color: var(--text-light);
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }
        .taille-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }
        .taille-btn.selected {
            background: var(--accent);
            color: var(--text-dark);
            border-color: var(--accent);
            font-weight: 600;
        }
        .taille-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        .stock-info {
            margin-top: 10px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* ── BOUTON WHATSAPP ── */
        .btn-whatsapp {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #25D366;
            color: #fff;
            padding: 18px 40px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: background 0.2s;
            margin-top: 10px;
        }
        .btn-whatsapp:hover {
            background: #1ebe5d;
        }
        .btn-whatsapp:disabled {
            background: var(--text-muted);
            cursor: not-allowed;
        }

        /* ── QUANTITÉ ── */
        .quantite-section h3 {
            font-size: 1rem;
            margin-bottom: 12px;
            color: var(--text-light);
        }
        .quantite-selector {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .quantite-selector button {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-light);
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.2s;
        }
        .quantite-selector button:hover {
            border-color: var(--accent);
            color: var(--accent);
        }
        .quantite-selector input {
            width: 60px;
            height: 40px;
            text-align: center;
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text-light);
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
        }

        /* ── PRODUITS SIMILAIRES ── */
        .similaires {
            padding: 60px 40px;
            border-top: 1px solid var(--border);
        }
        .similaires h2 {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 40px;
            color: var(--accent);
        }
        .similaires-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .product-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.25s, border-color 0.25s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent);
        }
        .product-card .img-wrap {
            height: 200px;
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
        .product-card .info {
            padding: 15px;
        }
        .product-card h3 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .product-card .prix {
            font-size: 1rem;
            color: var(--accent);
            font-weight: 700;
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
            .product-detail {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .similaires-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 600px) {
            header { padding: 15px 20px; }
            .breadcrumb, .product-detail, .similaires {
                padding-left: 20px;
                padding-right: 20px;
            }
            .product-info h1 { font-size: 1.6rem; }
            .similaires-grid { grid-template-columns: 1fr; }
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
        <a href="https://wa.me/<?= WHATSAPP ?>" target="_blank">WhatsApp</a>
    </nav>
</header>

<!-- BREADCRUMB -->
<div class="breadcrumb">
    <a href="<?= URL_ROOT ?>">Accueil</a> / 
    <a href="<?= URL_ROOT ?>/catalogue">Catalogue</a> / 
    <span><?= htmlspecialchars($produit['nom']) ?></span>
</div>

<!-- PRODUCT DETAIL -->
<div class="product-detail">
    <!-- GALLERY -->
    <div class="gallery">
        <div class="main-image">
            <?php 
                $mainImage = !empty($images) 
                    ? UPLOAD_URL . $images[0]['chemin'] 
                    : 'https://via.placeholder.com/600x600/141414/c9a84c?text=Photo';
            ?>
            <img id="mainImage" src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
            <div class="zoom-hint">🔍 Cliquez pour zoomer</div>
        </div>
        <?php if (count($images) > 1): ?>
        <div class="thumbnails">
            <?php foreach ($images as $index => $img): ?>
                <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" 
                     onclick="changeImage('<?= UPLOAD_URL . $img['chemin'] ?>', this)">
                    <img src="<?= UPLOAD_URL . $img['chemin'] ?>" alt="">
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- PRODUCT INFO -->
    <div class="product-info">
        <?php if (!empty($produit['marque'])): ?>
            <div class="marque"><?= htmlspecialchars($produit['marque']) ?></div>
        <?php endif; ?>
        
        <h1><?= htmlspecialchars($produit['nom']) ?></h1>
        
        <?php if (!empty($produit['categorie_nom'])): ?>
            <div class="categorie">Catégorie : <?= htmlspecialchars($produit['categorie_nom']) ?></div>
        <?php endif; ?>

        <!-- PRIX -->
        <div class="prix-section">
            <?php if (!empty($produit['prix_promo'])): ?>
                <span class="prix-promo"><?= number_format($produit['prix_promo'], 0, ',', ' ') ?> DH</span>
                <span class="prix-barre"><?= number_format($produit['prix'], 0, ',', ' ') ?> DH</span>
                <span class="badge-promo">PROMO</span>
            <?php else: ?>
                <span class="prix-normal"><?= number_format($produit['prix'], 0, ',', ' ') ?> DH</span>
            <?php endif; ?>
        </div>

        <!-- DESCRIPTION -->
        <?php if (!empty($produit['description'])): ?>
            <div class="description">
                <?= nl2br(htmlspecialchars($produit['description'])) ?>
            </div>
        <?php endif; ?>

        <!-- TAILLES -->
        <?php if (!empty($tailles)): ?>
        <div class="tailles-section">
            <h3>Pointures disponibles</h3>
            <div class="tailles-grid">
                <?php foreach ($tailles as $taille): ?>
                    <button type="button" 
                            class="taille-btn" 
                            data-taille="<?= htmlspecialchars($taille['taille']) ?>"
                            onclick="selectTaille(this)">
                        <?= htmlspecialchars($taille['taille']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="stock-info">Sélectionnez votre pointure pour commander</div>
        </div>
        <?php endif; ?>

        <!-- FORMULAIRE AJOUT PANIER -->
        <form method="POST" action="<?= URL_ROOT ?>/panier/ajouter" id="formPanier">
            <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
            <input type="hidden" name="redirect" value="panier">
            
            <!-- Quantité -->
            <div class="quantite-section" style="margin-bottom:20px;">
                <h3>Quantité</h3>
                <div class="quantite-selector">
                    <button type="button" onclick="changeQuantite(-1)">−</button>
                    <input type="number" name="quantite" id="quantite" value="1" min="1" max="10" readonly>
                    <button type="button" onclick="changeQuantite(1)">+</button>
                </div>
            </div>
            
            <button type="submit" id="btnAjouter" class="btn-whatsapp" style="width:100%; border:none; cursor:pointer;">
                🛒 Ajouter au panier
            </button>
        </form>
    </div>
</div>

<!-- PRODUITS SIMILAIRES -->
<?php if (!empty($similaires)): ?>
<section class="similaires">
    <h2>Produits similaires</h2>
    <div class="similaires-grid">
        <?php foreach ($similaires as $sim): 
            $img = $sim['image'] 
                ? UPLOAD_URL . $sim['image'] 
                : 'https://via.placeholder.com/400x300/141414/c9a84c?text=Photo';
        ?>
            <a href="<?= URL_ROOT ?>/produit/<?= $sim['slug'] ?>" class="product-card">
                <div class="img-wrap">
                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($sim['nom']) ?>">
                </div>
                <div class="info">
                    <h3><?= htmlspecialchars($sim['nom']) ?></h3>
                    <div class="prix">
                        <?php if (!empty($sim['prix_promo'])): ?>
                            <?= number_format($sim['prix_promo'], 0, ',', ' ') ?> DH
                        <?php else: ?>
                            <?= number_format($sim['prix'], 0, ',', ' ') ?> DH
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox" onclick="closeLightboxOutside(event)">
    <button class="lb-close" onclick="closeLightbox()">✕</button>
    <div class="lb-nav">
        <button onclick="prevLightboxImage(event)">‹</button>
        <button onclick="nextLightboxImage(event)">›</button>
    </div>
    <img class="lb-image" id="lbImage" src="" alt="">
    <div class="lb-counter" id="lbCounter"></div>
    <?php if (count($images) > 1): ?>
    <div class="lb-thumbs" id="lbThumbs">
        <?php foreach ($images as $index => $img): ?>
            <img src="<?= UPLOAD_URL . $img['chemin'] ?>" 
                 class="<?= $index === 0 ? 'active' : '' ?>"
                 onclick="goToLightboxImage(<?= $index ?>, event)">
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- FOOTER -->
<footer>
    <p><?= htmlspecialchars(SITE_NAME) ?> — <a href="https://wa.me/<?= WHATSAPP ?>">WhatsApp</a></p>
    <p>&copy; <?= date('Y') ?> Tous droits réservés.</p>
</footer>

<script>
let tailleSelectionnee = '';
let lbImages = [];
let lbCurrentIndex = 0;

<?php if (!empty($images)): ?>
lbImages = [
    <?php foreach ($images as $img): ?>
        '<?= UPLOAD_URL . $img['chemin'] ?>',
    <?php endforeach; ?>
];
<?php endif; ?>

function changeImage(src, thumb) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

function selectTaille(btn) {
    document.querySelectorAll('.taille-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    tailleSelectionnee = btn.dataset.taille;
}

function changeQuantite(delta) {
    const input = document.getElementById('quantite');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > 10) val = 10;
    input.value = val;
}

// ── LIGHTBOX ──────────────────────────────────────────
function openLightbox(index) {
    if (lbImages.length === 0) return;
    lbCurrentIndex = index !== undefined ? index : 0;
    updateLightbox();
    document.getElementById('lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
    document.body.style.overflow = '';
}

function closeLightboxOutside(e) {
    if (e.target === e.currentTarget) closeLightbox();
}

function updateLightbox() {
    const img = document.getElementById('lbImage');
    img.src = lbImages[lbCurrentIndex];
    document.getElementById('lbCounter').textContent = (lbCurrentIndex + 1) + ' / ' + lbImages.length;
    
    // Miniatures actives
    const thumbs = document.querySelectorAll('#lbThumbs img');
    thumbs.forEach((t, i) => {
        t.classList.toggle('active', i === lbCurrentIndex);
        // Scroll dans la vue
        if (i === lbCurrentIndex) t.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    });
}

function nextLightboxImage(e) {
    if (e) e.stopPropagation();
    lbCurrentIndex = (lbCurrentIndex + 1) % lbImages.length;
    updateLightbox();
}

function prevLightboxImage(e) {
    if (e) e.stopPropagation();
    lbCurrentIndex = (lbCurrentIndex - 1 + lbImages.length) % lbImages.length;
    updateLightbox();
}

function goToLightboxImage(index, e) {
    if (e) e.stopPropagation();
    lbCurrentIndex = index;
    updateLightbox();
}

// Ouvrir la lightbox au clic sur l'image principale
document.querySelector('.main-image').addEventListener('click', function() {
    // Trouver l'index de l'image actuellement affichée
    const currentSrc = document.getElementById('mainImage').src;
    const idx = lbImages.findIndex(src => src === currentSrc);
    openLightbox(idx >= 0 ? idx : 0);
});

// Navigation clavier
document.addEventListener('keydown', function(e) {
    if (!document.getElementById('lightbox').classList.contains('open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') nextLightboxImage();
    if (e.key === 'ArrowLeft') prevLightboxImage();
});

// Validation avant soumission
document.getElementById('formPanier').addEventListener('submit', function(e) {
    <?php if (!empty($tailles)): ?>
    if (!tailleSelectionnee) {
        e.preventDefault();
        alert('Veuillez sélectionner une pointure avant d\'ajouter au panier.');
        return false;
    }
    const tailleInput = document.createElement('input');
    tailleInput.type = 'hidden';
    tailleInput.name = 'taille';
    tailleInput.value = tailleSelectionnee;
    this.appendChild(tailleInput);
    <?php else: ?>
    const tailleInput = document.createElement('input');
    tailleInput.type = 'hidden';
    tailleInput.name = 'taille';
    tailleInput.value = 'Unique';
    this.appendChild(tailleInput);
    <?php endif; ?>
});
</script>

</body>
</html>

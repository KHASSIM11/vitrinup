<?php
/**
 * @var string $title          Titre de la page
 * @var array  $articles       Articles du panier
 * @var int    $nombreArticles Nombre d'articles
 * @var float  $total          Total du panier
 * @var float  $economie       Économie réalisée
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

        /* ── FLASH ── */
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

        /* ── GRID ── */
        .commande-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 40px 60px;
        }

        /* ── FORMULAIRE ── */
        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 30px;
        }
        .form-card h2 {
            font-size: 1.2rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 6px;
            font-weight: 600;
        }
        .form-group label .required { color: var(--promo); }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            background: #0a0a0a;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-light);
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--accent);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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
        .recap-article {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
            font-size: 0.9rem;
        }
        .recap-article:last-child { border-bottom: none; }
        .recap-article .nom { flex: 1; }
        .recap-article .details { color: var(--text-muted); font-size: 0.8rem; }
        .recap-article .prix { color: var(--accent); font-weight: 600; }
        .recap-ligne {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: var(--text-muted);
        }
        .recap-ligne.total {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-light);
            padding-top: 15px;
            border-top: 1px solid var(--border);
            margin-top: 15px;
        }

        /* ── BOUTON ── */
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
            margin-top: 25px;
        }
        .btn-commander:hover { background: #1ebe5d; }
        .btn-retour {
            display: block;
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-muted);
            border-radius: 8px;
            margin-top: 10px;
            cursor: pointer;
            text-align: center;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .btn-retour:hover { border-color: var(--accent); color: var(--accent); }

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

        @media (max-width: 900px) {
            .commande-container { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
            header { padding: 15px 20px; }
            .page-title, .flash { padding-left: 20px; padding-right: 20px; }
            .commande-container { padding: 0 20px 40px; }
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
        <a href="<?= URL_ROOT ?>/panier">🛒 Panier (<?= $nombreArticles ?>)</a>
    </nav>
</header>

<!-- PAGE TITLE -->
<div class="page-title">
    <h1>📋 Finaliser votre commande</h1>
    <p>Remplissez vos coordonnées pour passer commande</p>
</div>

<!-- FLASH MESSAGES -->
<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="flash">
        <div class="flash-message flash-error">
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
        </div>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="commande-container">
    <!-- FORMULAIRE COORDONNÉES -->
    <div class="form-card">
        <h2>👤 Vos coordonnées</h2>
        <form method="POST" action="<?= URL_ROOT ?>/panier/commander">
            <div class="form-group">
                <label>Nom complet <span class="required">*</span></label>
                <input type="text" name="nom" required placeholder="Ex: Ahmed Benali">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Téléphone <span class="required">*</span></label>
                    <input type="tel" name="telephone" required placeholder="Ex: 0612345678">
                </div>
                <div class="form-group">
                    <label>Email (optionnel)</label>
                    <input type="email" name="email" placeholder="Ex: ahmed@email.com">
                </div>
            </div>

            <div class="form-group">
                <label>Adresse de livraison</label>
                <input type="text" name="adresse" placeholder="Ex: 12 Rue Mohammed V">
            </div>

            <div class="form-group">
                <label>Ville</label>
                <input type="text" name="ville" placeholder="Ex: Casablanca">
            </div>

            <div class="form-group">
                <label>Notes (optionnel)</label>
                <textarea name="notes" placeholder="Ajoutez une note pour votre commande..."></textarea>
            </div>

            <button type="submit" class="btn-commander">
                📲 Confirmer et envoyer sur WhatsApp
            </button>
        </form>

        <a href="<?= URL_ROOT ?>/panier" class="btn-retour">← Retour au panier</a>
    </div>

    <!-- RÉCAPITULATIF -->
    <div class="recap-card">
        <h2>🛒 Récapitulatif</h2>

        <?php foreach ($articles as $article): ?>
            <div class="recap-article">
                <div>
                    <div class="nom"><?= htmlspecialchars($article['nom']) ?></div>
                    <div class="details">
                        Taille : <?= htmlspecialchars($article['taille']) ?> × <?= $article['quantite'] ?>
                    </div>
                </div>
                <div class="prix">
                    <?= number_format($article['prix'] * $article['quantite'], 0, ',', ' ') ?> DH
                </div>
            </div>
        <?php endforeach; ?>

        <?php if ($economie > 0): ?>
            <div class="recap-ligne" style="color:var(--promo);">
                <span>Économie</span>
                <span>− <?= number_format($economie, 0, ',', ' ') ?> DH</span>
            </div>
        <?php endif; ?>

        <div class="recap-ligne total">
            <span>Total</span>
            <span><?= number_format($total, 0, ',', ' ') ?> DH</span>
        </div>

        <div class="info-livraison">
            🚚 Livraison gratuite à domicile<br>
            💳 Paiement à la livraison
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <p><?= htmlspecialchars(SITE_NAME) ?> — <a href="https://wa.me/<?= WHATSAPP ?>">WhatsApp</a></p>
    <p>&copy; <?= date('Y') ?> Tous droits réservés.</p>
</footer>

</body>
</html>

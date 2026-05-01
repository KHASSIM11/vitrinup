<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitrinup - Votre Vitrine de Chaussures en Ligne</title>
    <style>
        /* Couleurs */
        :root {
            --primary-color: #FF6600; /* Orange */
            --secondary-color: #1a1a1a; /* Noir */
            --text-color: #333;
            --light-text-color: #fff;
            --background-color: #f4f4f4;
        }

        /* Styles Généraux */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: var(--text-color);
            background-color: var(--background-color);
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
        }

        h1, h2, h3 {
            color: var(--secondary-color);
        }

        a {
            text-decoration: none;
            color: var(--primary-color);
        }

        .btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: var(--light-text-color);
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #e65c00; /* Orange plus foncé au survol */
        }

        .btn-secondary {
            background-color: var(--secondary-color);
        }

        .btn-secondary:hover {
            background-color: #333;
        }

        /* Header */
        header {
            background-color: var(--secondary-color);
            color: var(--light-text-color);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        header h1 a {
            color: var(--light-text-color);
        }

        header nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        header nav ul li {
            margin-left: 20px;
        }

        header nav ul li a {
            color: var(--light-text-color);
            font-weight: bold;
            transition: color 0.3s ease;
        }

        header nav ul li a:hover {
            color: var(--primary-color);
        }

        /* Hero Section */
        .hero {
            background-color: var(--secondary-color);
            color: var(--light-text-color);
            text-align: center;
            padding: 80px 0;
            position: relative;
            overflow: hidden; /* Pour contenir les éléments absolus */
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://via.placeholder.com/1500x800/1a1a1a/ffffff?text=Image+Chaussures+Hero') no-repeat center center/cover;
            opacity: 0.3;
            z-index: 1;
        }

        .hero .container {
            position: relative; /* Pour que le contenu soit au-dessus du pseudo-élément */
            z-index: 2;
        }

        .hero h2 {
            font-size: 48px;
            margin-bottom: 20px;
            line-height: 1.2;
            color: var(--light-text-color);
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        /* Features Section */
        .features {
            padding: 60px 0;
            text-align: center;
        }

        .features h2 {
            font-size: 36px;
            margin-bottom: 40px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .feature-item {
            background-color: var(--light-text-color);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .feature-item i {
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .feature-item h3 {
            font-size: 22px;
            margin-bottom: 15px;
        }

        .feature-item p {
            font-size: 16px;
            color: #555;
        }

        /* Pricing Section */
        .pricing {
            background-color: var(--secondary-color);
            color: var(--light-text-color);
            padding: 60px 0;
            text-align: center;
        }

        .pricing h2 {
            font-size: 36px;
            margin-bottom: 40px;
            color: var(--light-text-color);
        }

        .pricing-card {
            background-color: #222; /* Noir légèrement plus clair pour la carte */
            padding: 40px;
            border-radius: 8px;
            display: inline-block;
            max-width: 350px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .pricing-card .price {
            font-size: 50px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .pricing-card .period {
            font-size: 18px;
            color: #ccc;
            margin-bottom: 30px;
        }

        .pricing-card ul {
            list-style: none;
            padding: 0;
            margin-bottom: 40px;
            text-align: left;
        }

        .pricing-card ul li {
            margin-bottom: 15px;
            font-size: 16px;
            color: #eee;
            display: flex;
            align-items: center;
        }

        .pricing-card ul li i {
            color: var(--primary-color);
            margin-right: 10px;
        }

        /* Footer */
        footer {
            background-color: var(--secondary-color);
            color: var(--light-text-color);
            text-align: center;
            padding: 30px 0;
            margin-top: 40px;
        }

        footer p {
            margin: 0;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header .container {
                flex-direction: column;
            }

            header nav ul {
                margin-top: 15px;
                flex-direction: column;
                align-items: center;
            }

            header nav ul li {
                margin: 5px 0;
            }

            .hero h2 {
                font-size: 36px;
            }

            .hero p {
                font-size: 16px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .pricing-card {
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="<?= URL_ROOT ?>"><?= SITE_NAME ?></a></h1>
            <a href="<?= URL_ROOT ?>/inscription" class="btn btn-secondary">Créer ma boutique</a>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <h2>Créez votre boutique de chaussures en ligne en 5 minutes</h2>
                <p>La solution simple et rapide pour vendre vos chaussures au Maroc via WhatsApp.</p>
                <a href="<?= URL_ROOT ?>/inscription" class="btn">Commencer gratuitement</a>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <h2>Pourquoi choisir Vitrinup ?</h2>
                <div class="features-grid">
                    <div class="feature-item">
                        <i class="fas fa-store"></i> <!-- Icône Font Awesome pour vitrine -->
                        <h3>Vitrine en ligne professionnelle</h3>
                        <p>Présentez vos produits de manière attrayante avec une page dédiée à votre marque.</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-box"></i> <!-- Icône Font Awesome pour catalogue -->
                        <h3>Catalogue produits facile à gérer</h3>
                        <p>Ajoutez, modifiez et organisez vos chaussures avec photos, descriptions et prix.</p>
                    </div>
                    <div class="feature-item">
                        <i class="fab fa-whatsapp"></i> <!-- Icône Font Awesome pour WhatsApp -->
                        <h3>Commandes via WhatsApp</h3>
                        <p>Simplifiez la prise de commande et la communication avec vos clients via WhatsApp.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="pricing">
            <div class="container">
                <h2>Notre Offre Simple et Abordable</h2>
                <div class="pricing-card">
                    <div class="price">99 DH</div>
                    <div class="period">par mois</div>
                    <ul>
                        <li><i class="fas fa-check"></i> Vitrine personnalisable</li>
                        <li><i class="fas fa-check"></i> Catalogue illimité</li>
                        <li><i class="fas fa-check"></i> Intégration WhatsApp</li>
                        <li><i class="fas fa-check"></i> Paiement à la livraison</li>
                        <li><i class="fas fa-check"></i> Support client</li>
                    </ul>
                    <a href="<?= URL_ROOT ?>/inscription" class="btn">Choisir cette offre</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Vitrinup. Tous droits réservés. Fait avec ❤️ au Maroc.</p>
        </div>
    </footer>

    <!-- Inclusion de Font Awesome pour les icônes -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>

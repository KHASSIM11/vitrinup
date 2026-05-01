<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Vitrinup - Votre Vitrine en Ligne' ?></title>
    <!-- Lien vers votre fichier CSS principal -->
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/css/style.css">
    <!-- Lien vers une police Google Font (optionnel, pour un look plus moderne) -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="<?= URL_ROOT ?>"><?= SITE_NAME ?></a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="<?= URL_ROOT ?>/boutiques">Nos Boutiques</a></li>
                    <li><a href="<?= URL_ROOT ?>/a-propos">À Propos</a></li>
                    <li><a href="<?= URL_ROOT ?>/contact">Contact</a></li>
                    <li><a href="<?= URL_ROOT ?>/connexion" class="nav-login-btn">Connexion</a></li>
                </ul>
            </nav>
            <div class="menu-toggle">☰</div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Créez votre boutique de chaussures en ligne en 5 minutes</h1>
            <p>Vitrinup vous offre la solution simple et rapide pour vendre vos produits au Maroc via WhatsApp.</p>
            <a href="<?= URL_ROOT ?>/inscription" class="cta-button hero-cta">Commencer gratuitement</a>
        </div>
        <div class="hero-image">
            <!-- Vous pouvez ajouter une image ici, par exemple une illustration de chaussures ou d'une boutique en ligne -->
            <img src="<?= URL_ROOT ?>/assets/img/hero-shoes.png" alt="Boutique de chaussures en ligne">
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2>Pourquoi choisir Vitrinup ?</h2>
            <div class="features-grid">
                <div class="feature-item">
                    <img src="<?= URL_ROOT ?>/assets/img/icon-easy.png" alt="Facile">
                    <h3>Simple et Rapide</h3>
                    <p>Configurez votre vitrine en quelques clics, sans compétences techniques.</p>
                </div>
                <div class="feature-item">
                    <img src="<?= URL_ROOT ?>/assets/img/icon-whatsapp.png" alt="WhatsApp">
                    <h3>Commandes WhatsApp</h3>
                    <p>Intégration directe pour des commandes fluides via WhatsApp.</p>
                </div>
                <div class="feature-item">
                    <img src="<?= URL_ROOT ?>/assets/img/icon-catalog.png" alt="Catalogue">
                    <h3>Catalogue Produits</h3>
                    <p>Présentez vos chaussures avec photos, descriptions et prix.</p>
                </div>
                <div class="feature-item">
                    <img src="<?= URL_ROOT ?>/assets/img/icon-payment.png" alt="Paiement">
                    <h3>Paiement à la Livraison</h3>
                    <p>Option de paiement sécurisée et habituelle au Maroc.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="pricing">
        <div class="container">
            <h2>Notre Offre</h2>
            <div class="pricing-card">
                <h3>Formule Essentielle</h3>
                <div class="price">
                    <span class="amount">99</span>
                    <span class="currency">DH</span>
                    <span class="period">/ mois</span>
                </div>
                <ul>
                    <li><span class="check">✓</span> Vitrine personnalisable</li>
                    <li><span class="check">✓</span> Catalogue produits illimité</li>
                    <li><span class="check">✓</span> Intégration WhatsApp</li>
                    <li><span class="check">✓</span> Paiement à la livraison</li>
                    <li><span class="check">✓</span> Support client</li>
                </ul>
                <a href="<?= URL_ROOT ?>/inscription" class="cta-button pricing-cta">Choisir cette offre</a>
            </div>
        </div>
    </section>

    <section class="cta-final">
        <div class="container">
            <h2>Prêt à vendre plus ?</h2>
            <p>Rejoignez les centaines de boutiques de chaussures qui font confiance à Vitrinup.</p>
            <a href="<?= URL_ROOT ?>/inscription" class="cta-button">Créer ma boutique maintenant</a>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <a href="<?= URL_ROOT ?>"><?= SITE_NAME ?></a>
                </div>
                <div class="footer-links">
                    <h4>Liens utiles</h4>
                    <ul>
                        <li><a href="<?= URL_ROOT ?>/mentions-legales">Mentions Légales</a></li>
                        <li><a href="<?= URL_ROOT ?>/politique-confidentialite">Politique de Confidentialité</a></li>
                        <li><a href="<?= URL_ROOT ?>/faq">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Contactez-nous</h4>
                    <p>Email: <a href="mailto:support@vitrinup.com">support@vitrinup.com</a></p>
                    <p>Téléphone: +212 6 XX XX XX XX</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Lien vers votre fichier JavaScript principal -->
    <script src="<?= URL_ROOT ?>/assets/js/main.js"></script>
    <script>
        // Script pour le menu mobile (à placer avant la fermeture de body)
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const mainNav = document.querySelector('.main-nav');

            if (menuToggle && mainNav) {
                menuToggle.addEventListener('click', function() {
                    mainNav.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>

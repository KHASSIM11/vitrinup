<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Vitrinup' ?></title>
    <!-- Lien vers votre fichier CSS principal -->
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="<?= URL_ROOT ?>"><?= SITE_NAME ?></a></h1>
            <nav>
                <ul>
                    <li><a href="<?= URL_ROOT ?>/boutiques">Nos Boutiques</a></li>
                    <li><a href="<?= URL_ROOT ?>/a-propos">À Propos</a></li>
                    <li><a href="<?= URL_ROOT ?>/contact">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <h2><?= $data['title'] ?></h2>
            <p><?= $data['description'] ?></p>
            <p>Préparez-vous à lancer votre vitrine en ligne et à vendre vos chaussures via WhatsApp !</p>
            <!-- Bouton d'appel à l'action -->
            <a href="<?= URL_ROOT ?>/inscription" class="cta-button">Créer ma vitrine</a>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Lien vers votre fichier JavaScript principal -->
    <script src="<?= URL_ROOT ?>/assets/js/main.js"></script>
</body>
</html>

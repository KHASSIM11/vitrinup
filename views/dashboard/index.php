<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – <?= SITE_NAME ?></title>
    <style>
        :root {
            --primary-color: #FF6600;
            --secondary-color: #1a1a1a;
            --light-text-color: #fff;
            --background-color: #f4f4f4;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--secondary-color);
            margin: 0;
            padding: 20px;
        }
        .header {
            background-color: var(--secondary-color);
            color: var(--light-text-color);
            padding: 15px;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 0;
            color: var(--primary-color);
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        .stat {
            flex: 1;
            background: var(--primary-color);
            color: var(--light-text-color);
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        a.logout {
            color: var(--light-text-color);
            text-decoration: none;
            margin-left: 15px;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Dashboard – <?= htmlspecialchars($boutique['nom'] ?? 'Boutique') ?></h1>
        <a href="<?= URL_ROOT ?>/logout" class="logout">Déconnexion</a>
    </header>

    <div class="container">
        <h2>Statistiques de votre boutique</h2>
        <p><strong>Email :</strong> <?= htmlspecialchars($boutique['email'] ?? '') ?></p>
        <p><strong>Ville :</strong> <?= htmlspecialchars($boutique['ville'] ?? '') ?></p>

        <div class="stats">
            <div class="stat">
                <strong>Produits</strong><br>
                <?= $productCount ?>
            </div>
            <div class="stat">
                <strong>Commandes</strong><br>
                <?= $orderCount ?>
            </div>
        </div>
    </div>
</body>
</html>

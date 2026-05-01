<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription – <?= SITE_NAME ?></title>
    <style>
        :root {
            --primary-color: #FF6600;   /* Orange */
            --secondary-color: #1a1a1a; /* Noir */
            --text-color: #333;
            --light-text-color: #fff;
            --background-color: #f4f4f4;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .container {
            max-width: 500px;
            margin: 60px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: var(--secondary-color);
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        .btn {
            display: block;
            width: 100%;
            margin-top: 25px;
            padding: 12px;
            background-color: var(--primary-color);
            color: var(--light-text-color);
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #e65c00;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Créer votre boutique</h1>
        <form method="POST" action="<?= URL_ROOT ?>/inscription">
            <label for="nom_boutique">Nom de la boutique</label>
            <input type="text" id="nom_boutique" name="nom_boutique" required>

            <label for="whatsapp">WhatsApp (facultatif)</label>
            <input type="text" id="whatsapp" name="whatsapp">

            <label for="email">Email de contact</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

            <label for="ville">Ville</label>
            <input type="text" id="ville" name="ville" required>

            <!-- Le slug est généré côté client pour éviter de le saisir -->
            <input type="hidden" id="slug" name="slug">

            <button type="submit" class="btn">Créer ma boutique</button>
        </form>
        <div class="footer">
            <a href="<?= URL_ROOT ?>">← Retour à l'accueil</a>
        </div>
    </div>

    <script>
        // Génère automatiquement le slug à partir du nom de la boutique
        const nomInput = document.getElementById('nom_boutique');
        const slugInput = document.getElementById('slug');

        function slugify(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Remplace les espaces par des tirets
                .replace(/[^\w\-]+/g, '')       // Supprime les caractères non alphanumériques
                .replace(/\-\-+/g, '-')         // Remplace plusieurs tirets par un seul
                .replace(/^-+/, '')             // Supprime les tirets de début
                .replace(/-+$/, '');            // Supprime les tirets de fin
        }

        nomInput.addEventListener('input', () => {
            slugInput.value = slugify(nomInput.value);
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin — Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="logo"><?= htmlspecialchars(SITE_NAME) ?></div>
        <div class="subtitle">Panel d'administration</div>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= URL_ROOT ?>/admin/login">
            <label>Email</label>
            <input type="email" name="email" required autofocus>

            <label>Mot de passe</label>
            <input type="password" name="password" required>

            <button type="submit" class="btn-login">SE CONNECTER</button>
        </form>

        <div class="back">
            <a href="<?= URL_ROOT ?>">← Retour au site</a>
        </div>
    </div>
</body>
</html>

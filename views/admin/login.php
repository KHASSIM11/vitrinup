<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin — Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #0a0a0a;
            color: #f5f0eb;
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #141414;
            border: 1px solid #222;
            border-radius: 12px;
            padding: 50px 40px;
            width: 100%;
            max-width: 420px;
        }
        .logo {
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: #c9a84c;
            letter-spacing: 3px;
            margin-bottom: 8px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 35px;
        }
        .error {
            background: #2a0a0a;
            border: 1px solid #c00;
            color: #ff6b6b;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        label {
            display: block;
            color: #888;
            font-size: 0.85rem;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            background: #0a0a0a;
            border: 1px solid #333;
            border-radius: 6px;
            color: #f5f0eb;
            font-size: 1rem;
            margin-bottom: 20px;
            outline: none;
            transition: border-color 0.2s;
        }
        input:focus { border-color: #c9a84c; }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #c9a84c;
            color: #0a0a0a;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 1px;
            transition: background 0.2s;
        }
        .btn-login:hover { background: #e0bb6a; }
        .back {
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
        }
        .back a { color: #666; text-decoration: none; }
        .back a:hover { color: #c9a84c; }
    </style>
</head>
<body>
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

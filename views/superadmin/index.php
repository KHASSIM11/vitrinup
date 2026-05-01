<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin – Dashboard</title>
    <style>
        :root {
            --primary-color: #FF6600;   /* Orange */
            --secondary-color: #1a1a1a; /* Noir */
            --light-text-color: #fff;
            --background-color: #f4f4f4;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--secondary-color);
            margin: 0;
        }

        .header {
            background-color: var(--secondary-color);
            color: var(--light-text-color);
            padding: 15px 0;
            text-align: center;
        }

        .header a {
            color: var(--light-text-color);
            margin-left: 20px;
            text-decoration: none;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 30px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: var(--primary-color);
            color: var(--light-text-color);
        }

        tr:hover {
            background-color: #fafafa;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            color: var(--light-text-color);
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-activate {
            background-color: #28a745; /* Vert */
        }

        .btn-deactivate {
            background-color: #dc3545; /* Rouge */
        }

        .btn-logout {
            background-color: var(--primary-color);
        }

        @media (max-width: 768px) {
            th, td {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Super Admin – <?= SITE_NAME ?></h1>
        <a href="<?= URL_ROOT ?>/superadmin/logout" class="btn btn-logout">Déconnexion</a>
    </header>

    <div class="container">
        <h2>Liste des boutiques</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>WhatsApp</th>
                    <th>Ville</th>
                    <th>Statut</th>
                    <th>Créée le</th>
                    <th>Produits</th>
                    <th>Commandes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // $boutiques, $productMap et $orderMap sont extraits par la méthode view()
                foreach ($boutiques as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['id']) ?></td>
                        <td><?= htmlspecialchars($b['nom']) ?></td>
                        <td><?= htmlspecialchars($b['email']) ?></td>
                        <td><?= htmlspecialchars($b['whatsapp']) ?></td>
                        <td><?= htmlspecialchars($b['ville']) ?></td>
                        <td><?= htmlspecialchars($b['statut']) ?></td>
                        <td><?= htmlspecialchars($b['created_at']) ?></td>
                        <td><?= $productMap[$b['id']] ?? 0 ?></td>
                        <td><?= $orderMap[$b['id']] ?? 0 ?></td>
                        <td>
                            <?php if ($b['statut'] === 'active'): ?>
                                <a href="<?= URL_ROOT ?>/superadmin/toggle?id=<?= $b['id'] ?>" class="btn btn-deactivate">Désactiver</a>
                            <?php else: ?>
                                <a href="<?= URL_ROOT ?>/superadmin/toggle?id=<?= $b['id'] ?>" class="btn btn-activate">Activer</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($boutiques)): ?>
                    <tr>
                        <td colspan="10" style="text-align:center;">Aucune boutique trouvée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

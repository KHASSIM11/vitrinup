<?php

class HomeController extends Controller {

    /**
     * Affiche la page d'accueil avec les 8 derniers produits actifs.
     */
    public function index(): void {
        // Récupération des 8 derniers produits actifs avec leur image principale
        $db = new Database();

        $products = $db->query(
            "SELECT p.id, p.nom, p.prix, p.prix_promo, i.url AS image
             FROM produits p
             JOIN images_produits i ON p.id = i.produit_id
             WHERE p.actif = 1 AND i.est_principale = 1
             ORDER BY p.created_at DESC
             LIMIT 8"
        )->resultSet();

        // Passage des données à la vue
        $data = [
            'title'    => 'Bienvenue sur ' . SITE_NAME,
            'products' => $products
        ];

        $this->view('home/index', $data);
    }

    /**
     * Affiche une page 404 simple.
     */
    public function notFound(): void {
        http_response_code(404);
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>404 - Page non trouvée</title>
    <style>
        body { background:#0a0a0a; color:#f5f0eb; font-family:Arial,Helvetica,sans-serif; text-align:center; padding:100px; }
        a { color:#c9a84c; text-decoration:none; }
        a:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <h1>404 - Page non trouvée</h1>
    <p>La page que vous recherchez n\'existe pas.</p>
    <p><a href="' . URL_ROOT . '">Retour à l\'accueil</a></p>
</body>
</html>';
    }
}
?>

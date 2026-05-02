<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class HomeController extends Controller {

    /**
     * Affiche la page d'accueil avec les 8 derniers produits actifs.
     */
    public function index(): void {
        $db = new Database();

        // Récupération des 8 derniers produits actifs avec leur image principale
        $products = $db->query(
            "SELECT p.id, p.nom, p.slug, p.prix, p.prix_promo, p.genre, p.marque,
                    i.chemin AS image
             FROM produits p
             LEFT JOIN images_produits i ON p.id = i.produit_id AND i.est_principale = 1
             WHERE p.statut = 'actif'
             ORDER BY p.created_at DESC
             LIMIT 8"
        )->resultSet();

        // Récupération des catégories pour la bannière (non utilisée dans la vue actuelle)
        $categories = $db->query(
            "SELECT * FROM categories ORDER BY ordre ASC"
        )->resultSet();

        $data = [
            'title'      => SITE_NAME,
            'products'   => $products,   // <-- clé corrigée pour correspondre à la vue
            'categories' => $categories,
        ];

        $this->view('home/index', $data);
    }

    /**
     * Page 404
     */
    public function notFound(): void {
        http_response_code(404);
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>404 - Page non trouvée</title>
    <style>
        body { background:#0a0a0a; color:#f5f0eb; font-family:Arial,sans-serif; text-align:center; padding:100px; }
        a { color:#c9a84c; }
    </style>
</head>
<body>
    <h1>404 — Page non trouvée</h1>
    <p><a href="' . URL_ROOT . '">Retour à l\'accueil</a></p>
</body>
</html>';
    }
}
?>

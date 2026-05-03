<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class ProduitController extends Controller {

    /**
     * Affiche la fiche détaillée d'un produit.
     * @param string $slug Le slug du produit
     */
    public function show(string $slug): void {
        $db = new Database();

        // Récupération du produit avec sa catégorie
        $produit = $db->query(
            "SELECT p.*, c.nom AS categorie_nom
             FROM produits p
             LEFT JOIN categories c ON p.categorie_id = c.id
             WHERE p.slug = :slug AND p.statut = 'actif'"
        )->bind(':slug', $slug)->single();

        // Si le produit n'existe pas ou est inactif → 404
        if (!$produit) {
            $this->notFound();
            return;
        }

        // Récupération de toutes les images du produit
        $images = $db->query(
            "SELECT * FROM images_produits 
             WHERE produit_id = :produit_id 
             ORDER BY est_principale DESC, ordre ASC"
        )->bind(':produit_id', $produit['id'])->resultSet();

        // Récupération des tailles disponibles
        $tailles = $db->query(
            "SELECT * FROM tailles_produits 
             WHERE produit_id = :produit_id AND stock > 0
             ORDER BY 
                CASE 
                    WHEN taille REGEXP '^[0-9]+$' THEN CAST(taille AS UNSIGNED)
                    ELSE 0 
                END,
                taille ASC"
        )->bind(':produit_id', $produit['id'])->resultSet();

        // Produits similaires (même catégorie ou même genre, excluant le produit actuel)
        $similaires = $db->query(
            "SELECT p.id, p.nom, p.slug, p.prix, p.prix_promo, p.genre,
                    (SELECT chemin FROM images_produits 
                     WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image
             FROM produits p
             WHERE p.id != :produit_id 
               AND p.statut = 'actif'
               AND (p.categorie_id = :categorie_id OR p.genre = :genre)
             ORDER BY RAND()
             LIMIT 4"
        )
        ->bind(':produit_id', $produit['id'])
        ->bind(':categorie_id', $produit['categorie_id'])
        ->bind(':genre', $produit['genre'])
        ->resultSet();

        $data = [
            'title'      => $produit['nom'] . ' — ' . SITE_NAME,
            'produit'    => $produit,
            'images'     => $images,
            'tailles'    => $tailles,
            'similaires' => $similaires,
        ];

        $this->view('produit/show', $data);
    }

    /**
     * Page 404 pour produit introuvable.
     */
    private function notFound(): void {
        http_response_code(404);
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produit non trouvé — ' . SITE_NAME . '</title>
    <style>
        body { background:#0a0a0a; color:#f5f0eb; font-family:Arial,sans-serif; text-align:center; padding:100px; }
        a { color:#c9a84c; }
    </style>
</head>
<body>
    <h1>Produit introuvable</h1>
    <p>Le produit que vous recherchez n\'existe pas ou n\'est plus disponible.</p>
    <p><a href="' . URL_ROOT . '/catalogue">Voir le catalogue</a></p>
</body>
</html>';
    }
}

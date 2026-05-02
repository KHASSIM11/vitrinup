<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class CatalogueController extends Controller {

    public function index(): void {
        $db = new Database();

        // Filtres GET
        $filtreGenre     = $_GET['genre']        ?? '';
        $filtreCategorie = $_GET['categorie_id'] ?? '';

        // Construction de la requête avec filtres
        $sql = "SELECT p.id, p.nom, p.slug, p.prix, p.prix_promo, p.genre, p.marque,
                       c.nom AS categorie_nom,
                       i.chemin AS image
                FROM produits p
                LEFT JOIN categories c ON p.categorie_id = c.id
                LEFT JOIN images_produits i ON p.id = i.produit_id AND i.est_principale = 1
                WHERE p.statut = 'actif'";

        $params = [];

        if (!empty($filtreGenre)) {
            $sql .= " AND p.genre = :genre";
            $params[':genre'] = $filtreGenre;
        }

        if (!empty($filtreCategorie)) {
            $sql .= " AND p.categorie_id = :categorie_id";
            $params[':categorie_id'] = $filtreCategorie;
        }

        $sql .= " ORDER BY p.created_at DESC";

        $query = $db->query($sql);
        foreach ($params as $key => $val) {
            $query->bind($key, $val);
        }
        $produits = $query->resultSet();

        // Toutes les catégories pour le filtre
        $categories = $db->query(
            "SELECT * FROM categories ORDER BY ordre ASC"
        )->resultSet();

        $data = [
            'title'           => 'Catalogue — ' . SITE_NAME,
            'produits'        => $produits,
            'categories'      => $categories,
            'filtreGenre'     => $filtreGenre,
            'filtreCategorie' => $filtreCategorie,
        ];

        $this->view('catalogue/index', $data);
    }
}
?>

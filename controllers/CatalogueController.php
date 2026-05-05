<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class CatalogueController extends Controller {

    public function index(): void {
        $db = new Database();

        // Filtres GET
        $filtreGenre     = $_GET['genre']        ?? '';
        $filtreCategorie = $_GET['categorie_id'] ?? '';
        $filtreMarque    = $_GET['marque']        ?? '';
        $prixMin         = $_GET['prix_min']      ?? '';
        $prixMax         = $_GET['prix_max']      ?? '';
        $tri             = $_GET['tri']           ?? 'nouveautes';

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

        if (!empty($filtreMarque)) {
            $sql .= " AND p.marque = :marque";
            $params[':marque'] = $filtreMarque;
        }

        if (!empty($prixMin)) {
            $sql .= " AND (p.prix_promo IS NOT NULL AND p.prix_promo >= :prix_min OR p.prix_promo IS NULL AND p.prix >= :prix_min2)";
            $params[':prix_min'] = floatval($prixMin);
            $params[':prix_min2'] = floatval($prixMin);
        }

        if (!empty($prixMax)) {
            $sql .= " AND (p.prix_promo IS NOT NULL AND p.prix_promo <= :prix_max OR p.prix_promo IS NULL AND p.prix <= :prix_max2)";
            $params[':prix_max'] = floatval($prixMax);
            $params[':prix_max2'] = floatval($prixMax);
        }

        // Tri
        switch ($tri) {
            case 'prix_croissant':
                $sql .= " ORDER BY COALESCE(p.prix_promo, p.prix) ASC";
                break;
            case 'prix_decroissant':
                $sql .= " ORDER BY COALESCE(p.prix_promo, p.prix) DESC";
                break;
            case 'nom':
                $sql .= " ORDER BY p.nom ASC";
                break;
            default: // nouveautes
                $sql .= " ORDER BY p.created_at DESC";
                break;
        }

        $query = $db->query($sql);
        foreach ($params as $key => $val) {
            $query->bind($key, $val);
        }
        $produits = $query->resultSet();

        // Toutes les catégories pour le filtre
        $categories = $db->query(
            "SELECT * FROM categories ORDER BY ordre ASC"
        )->resultSet();

        // Toutes les marques distinctes pour le filtre
        $marques = $db->query(
            "SELECT DISTINCT marque FROM produits WHERE statut = 'actif' AND marque IS NOT NULL AND marque != '' ORDER BY marque ASC"
        )->resultSet();

        $data = [
            'title'           => 'Catalogue — ' . SITE_NAME,
            'produits'        => $produits,
            'categories'      => $categories,
            'marques'         => $marques,
            'filtreGenre'     => $filtreGenre,
            'filtreCategorie' => $filtreCategorie,
            'filtreMarque'    => $filtreMarque,
            'prixMin'         => $prixMin,
            'prixMax'         => $prixMax,
            'tri'             => $tri,
        ];

        $this->view('catalogue/index', $data);
    }
}
?>

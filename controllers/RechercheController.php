<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class RechercheController extends Controller {

    /**
     * Affiche les résultats de recherche
     */
    public function index(): void {
        $db = new Database();
        
        // Récupération du terme de recherche
        $q = trim($_GET['q'] ?? '');
        $q = htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
        
        $resultats = [];
        $nombreResultats = 0;
        
        if (!empty($q)) {
            // Recherche dans les produits (nom, description, marque)
            $searchTerm = '%' . $q . '%';
            $exactTerm = $q;
            $startTerm = $q . '%';
            
            $sql = "SELECT p.id, p.nom, p.slug, p.prix, p.prix_promo, p.genre, p.marque, p.description,
                           c.nom AS categorie_nom,
                           (SELECT chemin FROM images_produits WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image
                    FROM produits p
                    LEFT JOIN categories c ON p.categorie_id = c.id
                    WHERE p.statut = 'actif'
                      AND (
                          p.nom LIKE :q1 
                          OR p.marque LIKE :q2 
                          OR p.description LIKE :q3
                          OR c.nom LIKE :q4
                      )
                    ORDER BY 
                        CASE 
                            WHEN p.nom LIKE :qExact THEN 1
                            WHEN p.nom LIKE :qStart THEN 2
                            ELSE 3
                        END,
                        p.nom ASC";
            
            $query = $db->query($sql);
            $query->bind(':q1', $searchTerm);
            $query->bind(':q2', $searchTerm);
            $query->bind(':q3', $searchTerm);
            $query->bind(':q4', $searchTerm);
            $query->bind(':qExact', $exactTerm);
            $query->bind(':qStart', $startTerm);
            $resultats = $query->resultSet();
            $nombreResultats = count($resultats);
        }
        
        // Récupérer les suggestions populaires (si pas de résultats)
        $suggestions = [];
        if (empty($resultats) && !empty($q)) {
            $suggestions = $db->query(
                "SELECT p.id, p.nom, p.slug, p.prix, p.prix_promo,
                        (SELECT chemin FROM images_produits WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image
                 FROM produits p
                 WHERE p.statut = 'actif'
                 ORDER BY p.created_at DESC
                 LIMIT 4"
            )->resultSet();
        }
        
        // Récupérer le nombre d'articles dans le panier
        $panierCount = 0;
        if (isset($_SESSION['panier'])) {
            foreach ($_SESSION['panier'] as $item) {
                $panierCount += $item['quantite'];
            }
        }
        
        $data = [
            'title' => (!empty($q) ? 'Résultats pour "' . $q . '"' : 'Recherche') . ' — ' . SITE_NAME,
            'q' => $q,
            'resultats' => $resultats,
            'nombreResultats' => $nombreResultats,
            'suggestions' => $suggestions,
            'panierCount' => $panierCount
        ];
        
        $this->view('recherche/index', $data);
    }
    
    /**
     * Autocomplétion AJAX (pour plus tard)
     */
    public function suggest(): void {
        header('Content-Type: application/json');
        
        $q = trim($_GET['q'] ?? '');
        if (empty($q) || strlen($q) < 2) {
            echo json_encode([]);
            exit;
        }
        
        $db = new Database();
        
        $suggestions = $db->query(
            "SELECT nom, slug FROM produits 
             WHERE statut = 'actif' 
               AND (nom LIKE :q OR marque LIKE :q)
             ORDER BY nom ASC
             LIMIT 5"
        )->bind(':q', '%' . $q . '%')->resultSet();
        
        echo json_encode($suggestions);
        exit;
    }
}

<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Panier.php';

class PanierController extends Controller {
    
    private $panier;
    private $db;
    
    public function __construct() {
        $this->panier = new Panier();
        $this->db = new Database();
    }
    
    /**
     * Affiche la page du panier
     */
    public function index(): void {
        $articles = $this->panier->getArticles();
        
        // Enrichir les articles avec les infos produits actuelles (stock, disponibilité)
        foreach ($articles as $cle => &$article) {
            $produit = $this->db->query(
                "SELECT statut FROM produits WHERE id = :id"
            )->bind(':id', $article['produit_id'])->single();
            
            $article['disponible'] = ($produit && $produit['statut'] === 'actif');
        }
        
        $data = [
            'title' => 'Mon Panier — ' . SITE_NAME,
            'articles' => $articles,
            'nombreArticles' => $this->panier->getNombreArticles(),
            'total' => $this->panier->getTotal(),
            'economie' => $this->panier->getEconomie(),
            'panierVide' => $this->panier->estVide()
        ];
        
        $this->view('panier/index', $data);
    }
    
    /**
     * Ajoute un produit au panier (AJAX ou POST)
     */
    public function ajouter(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_ROOT . '/panier');
            exit;
        }
        
        $produitId = intval($_POST['produit_id'] ?? 0);
        $taille = trim($_POST['taille'] ?? '');
        $quantite = intval($_POST['quantite'] ?? 1);
        
        if ($produitId <= 0 || empty($taille) || $quantite < 1) {
            $_SESSION['flash_error'] = 'Veuillez sélectionner une taille et une quantité valides.';
            header('Location: ' . URL_ROOT . '/catalogue');
            exit;
        }
        
        // Récupérer les infos du produit
        $produit = $this->db->query(
            "SELECT id, nom, prix, prix_promo, slug,
                (SELECT chemin FROM images_produits WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image
             FROM produits p
             WHERE id = :id AND statut = 'actif'"
        )->bind(':id', $produitId)->single();
        
        if (!$produit) {
            $_SESSION['flash_error'] = 'Produit introuvable ou indisponible.';
            header('Location: ' . URL_ROOT . '/catalogue');
            exit;
        }
        
        // Vérifier le stock
        $stock = $this->db->query(
            "SELECT stock FROM tailles_produits WHERE produit_id = :produit_id AND taille = :taille"
        )
        ->bind(':produit_id', $produitId)
        ->bind(':taille', $taille)
        ->single();
        
        if (!$stock || $stock['stock'] < $quantite) {
            $_SESSION['flash_error'] = 'Stock insuffisant pour cette taille.';
            header('Location: ' . URL_ROOT . '/produit/' . $produit['slug']);
            exit;
        }
        
        // Ajouter au panier
        $this->panier->ajouter(
            $produit['id'],
            $produit['nom'],
            $produit['prix'],
            $produit['prix_promo'],
            $produit['image'],
            $taille,
            $quantite
        );
        
        $_SESSION['flash_success'] = 'Produit ajouté au panier !';
        
        // Redirection
        $redirect = $_POST['redirect'] ?? 'panier';
        if ($redirect === 'produit') {
            header('Location: ' . URL_ROOT . '/produit/' . $produit['slug']);
        } else {
            header('Location: ' . URL_ROOT . '/panier');
        }
        exit;
    }
    
    /**
     * Modifie la quantité d'un article
     */
    public function modifier(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_ROOT . '/panier');
            exit;
        }
        
        $cle = $_POST['cle'] ?? '';
        $quantite = intval($_POST['quantite'] ?? 1);
        
        if (!empty($cle)) {
            $this->panier->modifierQuantite($cle, $quantite);
        }
        
        header('Location: ' . URL_ROOT . '/panier');
        exit;
    }
    
    /**
     * Supprime un article du panier
     */
    public function supprimer(): void {
        $cle = $_GET['cle'] ?? '';
        
        if (!empty($cle)) {
            $this->panier->supprimer($cle);
        }
        
        header('Location: ' . URL_ROOT . '/panier');
        exit;
    }
    
    /**
     * Vide le panier
     */
    public function vider(): void {
        $this->panier->vider();
        header('Location: ' . URL_ROOT . '/panier');
        exit;
    }
    
    /**
     * Commande WhatsApp avec tout le panier
     */
    public function commander(): void {
        if ($this->panier->estVide()) {
            header('Location: ' . URL_ROOT . '/panier');
            exit;
        }
        
        $articles = $this->panier->getArticles();
        $total = $this->panier->getTotal();
        
        // Construire le message WhatsApp
        $message = "Bonjour, je souhaite commander les articles suivants :\n\n";
        
        foreach ($articles as $article) {
            $message .= "👟 {$article['nom']}\n";
            $message .= "📏 Taille : {$article['taille']}\n";
            $message .= "🔢 Qté : {$article['quantite']}\n";
            $message .= "💰 " . number_format($article['prix'], 0, ',', ' ') . " DH / unité\n";
            $message .= "─ ─ ─ ─ ─ ─ ─ ─\n";
        }
        
        $message .= "\n💵 TOTAL : " . number_format($total, 0, ',', ' ') . " DH\n";
        $message .= "\nMerci !";
        
        $whatsappUrl = "https://wa.me/" . WHATSAPP . "?text=" . urlencode($message);
        
        // Vider le panier après commande
        $this->panier->vider();
        
        header('Location: ' . $whatsappUrl);
        exit;
    }
    
    /**
     * Retourne le nombre d'articles en JSON (pour AJAX)
     */
    public function count(): void {
        header('Content-Type: application/json');
        echo json_encode(['count' => $this->panier->getNombreArticles()]);
        exit;
    }
}

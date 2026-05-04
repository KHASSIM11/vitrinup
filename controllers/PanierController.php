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
     * Affiche le formulaire de commande ou traite la soumission
     */
    public function commander(): void {
        if ($this->panier->estVide()) {
            header('Location: ' . URL_ROOT . '/panier');
            exit;
        }

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterCommande();
            return;
        }

        $articles = $this->panier->getArticles();
        $total = $this->panier->getTotal();
        $economie = $this->panier->getEconomie();

        $data = [
            'title' => 'Finaliser la commande — ' . SITE_NAME,
            'articles' => $articles,
            'nombreArticles' => $this->panier->getNombreArticles(),
            'total' => $total,
            'economie' => $economie,
        ];

        $this->view('panier/commander', $data);
    }

    /**
     * Traite et sauvegarde la commande en base de données
     */
    private function traiterCommande(): void {
        $nom       = trim($_POST['nom'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $adresse   = trim($_POST['adresse'] ?? '');
        $ville     = trim($_POST['ville'] ?? '');
        $notes     = trim($_POST['notes'] ?? '');

        if (empty($nom) || empty($telephone)) {
            $_SESSION['flash_error'] = 'Veuillez remplir votre nom et votre téléphone.';
            header('Location: ' . URL_ROOT . '/panier/commander');
            exit;
        }

        $articles = $this->panier->getArticles();
        $total = $this->panier->getTotal();

        // Construire le message détaillé
        $message = "👤 Nom : $nom\n📞 Tél : $telephone\n";
        if (!empty($email)) $message .= "📧 Email : $email\n";
        if (!empty($adresse)) $message .= "📍 Adresse : $adresse\n";
        if (!empty($ville)) $message .= "🏙️ Ville : $ville\n";
        $message .= "\n🛒 ARTICLES COMMANDÉS :\n";
        
        foreach ($articles as $article) {
            $message .= "• {$article['nom']} — Taille {$article['taille']} × {$article['quantite']} = " . number_format($article['prix'] * $article['quantite'], 0, ',', ' ') . " DH\n";
        }
        
        $message .= "\n💰 TOTAL : " . number_format($total, 0, ',', ' ') . " DH\n";
        $message .= "🚚 Paiement à la livraison\n";
        
        if (!empty($notes)) {
            $message .= "\n📝 Notes : $notes\n";
        }

        // Sauvegarder chaque article comme une commande séparée
        foreach ($articles as $article) {
            $this->db->query(
                "INSERT INTO commandes (produit_id, client_nom, client_tel, taille, message, statut)
                 VALUES (:produit_id, :client_nom, :client_tel, :taille, :message, 'nouveau')"
            )
            ->bind(':produit_id', $article['produit_id'])
            ->bind(':client_nom', $nom)
            ->bind(':client_tel', $telephone)
            ->bind(':taille', $article['taille'])
            ->bind(':message', $message)
            ->execute();
        }

        // Construire le message WhatsApp
        $whatsappMessage = "🛒 *NOUVELLE COMMANDE* 🛒\n\n";
        $whatsappMessage .= "👤 *Client :* $nom\n";
        $whatsappMessage .= "📞 *Tél :* $telephone\n";
        if (!empty($email)) $whatsappMessage .= "📧 *Email :* $email\n";
        if (!empty($adresse)) $whatsappMessage .= "📍 *Adresse :* $adresse\n";
        if (!empty($ville)) $whatsappMessage .= "🏙️ *Ville :* $ville\n\n";
        $whatsappMessage .= "─ ─ ─ ─ ─ ─ ─ ─ ─ ─\n\n";
        
        foreach ($articles as $article) {
            $whatsappMessage .= "👟 {$article['nom']}\n";
            $whatsappMessage .= "📏 Taille : {$article['taille']}\n";
            $whatsappMessage .= "🔢 Qté : {$article['quantite']}\n";
            $whatsappMessage .= "💰 " . number_format($article['prix'], 0, ',', ' ') . " DH\n\n";
        }
        
        $whatsappMessage .= "💵 *TOTAL : " . number_format($total, 0, ',', ' ') . " DH*\n";
        $whatsappMessage .= "🚚 *Paiement à la livraison*\n";
        
        if (!empty($notes)) {
            $whatsappMessage .= "\n📝 *Notes :* $notes\n";
        }

        $whatsappUrl = "https://wa.me/" . WHATSAPP . "?text=" . urlencode($whatsappMessage);
        
        // Vider le panier
        $this->panier->vider();
        
        $_SESSION['flash_success'] = '✅ Commande enregistrée ! Vous allez être redirigé vers WhatsApp.';
        
        // Rediriger vers WhatsApp
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

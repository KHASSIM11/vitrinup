<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class AdminController extends Controller {

    private $db;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->requireAdmin();
        $this->db = new Database();
    }

    private function requireAdmin(): void {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: ' . URL_ROOT . '/admin/login');
            exit;
        }
    }

    public function index(): void {
        $totalActifs   = $this->db->query("SELECT COUNT(*) AS cnt FROM produits WHERE statut = 'actif'")->single()['cnt'];
        $totalInactifs = $this->db->query("SELECT COUNT(*) AS cnt FROM produits WHERE statut = 'inactif'")->single()['cnt'];
        $totalCommandes = $this->db->query("SELECT COUNT(*) AS cnt FROM commandes")->single()['cnt'];
        $nouvellesCommandes = $this->db->query("SELECT COUNT(*) AS cnt FROM commandes WHERE statut = 'nouveau'")->single()['cnt'];

        $dernieresCmds = $this->db->query(
            "SELECT cm.*, p.nom AS produit_nom
             FROM commandes cm
             JOIN produits p ON cm.produit_id = p.id
             ORDER BY cm.created_at DESC
             LIMIT 5"
        )->resultSet();

        // ── Stats stocks ──
        $stockFaible = $this->db->query(
            "SELECT p.id, p.nom, p.slug, p.marque,
                    (SELECT chemin FROM images_produits WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image,
                    (SELECT COALESCE(SUM(tp.stock), 0) FROM tailles_produits tp WHERE tp.produit_id = p.id) AS stock_total
             FROM produits p
             WHERE (SELECT COALESCE(SUM(tp.stock), 0) FROM tailles_produits tp WHERE tp.produit_id = p.id) <= :seuil
             AND p.statut = 'actif'
             ORDER BY stock_total ASC
             LIMIT 10"
        )->bind(':seuil', STOCK_SEUIL_ALERTE)->resultSet();

        $ruptureStock = $this->db->query(
            "SELECT p.id, p.nom, p.slug, p.marque
             FROM produits p
             WHERE p.id NOT IN (SELECT tp.produit_id FROM tailles_produits tp WHERE tp.stock > 0)
             AND p.statut = 'actif'
             LIMIT 10"
        )->resultSet();

        $nbStockFaible = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM produits p
             WHERE (SELECT COALESCE(SUM(tp.stock), 0) FROM tailles_produits tp WHERE tp.produit_id = p.id) <= :seuil2
             AND p.statut = 'actif'"
        )->bind(':seuil2', STOCK_SEUIL_ALERTE)->single()['cnt'];

        $nbRupture = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM produits p
             WHERE p.id NOT IN (SELECT tp.produit_id FROM tailles_produits tp WHERE tp.stock > 0)
             AND p.statut = 'actif'"
        )->single()['cnt'];

        $data = [
            'totalActifs'        => $totalActifs,
            'totalInactifs'      => $totalInactifs,
            'totalCommandes'     => $totalCommandes,
            'nouvellesCommandes' => $nouvellesCommandes,
            'dernieresCmds'      => $dernieresCmds,
            'stockFaible'        => $stockFaible,
            'ruptureStock'       => $ruptureStock,
            'nbStockFaible'      => $nbStockFaible,
            'nbRupture'          => $nbRupture,
            'adminNom'           => $_SESSION['admin_nom'],
        ];

        $this->view('admin/dashboard', $data);
    }
}
?>

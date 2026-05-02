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

        $data = [
            'totalActifs'        => $totalActifs,
            'totalInactifs'      => $totalInactifs,
            'totalCommandes'     => $totalCommandes,
            'nouvellesCommandes' => $nouvellesCommandes,
            'dernieresCmds'      => $dernieresCmds,
            'adminNom'           => $_SESSION['admin_nom'],
        ];

        $this->view('admin/dashboard', $data);
    }
}
?>

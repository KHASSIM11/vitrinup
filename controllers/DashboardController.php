<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class DashboardController extends Controller {
    private $db;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = new Database();
    }

    private function requireLogin(): void {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . URL_ROOT . '/login');
            exit;
        }
    }

    public function index(): void {
        $this->requireLogin();

        $boutiqueId = $_SESSION['boutique_id'] ?? null;
        if (!$boutiqueId) {
            echo 'Boutique non définie.';
            return;
        }

        // Infos boutique
        $boutique = $this->db->query('SELECT * FROM boutiques WHERE id = :id')
                             ->bind(':id', $boutiqueId)
                             ->single();

        // Statistiques
        $productCount = $this->db->query('SELECT COUNT(*) AS cnt FROM produits WHERE boutique_id = :id')
                                 ->bind(':id', $boutiqueId)
                                 ->single()['cnt'];

        $orderCount = $this->db->query('SELECT COUNT(*) AS cnt FROM commandes WHERE boutique_id = :id')
                               ->bind(':id', $boutiqueId)
                               ->single()['cnt'];

        $data = [
            'boutique'     => $boutique,
            'productCount' => $productCount,
            'orderCount'   => $orderCount
        ];

        $this->view('dashboard/index', $data);
    }
}
?>

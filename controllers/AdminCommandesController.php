<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class AdminCommandesController extends Controller {

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

    // ── LISTE ──────────────────────────────────────────────
    public function index(): void {
        $statut = $_GET['statut'] ?? '';
        $page   = max(1, intval($_GET['page'] ?? 1));
        $limite = 15;
        $offset = ($page - 1) * $limite;

        $where  = '';
        $params = [];

        if (!empty($statut)) {
            $where = " WHERE cm.statut = :statut";
            $params[':statut'] = $statut;
        }

        // Compte total
        $countQuery = $this->db->query("SELECT COUNT(*) AS cnt FROM commandes cm" . $where);
        foreach ($params as $key => $val) {
            $countQuery->bind($key, $val);
        }
        $total      = $countQuery->single()['cnt'];
        $totalPages = max(1, ceil($total / $limite));

        // Requête paginée
        $sql = "SELECT cm.*, p.nom AS produit_nom, p.slug AS produit_slug
                FROM commandes cm
                JOIN produits p ON cm.produit_id = p.id"
                . $where .
                " ORDER BY cm.created_at DESC
                LIMIT $limite OFFSET $offset";

        $query = $this->db->query($sql);
        foreach ($params as $key => $val) {
            $query->bind($key, $val);
        }
        $commandes = $query->resultSet();

        // Stats pour les filtres
        $stats = $this->db->query(
            "SELECT statut, COUNT(*) AS cnt FROM commandes GROUP BY statut ORDER BY statut ASC"
        )->resultSet();

        $statsParStatut = [];
        foreach ($stats as $s) {
            $statsParStatut[$s['statut']] = $s['cnt'];
        }

        $this->view('admin/commandes/index', [
            'commandes'      => $commandes,
            'statutActif'    => $statut,
            'statsParStatut' => $statsParStatut,
            'page'           => $page,
            'totalPages'     => $totalPages,
            'total'          => $total,
            'adminNom'       => $_SESSION['admin_nom'],
        ]);
    }

    // ── VOIR DÉTAIL ────────────────────────────────────────
    public function voir($id): void {
        $commande = $this->db->query(
            "SELECT cm.*, p.nom AS produit_nom, p.slug AS produit_slug, p.prix, p.prix_promo
             FROM commandes cm
             JOIN produits p ON cm.produit_id = p.id
             WHERE cm.id = :id"
        )->bind(':id', $id)->single();

        if (!$commande) {
            header('Location: ' . URL_ROOT . '/admin/commandes');
            exit;
        }

        $this->view('admin/commandes/voir', [
            'commande' => $commande,
            'adminNom' => $_SESSION['admin_nom'],
        ]);
    }

    // ── CHANGER STATUT ─────────────────────────────────────
    public function statut($id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_ROOT . '/admin/commandes');
            exit;
        }

        $nouveauStatut = $_POST['statut'] ?? '';
        $statutsValides = ['nouveau', 'vu', 'confirme', 'annule'];

        if (!in_array($nouveauStatut, $statutsValides)) {
            $_SESSION['flash_error'] = 'Statut invalide.';
            header('Location: ' . URL_ROOT . '/admin/commandes');
            exit;
        }

        $this->db->query("UPDATE commandes SET statut = :statut WHERE id = :id")
                 ->bind(':statut', $nouveauStatut)
                 ->bind(':id', $id)
                 ->execute();

        $_SESSION['flash_success'] = 'Statut de la commande mis à jour.';
        header('Location: ' . URL_ROOT . '/admin/commandes/voir/' . $id);
        exit;
    }

    // ── SUPPRIMER ──────────────────────────────────────────
    public function supprimer($id): void {
        $this->db->query("DELETE FROM commandes WHERE id = :id")->bind(':id', $id)->execute();

        $_SESSION['flash_success'] = 'Commande supprimée.';
        header('Location: ' . URL_ROOT . '/admin/commandes');
        exit;
    }
}

<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class AdminStocksController extends Controller {

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

    // ── LISTE STOCKS ───────────────────────────────────────
    public function index(): void {
        $search  = trim($_GET['search'] ?? '');
        $statut  = $_GET['statut'] ?? '';
        $page    = max(1, intval($_GET['page'] ?? 1));
        $limite  = 20;
        $offset  = ($page - 1) * $limite;

        $where   = [];
        $params  = [];

        if (!empty($search)) {
            $where[] = "p.nom LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }
        if ($statut === 'faible') {
            $where[] = "(SELECT COALESCE(SUM(tp2.stock), 0) FROM tailles_produits tp2 WHERE tp2.produit_id = p.id) <= :seuil";
            $params[':seuil'] = STOCK_SEUIL_ALERTE;
        } elseif ($statut === 'rupture') {
            $where[] = "p.id NOT IN (SELECT tp3.produit_id FROM tailles_produits tp3 WHERE tp3.stock > 0)";
        } elseif ($statut === 'ok') {
            $where[] = "(SELECT COALESCE(SUM(tp4.stock), 0) FROM tailles_produits tp4 WHERE tp4.produit_id = p.id) > :seuil2";
            $params[':seuil2'] = STOCK_SEUIL_ALERTE;
        }

        $whereClause = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

        // Total
        $countSql = "SELECT COUNT(*) AS cnt FROM produits p" . $whereClause;
        $countQuery = $this->db->query($countSql);
        foreach ($params as $k => $v) {
            $countQuery->bind($k, $v);
        }
        $total = $countQuery->single()['cnt'];
        $totalPages = max(1, ceil($total / $limite));

        // Produits avec stock total
        $sql = "SELECT p.id, p.nom, p.slug, p.statut, p.marque,
                       (SELECT chemin FROM images_produits WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image,
                       (SELECT COALESCE(SUM(tp.stock), 0) FROM tailles_produits tp WHERE tp.produit_id = p.id) AS stock_total,
                       (SELECT COUNT(*) FROM tailles_produits tp WHERE tp.produit_id = p.id) AS nb_tailles
                FROM produits p"
                . $whereClause .
                " ORDER BY stock_total ASC, p.nom ASC
                LIMIT $limite OFFSET $offset";

        $query = $this->db->query($sql);
        foreach ($params as $k => $v) {
            $query->bind($k, $v);
        }
        $produits = $query->resultSet();

        // Récupérer les tailles pour chaque produit
        $produitIds = array_column($produits, 'id');
        $taillesParProduit = [];
        if (!empty($produitIds)) {
            $idsStr = implode(',', $produitIds);
            $tailles = $this->db->query(
                "SELECT produit_id, taille, stock FROM tailles_produits WHERE produit_id IN ($idsStr) ORDER BY taille ASC"
            )->resultSet();
            foreach ($tailles as $t) {
                $taillesParProduit[$t['produit_id']][] = $t;
            }
        }

        // Stats globales
        $stats = $this->db->query(
            "SELECT
                (SELECT COUNT(*) FROM produits p1 WHERE p1.id IN (SELECT tp1.produit_id FROM tailles_produits tp1 GROUP BY tp1.produit_id HAVING COALESCE(SUM(tp1.stock), 0) <= :seuil3)) AS stock_faible,
                (SELECT COUNT(*) FROM produits p2 WHERE p2.id NOT IN (SELECT tp2.produit_id FROM tailles_produits tp2 WHERE tp2.stock > 0) AND p2.statut = 'actif') AS rupture,
                (SELECT COUNT(*) FROM produits p3 WHERE p3.id IN (SELECT tp3.produit_id FROM tailles_produits tp3)) AS avec_stock"
        )->bind(':seuil3', STOCK_SEUIL_ALERTE)->single();

        $this->view('admin/stocks/index', [
            'produits'          => $produits,
            'taillesParProduit' => $taillesParProduit,
            'search'            => $search,
            'statut'            => $statut,
            'page'              => $page,
            'totalPages'        => $totalPages,
            'total'             => $total,
            'stats'             => $stats,
            'adminNom'          => $_SESSION['admin_nom'],
        ]);
    }

    // ── MODIFIER STOCK (AJAX) ──────────────────────────────
    public function modifierStock(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            exit;
        }

        $tailleId  = intval($_POST['taille_id'] ?? 0);
        $nouveauStock = intval($_POST['stock'] ?? -1);

        if ($tailleId <= 0 || $nouveauStock < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Paramètres invalides']);
            exit;
        }

        $this->db->query("UPDATE tailles_produits SET stock = :stock WHERE id = :id")
                 ->bind(':stock', $nouveauStock)
                 ->bind(':id', $tailleId)
                 ->execute();

        echo json_encode(['success' => true]);
        exit;
    }

    // ── AJOUTER UNE TAILLE ─────────────────────────────────
    public function ajouterTaille(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            exit;
        }

        $produitId = intval($_POST['produit_id'] ?? 0);
        $taille    = trim($_POST['taille'] ?? '');
        $stock     = intval($_POST['stock'] ?? 0);

        if ($produitId <= 0 || empty($taille)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Paramètres invalides']);
            exit;
        }

        // Vérifier si la taille existe déjà
        $existe = $this->db->query(
            "SELECT id FROM tailles_produits WHERE produit_id = :pid AND taille = :taille"
        )->bind(':pid', $produitId)->bind(':taille', $taille)->single();

        if ($existe) {
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => 'Cette taille existe déjà pour ce produit']);
            exit;
        }

        $this->db->query(
            "INSERT INTO tailles_produits (produit_id, taille, stock) VALUES (:pid, :taille, :stock)"
        )->bind(':pid', $produitId)->bind(':taille', $taille)->bind(':stock', $stock)->execute();

        $newId = $this->db->lastInsertId();

        echo json_encode(['success' => true, 'id' => $newId]);
        exit;
    }

    // ── SUPPRIMER UNE TAILLE ───────────────────────────────
    public function supprimerTaille(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            exit;
        }

        $tailleId = intval($_POST['taille_id'] ?? 0);

        if ($tailleId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Paramètres invalides']);
            exit;
        }

        $this->db->query("DELETE FROM tailles_produits WHERE id = :id")->bind(':id', $tailleId)->execute();

        echo json_encode(['success' => true]);
        exit;
    }
}

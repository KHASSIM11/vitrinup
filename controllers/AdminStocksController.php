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

    // ── DASHBOARD STOCKS ────────────────────────────────────
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
                "SELECT id, produit_id, taille, stock FROM tailles_produits WHERE produit_id IN ($idsStr) ORDER BY taille ASC"
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

    // ── ENTRÉE DE STOCK ─────────────────────────────────────
    public function entree(): void {
        $error = '';
        $success = '';

        // Récupérer tous les produits actifs avec leurs tailles
        $produits = $this->db->query(
            "SELECT p.id, p.nom, p.marque,
                    (SELECT chemin FROM images_produits WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image
             FROM produits p WHERE p.statut = 'actif' ORDER BY p.nom ASC"
        )->resultSet();

        // Récupérer les tailles pour tous les produits
        $produitIds = array_column($produits, 'id');
        $taillesParProduit = [];
        if (!empty($produitIds)) {
            $idsStr = implode(',', $produitIds);
            $tailles = $this->db->query(
                "SELECT id, produit_id, taille, stock FROM tailles_produits WHERE produit_id IN ($idsStr) ORDER BY taille ASC"
            )->resultSet();
            foreach ($tailles as $t) {
                $taillesParProduit[$t['produit_id']][] = $t;
            }
        }

        $this->view('admin/stocks/entree', [
            'produits'          => $produits,
            'taillesParProduit' => $taillesParProduit,
            'error'             => $error,
            'success'           => $success,
            'adminNom'          => $_SESSION['admin_nom'],
        ]);
    }

    // ── AJOUTER ENTRÉE (POST) ──────────────────────────────
    public function ajouterEntree(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_ROOT . '/admin/stocks/entree');
            exit;
        }

        $produitId = intval($_POST['produit_id'] ?? 0);
        $tailleId  = intval($_POST['taille_id'] ?? 0);
        $quantite  = intval($_POST['quantite'] ?? 0);
        $reference = trim($_POST['reference'] ?? '');

        if ($produitId <= 0 || $tailleId <= 0 || $quantite <= 0) {
            $_SESSION['flash_error'] = 'Paramètres invalides.';
            header('Location: ' . URL_ROOT . '/admin/stocks/entree');
            exit;
        }

        // Récupérer la taille
        $taille = $this->db->query(
            "SELECT tp.*, p.nom AS produit_nom FROM tailles_produits tp JOIN produits p ON tp.produit_id = p.id WHERE tp.id = :id AND tp.produit_id = :pid"
        )->bind(':id', $tailleId)->bind(':pid', $produitId)->single();

        if (!$taille) {
            $_SESSION['flash_error'] = 'Taille introuvable.';
            header('Location: ' . URL_ROOT . '/admin/stocks/entree');
            exit;
        }

        $stockAvant = intval($taille['stock']);
        $stockApres = $stockAvant + $quantite;

        // Mettre à jour le stock
        $this->db->query("UPDATE tailles_produits SET stock = :stock WHERE id = :id")
                 ->bind(':stock', $stockApres)
                 ->bind(':id', $tailleId)
                 ->execute();

        // Enregistrer le mouvement
        $this->db->query(
            "INSERT INTO mouvements_stock (produit_id, taille_id, taille, type, quantite, stock_avant, stock_apres, reference)
             VALUES (:produit_id, :taille_id, :taille, 'entree', :quantite, :stock_avant, :stock_apres, :reference)"
        )
        ->bind(':produit_id', $produitId)
        ->bind(':taille_id', $tailleId)
        ->bind(':taille', $taille['taille'])
        ->bind(':quantite', $quantite)
        ->bind(':stock_avant', $stockAvant)
        ->bind(':stock_apres', $stockApres)
        ->bind(':reference', $reference)
        ->execute();

        $_SESSION['flash_success'] = 'Entrée de stock ajoutée : +' . $quantite . ' pour ' . htmlspecialchars($taille['produit_nom']) . ' (taille ' . $taille['taille'] . ')';
        header('Location: ' . URL_ROOT . '/admin/stocks/entree');
        exit;
    }

    // ── SORTIE DE STOCK ─────────────────────────────────────
    public function sortie(): void {
        // Récupérer les commandes qui impactent le stock
        $commandes = $this->db->query(
            "SELECT c.id, c.produit_id, c.taille, c.client_nom, c.client_tel, c.statut, c.created_at,
                    p.nom AS produit_nom, p.marque,
                    tp.id AS taille_id, tp.stock AS stock_actuel
             FROM commandes c
             JOIN produits p ON c.produit_id = p.id
             LEFT JOIN tailles_produits tp ON tp.produit_id = c.produit_id AND tp.taille = c.taille
             WHERE c.statut IN ('nouveau', 'vu', 'confirme')
             ORDER BY c.created_at DESC"
        )->resultSet();

        $this->view('admin/stocks/sortie', [
            'commandes' => $commandes,
            'adminNom'  => $_SESSION['admin_nom'],
        ]);
    }

    // ── AJOUTER SORTIE (POST) ──────────────────────────────
    public function ajouterSortie(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_ROOT . '/admin/stocks/sortie');
            exit;
        }

        $commandeId = intval($_POST['commande_id'] ?? 0);
        $action     = $_POST['action'] ?? ''; // 'confirmer' ou 'annuler'

        if ($commandeId <= 0 || !in_array($action, ['confirmer', 'annuler'])) {
            $_SESSION['flash_error'] = 'Paramètres invalides.';
            header('Location: ' . URL_ROOT . '/admin/stocks/sortie');
            exit;
        }

        // Récupérer la commande
        $commande = $this->db->query(
            "SELECT c.*, p.nom AS produit_nom FROM commandes c JOIN produits p ON c.produit_id = p.id WHERE c.id = :id"
        )->bind(':id', $commandeId)->single();

        if (!$commande) {
            $_SESSION['flash_error'] = 'Commande introuvable.';
            header('Location: ' . URL_ROOT . '/admin/stocks/sortie');
            exit;
        }

        // Récupérer la taille correspondante
        $taille = $this->db->query(
            "SELECT * FROM tailles_produits WHERE produit_id = :pid AND taille = :taille"
        )->bind(':pid', $commande['produit_id'])->bind(':taille', $commande['taille'])->single();

        if ($action === 'confirmer') {
            // Déduire le stock (1 unité par commande)
            if ($taille) {
                $stockAvant = intval($taille['stock']);
                $stockApres = max(0, $stockAvant - 1);

                $this->db->query("UPDATE tailles_produits SET stock = :stock WHERE id = :id")
                         ->bind(':stock', $stockApres)
                         ->bind(':id', $taille['id'])
                         ->execute();

                // Enregistrer le mouvement
                $this->db->query(
                    "INSERT INTO mouvements_stock (produit_id, taille_id, taille, type, quantite, stock_avant, stock_apres, reference)
                     VALUES (:produit_id, :taille_id, :taille, 'commande', 1, :stock_avant, :stock_apres, :reference)"
                )
                ->bind(':produit_id', $commande['produit_id'])
                ->bind(':taille_id', $taille['id'])
                ->bind(':taille', $commande['taille'])
                ->bind(':stock_avant', $stockAvant)
                ->bind(':stock_apres', $stockApres)
                ->bind(':reference', 'Commande #' . $commandeId . ' - ' . $commande['client_nom'])
                ->execute();
            }

            // Mettre à jour le statut de la commande
            $this->db->query("UPDATE commandes SET statut = 'confirme' WHERE id = :id")
                     ->bind(':id', $commandeId)
                     ->execute();

            $_SESSION['flash_success'] = 'Commande #' . $commandeId . ' confirmée. Stock déduit.';

        } elseif ($action === 'annuler') {
            // Remettre le stock si la commande était déjà confirmée
            if ($commande['statut'] === 'confirme' && $taille) {
                $stockAvant = intval($taille['stock']);
                $stockApres = $stockAvant + 1;

                $this->db->query("UPDATE tailles_produits SET stock = :stock WHERE id = :id")
                         ->bind(':stock', $stockApres)
                         ->bind(':id', $taille['id'])
                         ->execute();

                // Enregistrer le mouvement d'annulation
                $this->db->query(
                    "INSERT INTO mouvements_stock (produit_id, taille_id, taille, type, quantite, stock_avant, stock_apres, reference)
                     VALUES (:produit_id, :taille_id, :taille, 'annulation', 1, :stock_avant, :stock_apres, :reference)"
                )
                ->bind(':produit_id', $commande['produit_id'])
                ->bind(':taille_id', $taille['id'])
                ->bind(':taille', $commande['taille'])
                ->bind(':stock_avant', $stockAvant)
                ->bind(':stock_apres', $stockApres)
                ->bind(':reference', 'Annulation commande #' . $commandeId)
                ->execute();
            }

            $this->db->query("UPDATE commandes SET statut = 'annule' WHERE id = :id")
                     ->bind(':id', $commandeId)
                     ->execute();

            $_SESSION['flash_success'] = 'Commande #' . $commandeId . ' annulée.';
        }

        header('Location: ' . URL_ROOT . '/admin/stocks/sortie');
        exit;
    }

    // ── HISTORIQUE ──────────────────────────────────────────
    public function historique(): void {
        $page   = max(1, intval($_GET['page'] ?? 1));
        $limite = 30;
        $offset = ($page - 1) * $limite;

        $typeFiltre = $_GET['type'] ?? '';
        $produitId  = intval($_GET['produit_id'] ?? 0);

        $where  = [];
        $params = [];

        if (!empty($typeFiltre)) {
            $where[] = "m.type = :type";
            $params[':type'] = $typeFiltre;
        }
        if ($produitId > 0) {
            $where[] = "m.produit_id = :produit_id";
            $params[':produit_id'] = $produitId;
        }

        $whereClause = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

        // Total
        $countQuery = $this->db->query("SELECT COUNT(*) AS cnt FROM mouvements_stock m" . $whereClause);
        foreach ($params as $k => $v) {
            $countQuery->bind($k, $v);
        }
        $total = $countQuery->single()['cnt'];
        $totalPages = max(1, ceil($total / $limite));

        // Mouvements
        $sql = "SELECT m.*, p.nom AS produit_nom, p.marque
                FROM mouvements_stock m
                JOIN produits p ON m.produit_id = p.id"
                . $whereClause .
                " ORDER BY m.created_at DESC
                LIMIT $limite OFFSET $offset";

        $query = $this->db->query($sql);
        foreach ($params as $k => $v) {
            $query->bind($k, $v);
        }
        $mouvements = $query->resultSet();

        // Liste des produits pour le filtre
        $produits = $this->db->query("SELECT id, nom FROM produits ORDER BY nom ASC")->resultSet();

        $this->view('admin/stocks/historique', [
            'mouvements'  => $mouvements,
            'produits'    => $produits,
            'typeFiltre'  => $typeFiltre,
            'produitId'   => $produitId,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'total'       => $total,
            'adminNom'    => $_SESSION['admin_nom'],
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

        // Récupérer l'ancien stock
        $ancien = $this->db->query("SELECT stock, produit_id, taille FROM tailles_produits WHERE id = :id")
                           ->bind(':id', $tailleId)->single();

        if (!$ancien) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Taille introuvable']);
            exit;
        }

        $stockAvant = intval($ancien['stock']);
        $diff = $nouveauStock - $stockAvant;

        $this->db->query("UPDATE tailles_produits SET stock = :stock WHERE id = :id")
                 ->bind(':stock', $nouveauStock)
                 ->bind(':id', $tailleId)
                 ->execute();

        // Enregistrer le mouvement si différence
        if ($diff !== 0) {
            $type = $diff > 0 ? 'entree' : 'sortie';
            $this->db->query(
                "INSERT INTO mouvements_stock (produit_id, taille_id, taille, type, quantite, stock_avant, stock_apres, reference)
                 VALUES (:produit_id, :taille_id, :taille, :type, :quantite, :stock_avant, :stock_apres, :reference)"
            )
            ->bind(':produit_id', $ancien['produit_id'])
            ->bind(':taille_id', $tailleId)
            ->bind(':taille', $ancien['taille'])
            ->bind(':type', $type)
            ->bind(':quantite', abs($diff))
            ->bind(':stock_avant', $stockAvant)
            ->bind(':stock_apres', $nouveauStock)
            ->bind(':reference', 'Ajustement manuel')
            ->execute();
        }

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

        // Enregistrer le mouvement initial
        if ($stock > 0) {
            $this->db->query(
                "INSERT INTO mouvements_stock (produit_id, taille_id, taille, type, quantite, stock_avant, stock_apres, reference)
                 VALUES (:produit_id, :taille_id, :taille, 'entree', :quantite, 0, :stock_apres, 'Création taille')"
            )
            ->bind(':produit_id', $produitId)
            ->bind(':taille_id', $newId)
            ->bind(':taille', $taille)
            ->bind(':quantite', $stock)
            ->bind(':stock_apres', $stock)
            ->execute();
        }

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
?>

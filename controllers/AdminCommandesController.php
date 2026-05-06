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

        // Récupérer la commande actuelle pour connaître l'ancien statut
        $commande = $this->db->query(
            "SELECT * FROM commandes WHERE id = :id"
        )->bind(':id', $id)->single();

        if (!$commande) {
            $_SESSION['flash_error'] = 'Commande introuvable.';
            header('Location: ' . URL_ROOT . '/admin/commandes');
            exit;
        }

        $ancienStatut = $commande['statut'];
        $quantiteCommande = max(1, intval($commande['quantite'] ?? 1));

        // Gérer le stock selon les transitions de statut
        if ($ancienStatut !== $nouveauStatut) {
            $taille = $this->db->query(
                "SELECT * FROM tailles_produits WHERE produit_id = :pid AND taille = :taille"
            )->bind(':pid', $commande['produit_id'])->bind(':taille', $commande['taille'])->single();

            // Si on confirme : déduire le stock
            if ($nouveauStatut === 'confirme' && $ancienStatut !== 'confirme') {
                if ($taille) {
                    $stockAvant = intval($taille['stock']);
                    $stockApres = max(0, $stockAvant - $quantiteCommande);

                    $this->db->query("UPDATE tailles_produits SET stock = :stock WHERE id = :id")
                             ->bind(':stock', $stockApres)
                             ->bind(':id', $taille['id'])
                             ->execute();

                    $this->db->query(
                        "INSERT INTO mouvements_stock (produit_id, taille_id, taille, type, quantite, stock_avant, stock_apres, reference)
                         VALUES (:produit_id, :taille_id, :taille, 'commande', :quantite, :stock_avant, :stock_apres, :reference)"
                    )
                    ->bind(':produit_id', $commande['produit_id'])
                    ->bind(':taille_id', $taille['id'])
                    ->bind(':taille', $commande['taille'])
                    ->bind(':quantite', $quantiteCommande)
                    ->bind(':stock_avant', $stockAvant)
                    ->bind(':stock_apres', $stockApres)
                    ->bind(':reference', 'Commande #' . $id . ' - ' . $commande['client_nom'])
                    ->execute();
                }
            }

            // Si on annule depuis "confirme" : remettre le stock
            if ($nouveauStatut === 'annule' && $ancienStatut === 'confirme') {
                if ($taille) {
                    $stockAvant = intval($taille['stock']);
                    $stockApres = $stockAvant + $quantiteCommande;

                    $this->db->query("UPDATE tailles_produits SET stock = :stock WHERE id = :id")
                             ->bind(':stock', $stockApres)
                             ->bind(':id', $taille['id'])
                             ->execute();

                    $this->db->query(
                        "INSERT INTO mouvements_stock (produit_id, taille_id, taille, type, quantite, stock_avant, stock_apres, reference)
                         VALUES (:produit_id, :taille_id, :taille, 'annulation', :quantite, :stock_avant, :stock_apres, :reference)"
                    )
                    ->bind(':produit_id', $commande['produit_id'])
                    ->bind(':taille_id', $taille['id'])
                    ->bind(':taille', $commande['taille'])
                    ->bind(':quantite', $quantiteCommande)
                    ->bind(':stock_avant', $stockAvant)
                    ->bind(':stock_apres', $stockApres)
                    ->bind(':reference', 'Annulation commande #' . $id)
                    ->execute();
                }
            }

            // Si on repasse de "annule" à "confirme" : re-déduire le stock
            if ($nouveauStatut === 'confirme' && $ancienStatut === 'annule') {
                if ($taille) {
                    $stockAvant = intval($taille['stock']);
                    $stockApres = max(0, $stockAvant - $quantiteCommande);

                    $this->db->query("UPDATE tailles_produits SET stock = :stock WHERE id = :id")
                             ->bind(':stock', $stockApres)
                             ->bind(':id', $taille['id'])
                             ->execute();

                    $this->db->query(
                        "INSERT INTO mouvements_stock (produit_id, taille_id, taille, type, quantite, stock_avant, stock_apres, reference)
                         VALUES (:produit_id, :taille_id, :taille, 'commande', :quantite, :stock_avant, :stock_apres, :reference)"
                    )
                    ->bind(':produit_id', $commande['produit_id'])
                    ->bind(':taille_id', $taille['id'])
                    ->bind(':taille', $commande['taille'])
                    ->bind(':quantite', $quantiteCommande)
                    ->bind(':stock_avant', $stockAvant)
                    ->bind(':stock_apres', $stockApres)
                    ->bind(':reference', 'Commande #' . $id . ' - ' . $commande['client_nom'])
                    ->execute();
                }
            }
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

<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class SuperAdminController extends Controller {
    private $db;

    public function __construct() {
        // Assurer que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = new Database();
    }

    /**
     * Vérifie si le super‑admin ou un admin temporaire est connecté.
     */
    private function isLogged(): bool {
        return isset($_SESSION['role']) && (
            $_SESSION['role'] === 'superadmin' ||
            $_SESSION['role'] === 'admin'      // rôle admin accepté temporairement
        );
    }

    /**
     * Redirige vers la page de login si l'utilisateur n'est pas authentifié.
     */
    private function requireLogin(): void {
        if (!$this->isLogged()) {
            header('Location: ' . URL_ROOT . '/login');
            exit;
        }
    }

    /**
     * Affiche le formulaire de login et le traite.
     */
    public function login(): void {
        // Si le formulaire est soumis, on vérifie les identifiants
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // *** NOTE ***
            // Pour cet exemple, les identifiants sont codés en dur.
            // Dans une vraie application, ils seraient stockés dans la base de données.
            $validEmail = 'superadmin@vitrinup.com';
            $validHash  = password_hash('SuperSecret123', PASSWORD_DEFAULT);

            if ($email === $validEmail && password_verify($password, $validHash)) {
                $_SESSION['role'] = 'superadmin';
                header('Location: ' . URL_ROOT . '/superadmin');
                exit;
            } else {
                $error = 'Identifiants invalides.';
                $this->view('superadmin/login', ['error' => $error]);
                return;
            }
        }

        // Affichage du formulaire
        $this->view('superadmin/login');
    }

    /**
     * Déconnexion du super‑admin.
     */
    public function logout(): void {
        session_destroy();
        header('Location: ' . URL_ROOT . '/login');
        exit;
    }

    /**
     * Tableau de bord du super‑admin : liste toutes les boutiques avec leurs statistiques.
     */
    public function index(): void {
        $this->requireLogin();

        // Récupération de toutes les boutiques, triées par date de création décroissante
        $boutiques = $this->db
            ->query('SELECT * FROM boutiques ORDER BY created_at DESC')
            ->resultSet();

        // Statistiques : nombre de produits par boutique
        $productCounts = $this->db
            ->query('SELECT boutique_id, COUNT(*) AS product_count FROM produits GROUP BY boutique_id')
            ->resultSet();
        $productMap = [];
        foreach ($productCounts as $row) {
            $productMap[$row['boutique_id']] = $row['product_count'];
        }

        // Statistiques : nombre de commandes par boutique
        $orderCounts = $this->db
            ->query('SELECT boutique_id, COUNT(*) AS order_count FROM commandes GROUP BY boutique_id')
            ->resultSet();
        $orderMap = [];
        foreach ($orderCounts as $row) {
            $orderMap[$row['boutique_id']] = $row['order_count'];
        }

        // Transmission des données à la vue
        $data = [
            'boutiques'   => $boutiques,
            'productMap'  => $productMap,
            'orderMap'    => $orderMap
        ];
        $this->view('superadmin/index', $data);
    }

    /**
     * Active ou désactive une boutique.
     * URL attendue : /superadmin/toggle?id=XX
     */
    public function toggle(): void {
        $this->requireLogin();

        $id = $_GET['id'] ?? null;
        if ($id) {
            // Récupérer le statut actuel
            $this->db->query('SELECT statut FROM boutiques WHERE id = :id')
                ->bind(':id', $id)
                ->execute();
            $row = $this->db->single();

            if ($row) {
                $newStatus = ($row['statut'] === 'active') ? 'inactive' : 'active';
                $this->db->query('UPDATE boutiques SET statut = :statut WHERE id = :id')
                    ->bind(':statut', $newStatus)
                    ->bind(':id', $id)
                    ->execute();
            }
        }

        header('Location: ' . URL_ROOT . '/superadmin');
        exit;
    }
}
?>

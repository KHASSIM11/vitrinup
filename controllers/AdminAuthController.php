<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class AdminAuthController extends Controller {

    private $db;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->db = new Database();
    }

    public function login(): void {
        // Déjà connecté → redirect
        if (isset($_SESSION['admin_id'])) {
            header('Location: ' . URL_ROOT . '/admin');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $admin = $this->db->query(
                "SELECT * FROM admins WHERE email = :email"
            )->bind(':email', $email)->single();

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id']  = $admin['id'];
                $_SESSION['admin_nom'] = $admin['nom'];
                header('Location: ' . URL_ROOT . '/admin');
                exit;
            } else {
                $error = 'Email ou mot de passe incorrect.';
            }
        }

        $this->view('admin/login', ['error' => $error]);
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header('Location: ' . URL_ROOT . '/admin/login');
        exit;
    }
}
?>

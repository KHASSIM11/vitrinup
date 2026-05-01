<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class AuthController extends Controller {
    private $db;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = new Database();
    }

    // Affiche le formulaire de login et le traite
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Recherche de l'utilisateur
            $user = $this->db->query('SELECT * FROM utilisateurs WHERE email = :email')
                             ->bind(':email', $email)
                             ->single();

            if ($user && password_verify($password, $user['password'])) {
                // Super admin spécial (identifié par l'email)
                if ($user['email'] === 'admin@vitrinup.com') {
                    $_SESSION['role'] = 'superadmin';
                } else {
                    $_SESSION['role'] = $user['role'];
                }

                $_SESSION['user_id']     = $user['id'];
                $_SESSION['boutique_id'] = $user['boutique_id'];

                // Redirection selon le rôle
                if ($_SESSION['role'] === 'superadmin') {
                    header('Location: ' . URL_ROOT . '/superadmin');
                } else {
                    header('Location: ' . URL_ROOT . '/dashboard');
                }
                exit;
            } else {
                $error = 'Identifiants invalides.';
                $this->view('auth/login', ['error' => $error]);
                return;
            }
        }

        // Affichage du formulaire
        $this->view('auth/login');
    }

    // Déconnexion
    public function logout() {
        session_destroy();
        header('Location: ' . URL_ROOT . '/login');
        exit;
    }
}
?>

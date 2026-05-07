<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class AdminAdminsController extends Controller {

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
        $admins = $this->db->query(
            "SELECT id, nom, email, created_at FROM admins ORDER BY created_at ASC"
        )->resultSet();

        $this->view('admin/admins/index', [
            'admins'   => $admins,
            'adminNom' => $_SESSION['admin_nom'],
            'adminId'  => $_SESSION['admin_id'],
        ]);
    }

    // ── AJOUTER ────────────────────────────────────────────
    public function ajouter(): void {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom      = trim($_POST['nom'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm'] ?? '';

            if (empty($nom) || empty($email) || empty($password)) {
                $error = 'Nom, email et mot de passe sont obligatoires.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Adresse email invalide.';
            } elseif (strlen($password) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            } elseif ($password !== $confirm) {
                $error = 'Les mots de passe ne correspondent pas.';
            } else {
                $exists = $this->db->query(
                    "SELECT id FROM admins WHERE email = :email"
                )->bind(':email', $email)->single();

                if ($exists) {
                    $error = 'Cet email est déjà utilisé par un autre administrateur.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $this->db->query(
                        "INSERT INTO admins (nom, email, password) VALUES (:nom, :email, :password)"
                    )
                    ->bind(':nom', $nom)
                    ->bind(':email', $email)
                    ->bind(':password', $hash)
                    ->execute();

                    $_SESSION['flash_success'] = "Administrateur \"{$nom}\" créé avec succès.";
                    header('Location: ' . URL_ROOT . '/admin/admins');
                    exit;
                }
            }
        }

        $this->view('admin/admins/form', [
            'admin'    => null,
            'error'    => $error,
            'adminNom' => $_SESSION['admin_nom'],
        ]);
    }

    // ── MODIFIER ───────────────────────────────────────────
    public function modifier($id): void {
        $admin = $this->db->query(
            "SELECT id, nom, email FROM admins WHERE id = :id"
        )->bind(':id', $id)->single();

        if (!$admin) {
            header('Location: ' . URL_ROOT . '/admin/admins');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom      = trim($_POST['nom'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm'] ?? '';

            if (empty($nom) || empty($email)) {
                $error = 'Nom et email sont obligatoires.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Adresse email invalide.';
            } elseif (!empty($password) && strlen($password) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            } elseif (!empty($password) && $password !== $confirm) {
                $error = 'Les mots de passe ne correspondent pas.';
            } else {
                $exists = $this->db->query(
                    "SELECT id FROM admins WHERE email = :email AND id != :id"
                )->bind(':email', $email)->bind(':id', $id)->single();

                if ($exists) {
                    $error = 'Cet email est déjà utilisé par un autre administrateur.';
                } else {
                    if (!empty($password)) {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $this->db->query(
                            "UPDATE admins SET nom = :nom, email = :email, password = :password WHERE id = :id"
                        )
                        ->bind(':nom', $nom)
                        ->bind(':email', $email)
                        ->bind(':password', $hash)
                        ->bind(':id', $id)
                        ->execute();
                    } else {
                        $this->db->query(
                            "UPDATE admins SET nom = :nom, email = :email WHERE id = :id"
                        )
                        ->bind(':nom', $nom)
                        ->bind(':email', $email)
                        ->bind(':id', $id)
                        ->execute();
                    }

                    if ($_SESSION['admin_id'] == $id) {
                        $_SESSION['admin_nom'] = $nom;
                    }

                    $_SESSION['flash_success'] = "Administrateur mis à jour.";
                    header('Location: ' . URL_ROOT . '/admin/admins');
                    exit;
                }
            }
        }

        $this->view('admin/admins/form', [
            'admin'    => $admin,
            'error'    => $error,
            'adminNom' => $_SESSION['admin_nom'],
        ]);
    }

    // ── SUPPRIMER ──────────────────────────────────────────
    public function supprimer($id): void {
        if ($id == $_SESSION['admin_id']) {
            $_SESSION['flash_error'] = "Vous ne pouvez pas supprimer votre propre compte.";
            header('Location: ' . URL_ROOT . '/admin/admins');
            exit;
        }

        $nb = $this->db->query("SELECT COUNT(*) AS cnt FROM admins")->single()['cnt'];
        if ($nb <= 1) {
            $_SESSION['flash_error'] = "Impossible de supprimer le seul administrateur.";
            header('Location: ' . URL_ROOT . '/admin/admins');
            exit;
        }

        $this->db->query("DELETE FROM admins WHERE id = :id")->bind(':id', $id)->execute();
        $_SESSION['flash_success'] = "Administrateur supprimé.";
        header('Location: ' . URL_ROOT . '/admin/admins');
        exit;
    }
}

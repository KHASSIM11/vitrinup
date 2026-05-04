<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class AdminCategoriesController extends Controller {

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
        $categories = $this->db->query(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM produits p WHERE p.categorie_id = c.id) AS nb_produits
             FROM categories c
             ORDER BY c.ordre ASC, c.nom ASC"
        )->resultSet();

        $this->view('admin/categories/index', [
            'categories' => $categories,
            'adminNom'   => $_SESSION['admin_nom'],
        ]);
    }

    // ── AJOUTER ────────────────────────────────────────────
    public function ajouter(): void {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom   = trim($_POST['nom'] ?? '');
            $ordre = intval($_POST['ordre'] ?? 0);

            if (empty($nom)) {
                $error = 'Le nom de la catégorie est obligatoire.';
            } else {
                $slug = $this->slugify($nom);

                $this->db->query(
                    "INSERT INTO categories (nom, slug, ordre) VALUES (:nom, :slug, :ordre)"
                )
                ->bind(':nom', $nom)
                ->bind(':slug', $slug)
                ->bind(':ordre', $ordre)
                ->execute();

                header('Location: ' . URL_ROOT . '/admin/categories');
                exit;
            }
        }

        $this->view('admin/categories/form', [
            'categorie' => null,
            'error'     => $error,
            'adminNom'  => $_SESSION['admin_nom'],
        ]);
    }

    // ── MODIFIER ───────────────────────────────────────────
    public function modifier($id): void {
        $categorie = $this->db->query("SELECT * FROM categories WHERE id = :id")
                              ->bind(':id', $id)->single();

        if (!$categorie) {
            header('Location: ' . URL_ROOT . '/admin/categories');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom   = trim($_POST['nom'] ?? '');
            $ordre = intval($_POST['ordre'] ?? 0);

            if (empty($nom)) {
                $error = 'Le nom de la catégorie est obligatoire.';
            } else {
                $slug = $this->slugify($nom);

                $this->db->query(
                    "UPDATE categories SET nom = :nom, slug = :slug, ordre = :ordre WHERE id = :id"
                )
                ->bind(':nom', $nom)
                ->bind(':slug', $slug)
                ->bind(':ordre', $ordre)
                ->bind(':id', $id)
                ->execute();

                header('Location: ' . URL_ROOT . '/admin/categories');
                exit;
            }
        }

        $this->view('admin/categories/form', [
            'categorie' => $categorie,
            'error'     => $error,
            'adminNom'  => $_SESSION['admin_nom'],
        ]);
    }

    // ── SUPPRIMER ──────────────────────────────────────────
    public function supprimer($id): void {
        // Vérifier si des produits sont liés à cette catégorie
        $nbProduits = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM produits WHERE categorie_id = :id"
        )->bind(':id', $id)->single()['cnt'];

        if ($nbProduits > 0) {
            $_SESSION['flash_error'] = "Impossible de supprimer : $nbProduits produit(s) sont liés à cette catégorie.";
            header('Location: ' . URL_ROOT . '/admin/categories');
            exit;
        }

        $this->db->query("DELETE FROM categories WHERE id = :id")->bind(':id', $id)->execute();

        header('Location: ' . URL_ROOT . '/admin/categories');
        exit;
    }

    // ── HELPERS ────────────────────────────────────────────
    private function slugify(string $text): string {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text) ?: 'n-a';
    }
}

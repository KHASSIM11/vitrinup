<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class AdminProduitsController extends Controller {

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
        $page   = max(1, intval($_GET['page'] ?? 1));
        $limite = 15;
        $offset = ($page - 1) * $limite;

        $total = $this->db->query("SELECT COUNT(*) AS cnt FROM produits")->single()['cnt'];
        $totalPages = max(1, ceil($total / $limite));

        $produits = $this->db->query(
            "SELECT p.*, c.nom AS categorie_nom,
                    (SELECT chemin FROM images_produits WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image
             FROM produits p
             LEFT JOIN categories c ON p.categorie_id = c.id
             ORDER BY p.created_at DESC
             LIMIT $limite OFFSET $offset"
        )->resultSet();

        $this->view('admin/produits/index', [
            'produits'   => $produits,
            'page'       => $page,
            'totalPages' => $totalPages,
            'total'      => $total,
            'adminNom'   => $_SESSION['admin_nom'],
        ]);
    }

    // ── AJOUTER ────────────────────────────────────────────
    public function ajouter(): void {
        $categories = $this->db->query("SELECT * FROM categories ORDER BY ordre ASC")->resultSet();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom         = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $prix        = floatval($_POST['prix'] ?? 0);
            $prix_promo  = !empty($_POST['prix_promo']) ? floatval($_POST['prix_promo']) : null;
            $categorie_id = intval($_POST['categorie_id'] ?? 0);
            $genre       = $_POST['genre'] ?? 'mixte';
            $marque      = trim($_POST['marque'] ?? '');
            $statut      = $_POST['statut'] ?? 'actif';

            if (empty($nom) || $prix <= 0 || $categorie_id === 0) {
                $error = 'Nom, prix et catégorie sont obligatoires.';
            } else {
                $slug = $this->slugify($nom);

                // Insert produit
                $this->db->query(
                    "INSERT INTO produits (nom, slug, description, prix, prix_promo, categorie_id, genre, marque, statut)
                     VALUES (:nom, :slug, :description, :prix, :prix_promo, :categorie_id, :genre, :marque, :statut)"
                )
                ->bind(':nom', $nom)
                ->bind(':slug', $slug)
                ->bind(':description', $description)
                ->bind(':prix', $prix)
                ->bind(':prix_promo', $prix_promo)
                ->bind(':categorie_id', $categorie_id)
                ->bind(':genre', $genre)
                ->bind(':marque', $marque)
                ->bind(':statut', $statut)
                ->execute();

                $produitId = $this->db->lastInsertId();

                // Upload images
                $this->uploadImages($produitId);

                // Tailles
                $this->saveTailles($produitId);

                header('Location: ' . URL_ROOT . '/admin/produits');
                exit;
            }
        }

        $this->view('admin/produits/form', [
            'produit'    => null,
            'categories' => $categories,
            'tailles'    => [],
            'images'     => [],
            'error'      => $error,
            'adminNom'   => $_SESSION['admin_nom'],
        ]);
    }

    // ── MODIFIER ───────────────────────────────────────────
    public function modifier($id): void {
        $categories = $this->db->query("SELECT * FROM categories ORDER BY ordre ASC")->resultSet();
        $produit = $this->db->query("SELECT * FROM produits WHERE id = :id")->bind(':id', $id)->single();

        if (!$produit) {
            header('Location: ' . URL_ROOT . '/admin/produits');
            exit;
        }

        $images  = $this->db->query("SELECT * FROM images_produits WHERE produit_id = :id ORDER BY ordre ASC")->bind(':id', $id)->resultSet();
        $tailles = $this->db->query("SELECT * FROM tailles_produits WHERE produit_id = :id ORDER BY taille ASC")->bind(':id', $id)->resultSet();
        $error   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom          = trim($_POST['nom'] ?? '');
            $description  = trim($_POST['description'] ?? '');
            $prix         = floatval($_POST['prix'] ?? 0);
            $prix_promo   = !empty($_POST['prix_promo']) ? floatval($_POST['prix_promo']) : null;
            $categorie_id = intval($_POST['categorie_id'] ?? 0);
            $genre        = $_POST['genre'] ?? 'mixte';
            $marque       = trim($_POST['marque'] ?? '');
            $statut       = $_POST['statut'] ?? 'actif';

            if (empty($nom) || $prix <= 0 || $categorie_id === 0) {
                $error = 'Nom, prix et catégorie sont obligatoires.';
            } else {
                $slug = $this->slugify($nom);

                $this->db->query(
                    "UPDATE produits SET nom=:nom, slug=:slug, description=:description, prix=:prix,
                     prix_promo=:prix_promo, categorie_id=:categorie_id, genre=:genre, marque=:marque, statut=:statut
                     WHERE id=:id"
                )
                ->bind(':nom', $nom)->bind(':slug', $slug)->bind(':description', $description)
                ->bind(':prix', $prix)->bind(':prix_promo', $prix_promo)->bind(':categorie_id', $categorie_id)
                ->bind(':genre', $genre)->bind(':marque', $marque)->bind(':statut', $statut)->bind(':id', $id)
                ->execute();

                // Nouvelles images si uploadées
                if (!empty($_FILES['images']['name'][0])) {
                    $this->uploadImages($id);
                }

                // Tailles
                $this->db->query("DELETE FROM tailles_produits WHERE produit_id = :id")->bind(':id', $id)->execute();
                $this->saveTailles($id);

                header('Location: ' . URL_ROOT . '/admin/produits');
                exit;
            }
        }

        $this->view('admin/produits/form', [
            'produit'    => $produit,
            'images'     => $images,
            'categories' => $categories,
            'tailles'    => $tailles,
            'error'      => $error,
            'adminNom'   => $_SESSION['admin_nom'],
        ]);
    }

    // ── SUPPRIMER ──────────────────────────────────────────
    public function supprimer($id): void {
        // Supprime les fichiers images
        $images = $this->db->query("SELECT chemin FROM images_produits WHERE produit_id = :id")->bind(':id', $id)->resultSet();
        foreach ($images as $img) {
            $path = UPLOAD_DIR . $img['chemin'];
            if (file_exists($path)) unlink($path);
        }

        $this->db->query("DELETE FROM produits WHERE id = :id")->bind(':id', $id)->execute();

        header('Location: ' . URL_ROOT . '/admin/produits');
        exit;
    }

    // ── SUPPRIMER IMAGE ────────────────────────────────────
    public function supprimerImage(): void {
        $imageId   = intval($_POST['image_id'] ?? 0);
        $produitId = intval($_POST['produit_id'] ?? 0);

        $img = $this->db->query("SELECT * FROM images_produits WHERE id = :id")->bind(':id', $imageId)->single();
        if ($img) {
            $path = UPLOAD_DIR . $img['chemin'];
            if (file_exists($path)) unlink($path);
            $this->db->query("DELETE FROM images_produits WHERE id = :id")->bind(':id', $imageId)->execute();
        }

        header('Location: ' . URL_ROOT . '/admin/produits/modifier/' . $produitId);
        exit;
    }

    // ── HELPERS ────────────────────────────────────────────
    private function uploadImages(int $produitId): void {
        if (empty($_FILES['images']['name'][0])) return;

        $uploadDir = UPLOAD_DIR . $produitId . '/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $isPremiere = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM images_produits WHERE produit_id = :id"
        )->bind(':id', $produitId)->single()['cnt'] === 0;

        $ordre = 0;
        foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;

            $ext      = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
            $allowed  = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowed)) continue;

            if ($_FILES['images']['size'][$i] > 2 * 1024 * 1024) continue; // max 2MB

            $filename = uniqid('img_') . '.' . $ext;
            $dest     = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $dest)) {
                $chemin       = $produitId . '/' . $filename;
                $estPrincipale = ($isPremiere && $ordre === 0) ? 1 : 0;

                $this->db->query(
                    "INSERT INTO images_produits (produit_id, chemin, est_principale, ordre)
                     VALUES (:produit_id, :chemin, :est_principale, :ordre)"
                )
                ->bind(':produit_id', $produitId)
                ->bind(':chemin', $chemin)
                ->bind(':est_principale', $estPrincipale)
                ->bind(':ordre', $ordre)
                ->execute();

                $ordre++;
            }
        }
    }

    private function saveTailles(int $produitId): void {
        $tailles = $_POST['tailles'] ?? [];
        $stocks  = $_POST['stocks']  ?? [];

        foreach ($tailles as $i => $taille) {
            $taille = trim($taille);
            $stock  = intval($stocks[$i] ?? 0);
            if (empty($taille)) continue;

            $this->db->query(
                "INSERT INTO tailles_produits (produit_id, taille, stock) VALUES (:produit_id, :taille, :stock)"
            )
            ->bind(':produit_id', $produitId)
            ->bind(':taille', $taille)
            ->bind(':stock', $stock)
            ->execute();
        }
    }

    private function slugify(string $text): string {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text) ?: 'n-a';
    }
}
?>

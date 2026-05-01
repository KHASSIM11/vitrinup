<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class InscriptionController extends Controller {

    /**
     * Point d'entrée du contrôleur.
     * Affiche le formulaire ou traite la soumission.
     */
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
        } else {
            // Affiche le formulaire d'inscription
            $this->view('inscription/index');
        }
    }

    /**
     * Traite la requête POST : création de la boutique et de l'utilisateur admin.
     */
    private function handlePost() {
        // Récupération et nettoyage des données
        $nomBoutique = trim($_POST['nom_boutique'] ?? '');
        $whatsapp    = trim($_POST['whatsapp'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $password    = $_POST['password'] ?? '';
        $ville       = trim($_POST['ville'] ?? '');

        // Validation minimale
        if (empty($nomBoutique) || empty($email) || empty($password) || empty($ville)) {
            echo 'Tous les champs obligatoires doivent être remplis.';
            return;
        }

        // Génération du slug (peut être fourni par le formulaire ou généré ici)
        $slug = $this->slugify($nomBoutique);

        // Hash du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $db = new Database();

        try {
            // ---------- Insertion de la boutique ----------
            $db->query('INSERT INTO boutiques (nom, slug, whatsapp, email, ville, statut) VALUES (:nom, :slug, :whatsapp, :email, :ville, :statut)')
               ->bind(':nom', $nomBoutique)
               ->bind(':slug', $slug)
               ->bind(':whatsapp', $whatsapp)
               ->bind(':email', $email)
               ->bind(':ville', $ville)
               ->bind(':statut', 'active')
               ->execute();

            $boutiqueId = $db->lastInsertId();

            // ---------- Insertion de l'utilisateur admin ----------
            $db->query('INSERT INTO utilisateurs (boutique_id, nom, email, password, role) VALUES (:boutique_id, :nom, :email, :password, :role)')
               ->bind(':boutique_id', $boutiqueId)
               ->bind(':nom', $nomBoutique)   // on utilise le même nom pour l'admin
               ->bind(':email', $email)
               ->bind(':password', $hashedPassword)
               ->bind(':role', 'admin')
               ->execute();

            // Redirection vers le tableau de bord après succès
            header('Location: ' . URL_ROOT . '/dashboard');
            exit;
        } catch (Exception $e) {
            // En production, il vaudrait logger l'erreur plutôt que l'afficher.
            echo 'Erreur lors de l\'inscription : ' . $e->getMessage();
        }
    }

    /**
     * Transforme un texte en slug URL‑friendly.
     */
    private function slugify(string $text): string {
        // Remplace les caractères non alphanumériques par des tirets
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Translittère les caractères Unicode en ASCII
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Supprime les caractères indésirables
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Supprime les tirets en trop
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        // Met en minuscules
        $text = strtolower($text);
        return $text ?: 'n-a';
    }
}
?>

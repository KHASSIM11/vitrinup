<?php
// Ce contrôleur teste la connexion à la base de données.
// Il hérite de la classe de base Controller.

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';

class TestController extends Controller {
    /**
     * Méthode d'entrée du contrôleur.
     * Elle crée une instance de Database et exécute une requête minimale.
     * Si tout se passe bien, elle affiche "Connexion DB OK".
     */
    public function index() {
        try {
            $db = new Database();

            // Requête très simple pour vérifier la connexion.
            $db->query('SELECT 1')->execute();

            echo 'Connexion DB OK';
        } catch (Exception $e) {
            // En cas d'erreur, on affiche le message d'exception.
            echo 'Erreur de connexion DB : ' . $e->getMessage();
        }
    }
}
?>

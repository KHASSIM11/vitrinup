<?php

class User extends Model { // Assurez-vous d'avoir une classe Model de base si nécessaire, sinon étendez Controller ou rien
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Exemple de méthode pour récupérer un utilisateur par email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Vérifie si un utilisateur a été trouvé
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // Ajoutez ici d'autres méthodes pour interagir avec la table des utilisateurs (inscription, connexion, etc.)
}
?>

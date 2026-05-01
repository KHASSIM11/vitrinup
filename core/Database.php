<?php

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh; // Database handler
    private $stmt;
    private $error;

    public function __construct() {
        // Set DSN (Data Source Name)
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Mode d'erreur pour lancer des exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mode de récupération par défaut : tableau associatif
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Désactive l'émulation des prepared statements
        ];

        // Crée une instance PDO
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // En cas d'erreur de connexion, affiche un message et arrête le script
            $this->error = $e->getMessage();
            echo $this->error; // En production, loggez cette erreur au lieu de l'afficher
            die();
        }
    }

    // Prépare la requête SQL
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
        return $this; // Permet le chaînage des méthodes
    }

    // Lie une valeur à un placeholder
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
        return $this; // Permet le chaînage des méthodes
    }

    // Exécute la requête préparée
    public function execute() {
        return $this->stmt->execute();
    }

    // Récupère un ensemble de résultats (plusieurs lignes)
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère une seule ligne de résultat
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupère le nombre de lignes affectées par la dernière requête
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    // Récupère l'ID de la dernière insertion
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }

    // Gère les erreurs PDO (peut être étendu pour un logging plus poussé)
    public function getError() {
        return $this->error;
    }
}
?>

<?php

class Controller {
    // Charge le modèle
    protected function model($model) {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }

    // Charge la vue
    protected function view($view, $data = []) {
        // Vérifie si le fichier de vue existe
        if (file_exists(__DIR__ . '/../views/' . $view . '.php')) {
            // Expose les variables du tableau $data dans la vue
            extract($data);
            require_once __DIR__ . '/../views/' . $view . '.php';
        } else {
            // Gérer l'erreur si la vue n'existe pas
            die('La vue ' . $view . ' n\'existe pas.');
        }
    }
}
?>

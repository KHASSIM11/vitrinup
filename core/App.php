<?php

class App {
    protected $controller = 'HomeController'; // Contrôleur par défaut
    protected $method = 'index'; // Méthode par défaut
    protected $params = []; // Paramètres passés à la méthode

    public function __construct() {
        $url = $this->parseUrl();

        // -----------------------------------------------------------------
        // Routage spécial : inscription → InscriptionController
        // -----------------------------------------------------------------
        if (isset($url[0]) && $url[0] === 'inscription') {
            $this->controller = 'InscriptionController';
            unset($url[0]);
        }
        // -----------------------------------------------------------------
        // Routage spécial : superadmin → SuperAdminController
        // -----------------------------------------------------------------
        elseif (isset($url[0]) && $url[0] === 'superadmin') {
            $this->controller = 'SuperAdminController';
            unset($url[0]);
        }
        // -----------------------------------------------------------------
        // Routage générique : recherche d'un fichier de contrôleur correspondant
        // -----------------------------------------------------------------
        elseif (isset($url[0]) && file_exists(__DIR__ . '/../controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        } else {
            // Si le contrôleur n'existe pas, on utilise le contrôleur par défaut (HomeController)
            // Vous pouvez implémenter une page 404 ici si besoin.
        }

        // Instancie le contrôleur
        require_once __DIR__ . '/../controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Vérifie si la méthode existe dans le contrôleur
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            } else {
                // Méthode non trouvée → vous pouvez rediriger vers une page 404 ici.
            }
        }

        // Récupère les paramètres restants
        $this->params = $url ? array_values($url) : [];

        // Appelle la méthode du contrôleur avec les paramètres
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            // Nettoie l'URL et la sépare en tableau
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return []; // Retourne un tableau vide si pas d'URL spécifiée
    }

    public function run() {
        // L'instanciation du contrôleur et l'appel de la méthode se font dans le constructeur
        // Cette méthode peut être utilisée pour d'autres initialisations globales si nécessaire
    }
}
?>

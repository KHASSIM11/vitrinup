<?php

class App {
    protected $controller = 'HomeController'; // Contrôleur par défaut
    protected $method = 'index'; // Méthode par défaut
    protected $params = []; // Paramètres passés à la méthode

    public function __construct() {
        $url = $this->parseUrl();

        // Vérifie si le fichier du contrôleur existe
        if (isset($url[0]) && file_exists(__DIR__ . '/../controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        } else {
            // Si le contrôleur n'existe pas, on peut rediriger vers une page 404 ou utiliser un contrôleur par défaut
            // Pour l'instant, on utilise le contrôleur par défaut
            // require_once __DIR__ . '/../controllers/ErrorController.php';
            // $this->controller = new ErrorController();
            // $this->method = 'notFound';
            // return; // Arrête l'exécution ici si on gère une erreur 404
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
                // Méthode non trouvée, on peut gérer une erreur 404
                // require_once __DIR__ . '/../controllers/ErrorController.php';
                // $this->controller = new ErrorController();
                // $this->method = 'notFound';
                // return;
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

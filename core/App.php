<?php

class App {

    protected $controller = 'HomeController';
    protected $method     = 'index';
    protected $params     = [];

    public function __construct() {
        $url = $this->parseUrl();
        $segment = $url[0] ?? '';

        // ----------------------------------------------------------------
        // ROUTES PUBLIQUES
        // ----------------------------------------------------------------
        if ($segment === '') {
            $this->controller = 'HomeController';

        } elseif ($segment === 'catalogue') {
            $this->controller = 'CatalogueController';

        } elseif ($segment === 'produit') {
            $this->controller = 'ProduitController';
            // /produit/{slug}
            if (!empty($url[1])) {
                $this->method  = 'show';
                $this->params  = [$url[1]];
            }

        } elseif ($segment === 'contact') {
            $this->controller = 'ContactController';

        // ----------------------------------------------------------------
        // ROUTES PANIER
        // ----------------------------------------------------------------
        } elseif ($segment === 'recherche') {
            $this->controller = 'RechercheController';
            $action = $url[1] ?? 'index';
            
            if ($action === 'suggest') {
                $this->method = 'suggest';
            }

        } elseif ($segment === 'panier') {
            $this->controller = 'PanierController';
            $action = $url[1] ?? 'index';
            
            switch ($action) {
                case 'ajouter':
                    $this->method = 'ajouter';
                    break;
                case 'modifier':
                    $this->method = 'modifier';
                    break;
                case 'supprimer':
                    $this->method = 'supprimer';
                    break;
                case 'vider':
                    $this->method = 'vider';
                    break;
                case 'commander':
                    $this->method = 'commander';
                    break;
                case 'count':
                    $this->method = 'count';
                    break;
                default:
                    $this->method = 'index';
                    break;
            }

        // ----------------------------------------------------------------
        // ROUTES ADMIN
        // ----------------------------------------------------------------
        } elseif ($segment === 'admin') {
            $sousPage = $url[1] ?? 'index';

            switch ($sousPage) {
                case 'login':
                    $this->controller = 'AdminAuthController';
                    $this->method     = 'login';
                    break;

                case 'logout':
                    $this->controller = 'AdminAuthController';
                    $this->method     = 'logout';
                    break;

                case 'produits':
                    $this->controller = 'AdminProduitsController';
                    $action = $url[2] ?? 'index';
                    if (in_array($action, ['index', 'ajouter', 'modifier', 'supprimer', 'supprimerImage'])) {
                        $this->method = $action;
                        $this->params = isset($url[3]) ? [$url[3]] : [];
                    }
                    break;

                case 'categories':
                    $this->controller = 'AdminCategoriesController';
                    $this->method     = $url[2] ?? 'index';
                    $this->params     = isset($url[3]) ? [$url[3]] : [];
                    break;

                case 'stocks':
                    $this->controller = 'AdminStocksController';
                    $action = $url[2] ?? 'index';
                    if (in_array($action, ['index', 'entree', 'sortie', 'historique', 'modifierStock', 'ajouterTaille', 'supprimerTaille', 'ajouterEntree', 'ajouterSortie'])) {
                        $this->method = $action;
                        $this->params = isset($url[3]) ? [$url[3]] : [];
                    }
                    break;

                case 'commandes':
                    $this->controller = 'AdminCommandesController';
                    $this->method     = $url[2] ?? 'index';
                    $this->params     = isset($url[3]) ? [$url[3]] : [];
                    break;

                default:
                    // /admin → dashboard
                    $this->controller = 'AdminController';
                    $this->method     = 'index';
                    break;
            }

        // ----------------------------------------------------------------
        // 404 — route inconnue
        // ----------------------------------------------------------------
        } else {
            $this->controller = 'HomeController';
            $this->method     = 'notFound';
        }

        // Charge et instancie le contrôleur
        $controllerFile = __DIR__ . '/../controllers/' . $this->controller . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $this->controller = new $this->controller;
        } else {
            die('Contrôleur introuvable : ' . $this->controller);
        }

        // Appelle la méthode avec les paramètres
        if (method_exists($this->controller, $this->method)) {
            call_user_func_array([$this->controller, $this->method], $this->params);
        } else {
            die('Méthode introuvable : ' . $this->method);
        }
    }

    public function parseUrl(): array {
        if (isset($_GET['url'])) {
            $url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [''];
    }

    public function run(): void {
        // Déjà géré dans le constructeur
    }
}
?>

<?php

class HomeController extends Controller {
    public function index() {
        $data = [
            'title' => 'Bienvenue sur Vitrinup',
            'description' => 'Votre plateforme pour créer une vitrine en ligne pour votre boutique de chaussures au Maroc.'
        ];
        $this->view('home/index', $data);
    }
}
?>

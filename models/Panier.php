<?php
/**
 * Modèle Panier - Gestion du panier en session
 */
class Panier {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }
    }
    
    /**
     * Ajoute un produit au panier
     */
    public function ajouter(int $produitId, string $nom, float $prix, ?float $prixPromo, string $image, string $taille, int $quantite = 1): void {
        $cle = $produitId . '_' . $taille;
        
        $prixFinal = $prixPromo > 0 ? $prixPromo : $prix;
        
        if (isset($_SESSION['panier'][$cle])) {
            $_SESSION['panier'][$cle]['quantite'] += $quantite;
        } else {
            $_SESSION['panier'][$cle] = [
                'produit_id' => $produitId,
                'nom' => $nom,
                'prix' => $prixFinal,
                'prix_original' => $prix,
                'prix_promo' => $prixPromo,
                'image' => $image,
                'taille' => $taille,
                'quantite' => $quantite
            ];
        }
    }
    
    /**
     * Modifie la quantité d'un article
     */
    public function modifierQuantite(string $cle, int $quantite): void {
        if ($quantite <= 0) {
            $this->supprimer($cle);
        } elseif (isset($_SESSION['panier'][$cle])) {
            $_SESSION['panier'][$cle]['quantite'] = $quantite;
        }
    }
    
    /**
     * Supprime un article du panier
     */
    public function supprimer(string $cle): void {
        unset($_SESSION['panier'][$cle]);
    }
    
    /**
     * Vide complètement le panier
     */
    public function vider(): void {
        $_SESSION['panier'] = [];
    }
    
    /**
     * Récupère tous les articles du panier
     */
    public function getArticles(): array {
        return $_SESSION['panier'] ?? [];
    }
    
    /**
     * Compte le nombre total d'articles
     */
    public function getNombreArticles(): int {
        $total = 0;
        foreach ($_SESSION['panier'] as $article) {
            $total += $article['quantite'];
        }
        return $total;
    }
    
    /**
     * Calcule le total du panier
     */
    public function getTotal(): float {
        $total = 0;
        foreach ($_SESSION['panier'] as $article) {
            $total += $article['prix'] * $article['quantite'];
        }
        return $total;
    }
    
    /**
     * Calcule l'économie totale (promotions)
     */
    public function getEconomie(): float {
        $economie = 0;
        foreach ($_SESSION['panier'] as $article) {
            if ($article['prix_promo'] > 0) {
                $economie += ($article['prix_original'] - $article['prix_promo']) * $article['quantite'];
            }
        }
        return $economie;
    }
    
    /**
     * Vérifie si le panier est vide
     */
    public function estVide(): bool {
        return empty($_SESSION['panier']);
    }
}

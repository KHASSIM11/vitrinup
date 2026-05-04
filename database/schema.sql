-- ============================================================
-- Structure de la base de données — Vitrinup
-- Basé sur le dump u640824467_vitrinup.sql
-- ============================================================

-- Table des admins
CREATE TABLE IF NOT EXISTS `admins` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom` varchar(100) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des catégories
CREATE TABLE IF NOT EXISTS `categories` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL,
    `ordre` int(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des produits
CREATE TABLE IF NOT EXISTS `produits` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `prix` decimal(10,2) NOT NULL,
    `prix_promo` decimal(10,2) DEFAULT NULL,
    `categorie_id` int(10) UNSIGNED NOT NULL,
    `genre` enum('homme','femme','enfant','mixte') NOT NULL DEFAULT 'mixte',
    `marque` varchar(100) DEFAULT NULL,
    `statut` enum('actif','inactif') NOT NULL DEFAULT 'actif',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `idx_categorie` (`categorie_id`),
    KEY `idx_statut` (`statut`),
    CONSTRAINT `fk_produit_categorie` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des images produits
CREATE TABLE IF NOT EXISTS `images_produits` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `produit_id` int(10) UNSIGNED NOT NULL,
    `chemin` varchar(255) NOT NULL,
    `est_principale` tinyint(1) NOT NULL DEFAULT 0,
    `ordre` int(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_produit_id` (`produit_id`),
    CONSTRAINT `fk_image_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des tailles / stock
CREATE TABLE IF NOT EXISTS `tailles_produits` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `produit_id` int(10) UNSIGNED NOT NULL,
    `taille` varchar(20) NOT NULL,
    `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_taille` (`produit_id`,`taille`),
    CONSTRAINT `fk_taille_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des commandes
CREATE TABLE IF NOT EXISTS `commandes` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `produit_id` int(10) UNSIGNED NOT NULL,
    `client_nom` varchar(255) DEFAULT NULL,
    `client_tel` varchar(30) DEFAULT NULL,
    `taille` varchar(20) DEFAULT NULL,
    `message` text DEFAULT NULL,
    `statut` enum('nouveau','vu','confirme','annule') NOT NULL DEFAULT 'nouveau',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `fk_commande_produit` (`produit_id`),
    CONSTRAINT `fk_commande_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin par défaut (mot de passe : admin123)
INSERT IGNORE INTO `admins` (`id`, `nom`, `email`, `password`, `created_at`)
VALUES (1, 'Admin', 'admin@boutique.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW());

-- --------------------------------------------------------
-- Schéma complet de la base de données Vitrinup
-- --------------------------------------------------------
-- Utilisez InnoDB pour le support des clés étrangères
-- --------------------------------------------------------

-- 1. Table `boutiques`
CREATE TABLE IF NOT EXISTS `boutiques` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom`           VARCHAR(255) NOT NULL,
    `slug`          VARCHAR(255) NOT NULL UNIQUE,
    `description`   TEXT NULL,
    `logo`          VARCHAR(255) NULL,
    `whatsapp`      VARCHAR(20)  NULL,
    `email`        VARCHAR(255) NULL,
    `adresse`       VARCHAR(255) NULL,
    `ville`         VARCHAR(100) NULL,
    `statut`        ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_boutiques_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table `produits`
CREATE TABLE IF NOT EXISTS `produits` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `boutique_id`   INT UNSIGNED NOT NULL,
    `nom`           VARCHAR(255) NOT NULL,
    `description`   TEXT NULL,
    `prix`          DECIMAL(10,2) NOT NULL,
    `taille`        VARCHAR(50) NULL,
    `couleur`       VARCHAR(50) NULL,
    `stock`         INT UNSIGNED NOT NULL DEFAULT 0,
    `image`         VARCHAR(255) NULL,
    `statut`        ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_produits_boutique_id` (`boutique_id`),
    CONSTRAINT `fk_produits_boutique`
        FOREIGN KEY (`boutique_id`) REFERENCES `boutiques`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table `utilisateurs`
CREATE TABLE IF NOT EXISTS `utilisateurs` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `boutique_id`   INT UNSIGNED NULL,
    `nom`           VARCHAR(255) NOT NULL,
    `email`         VARCHAR(255) NOT NULL UNIQUE,
    `password`      VARCHAR(255) NOT NULL,
    `role`          ENUM('admin','manager','staff') NOT NULL DEFAULT 'staff',
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_utilisateurs_boutique_id` (`boutique_id`),
    CONSTRAINT `fk_utilisateurs_boutique`
        FOREIGN KEY (`boutique_id`) REFERENCES `boutiques`(`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table `commandes`
CREATE TABLE IF NOT EXISTS `commandes` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `boutique_id`   INT UNSIGNED NOT NULL,
    `client_nom`    VARCHAR(255) NOT NULL,
    `client_tel`    VARCHAR(20)  NOT NULL,
    `produit_id`    INT UNSIGNED NOT NULL,
    `quantite`      INT UNSIGNED NOT NULL,
    `statut`        ENUM('pending','processing','completed','canceled') NOT NULL DEFAULT 'pending',
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_commandes_boutique_id` (`boutique_id`),
    INDEX `idx_commandes_produit_id` (`produit_id`),
    CONSTRAINT `fk_commandes_boutique`
        FOREIGN KEY (`boutique_id`) REFERENCES `boutiques`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_commandes_produit`
        FOREIGN KEY (`produit_id`) REFERENCES `produits`(`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Fin du schéma
-- --------------------------------------------------------

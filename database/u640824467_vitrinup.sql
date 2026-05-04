-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 04 mai 2026 à 23:36
-- Version du serveur : 11.8.6-MariaDB-log
-- Version de PHP : 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `u640824467_vitrinup`
--

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `nom`, `email`, `password`, `created_at`) VALUES
(1, 'Admin', 'admin@boutique.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-05-02 15:47:53');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `ordre` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `slug`, `ordre`) VALUES
(1, 'Homme', 'homme', 1),
(2, 'Femme', 'femme', 2),
(3, 'Enfant', 'enfant', 3),
(4, 'Sport', 'sport', 4),
(5, 'Soldes', 'soldes', 5);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(10) UNSIGNED NOT NULL,
  `produit_id` int(10) UNSIGNED NOT NULL,
  `client_nom` varchar(255) DEFAULT NULL,
  `client_tel` varchar(30) DEFAULT NULL,
  `taille` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `statut` enum('nouveau','vu','confirme','annule') NOT NULL DEFAULT 'nouveau',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `images_produits`
--

CREATE TABLE `images_produits` (
  `id` int(10) UNSIGNED NOT NULL,
  `produit_id` int(10) UNSIGNED NOT NULL,
  `chemin` varchar(255) NOT NULL,
  `est_principale` tinyint(1) NOT NULL DEFAULT 0,
  `ordre` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `images_produits`
--

INSERT INTO `images_produits` (`id`, `produit_id`, `chemin`, `est_principale`, `ordre`) VALUES
(1, 4, '4/img_69f72f4408230.webp', 1, 0),
(2, 1, '1/img_69f72f9aee9e1.webp', 1, 0),
(3, 2, '2/img_69f730da84ebe.webp', 1, 0),
(4, 3, '3/img_69f7310fbceb0.webp', 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `prix_promo` decimal(10,2) DEFAULT NULL,
  `categorie_id` int(10) UNSIGNED NOT NULL,
  `genre` enum('homme','femme','enfant','mixte') NOT NULL DEFAULT 'mixte',
  `marque` varchar(100) DEFAULT NULL,
  `statut` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `slug`, `description`, `prix`, `prix_promo`, `categorie_id`, `genre`, `marque`, `statut`, `created_at`) VALUES
(1, 'Nike Air Max 270', 'nike-air-max-270', 'Chaussure running confortable', 599.00, NULL, 1, 'homme', '', 'actif', '2026-05-02 16:48:58'),
(2, 'Adidas Stan Smith', 'adidas-stan-smith', 'Classique intemporel', 450.00, NULL, 2, 'femme', '', 'actif', '2026-05-02 16:48:58'),
(3, 'Puma RS-X', 'puma-rs-x', 'Style urbain moderne', 520.00, NULL, 1, 'homme', '', 'actif', '2026-05-02 16:48:58'),
(4, 'Adidas 360', 'adidas-360', '', 500.00, NULL, 1, 'homme', 'Adidas', 'actif', '2026-05-02 17:56:17');

-- --------------------------------------------------------

--
-- Structure de la table `tailles_produits`
--

CREATE TABLE `tailles_produits` (
  `id` int(10) UNSIGNED NOT NULL,
  `produit_id` int(10) UNSIGNED NOT NULL,
  `taille` varchar(20) NOT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tailles_produits`
--

INSERT INTO `tailles_produits` (`id`, `produit_id`, `taille`, `stock`) VALUES
(1, 4, '42', 10),
(2, 4, '41', 12),
(3, 4, '40', 5),
(4, 1, '42', 10),
(5, 1, '40', 10),
(6, 1, '41', 12),
(7, 2, '42', 10),
(8, 2, '41', 10),
(9, 2, '40', 11),
(10, 3, '42', 10),
(11, 3, '41', 10),
(12, 3, '40', 10);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_commande_produit` (`produit_id`);

--
-- Index pour la table `images_produits`
--
ALTER TABLE `images_produits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_produit_id` (`produit_id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_categorie` (`categorie_id`),
  ADD KEY `idx_statut` (`statut`);

--
-- Index pour la table `tailles_produits`
--
ALTER TABLE `tailles_produits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_taille` (`produit_id`,`taille`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `images_produits`
--
ALTER TABLE `images_produits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `tailles_produits`
--
ALTER TABLE `tailles_produits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `fk_commande_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `images_produits`
--
ALTER TABLE `images_produits`
  ADD CONSTRAINT `fk_image_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `fk_produit_categorie` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `tailles_produits`
--
ALTER TABLE `tailles_produits`
  ADD CONSTRAINT `fk_taille_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

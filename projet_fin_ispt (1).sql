-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  Dim 22 mai 2022 à 17:01
-- Version du serveur :  10.1.37-MariaDB
-- Version de PHP :  7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `projet_fin_ispt`
--

-- --------------------------------------------------------

--
-- Structure de la table `affectation_frais`
--

CREATE TABLE `affectation_frais` (
  `id` int(11) NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `id_frais` int(11) NOT NULL,
  `promotion` varchar(20) NOT NULL,
  `id_section` int(11) NOT NULL,
  `id_departement` int(11) NOT NULL,
  `id_option` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `affectation_frais`
--

INSERT INTO `affectation_frais` (`id`, `matricule`, `id_frais`, `promotion`, `id_section`, `id_departement`, `id_option`, `id_annee`) VALUES
(3, '2285', 1, 'G3', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `annee_acad`
--

CREATE TABLE `annee_acad` (
  `id_annee` int(11) NOT NULL,
  `annee_acad` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `annee_acad`
--

INSERT INTO `annee_acad` (`id_annee`, `annee_acad`) VALUES
(1, '2021-2022');

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

CREATE TABLE `departement` (
  `id_departement` int(11) NOT NULL,
  `departement` varchar(200) NOT NULL,
  `id_section` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `departement`
--

INSERT INTO `departement` (`id_departement`, `departement`, `id_section`, `id_annee`) VALUES
(1, 'Informatique', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `depense_facultaire`
--

CREATE TABLE `depense_facultaire` (
  `id_pdf` int(11) NOT NULL,
  `poste` varchar(200) NOT NULL,
  `montant` float NOT NULL,
  `depense` float DEFAULT '0',
  `id_section` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `depense_facultaire`
--

INSERT INTO `depense_facultaire` (`id_pdf`, `poste`, `montant`, `depense`, `id_section`, `id_annee`) VALUES
(3, 'communication', 12000, 0, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `etudiants_inscrits`
--

CREATE TABLE `etudiants_inscrits` (
  `id` int(11) NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `noms` varchar(30) NOT NULL,
  `password` text NOT NULL,
  `id_section` int(11) NOT NULL,
  `id_departement` int(11) NOT NULL,
  `id_option` int(11) NOT NULL,
  `promotion` varchar(10) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `etudiants_inscrits`
--

INSERT INTO `etudiants_inscrits` (`id`, `matricule`, `noms`, `password`, `id_section`, `id_departement`, `id_option`, `promotion`, `id_annee`) VALUES
(1, '2285', 'MICAH BAHIZI JUSTIN', '7c2949a45b32f9b74a75b3bfabe08a061210d0f1', 1, 1, 1, 'G3', 1);

-- --------------------------------------------------------

--
-- Structure de la table `gestion_cheque`
--

CREATE TABLE `gestion_cheque` (
  `id` int(11) NOT NULL,
  `liebelle` varchar(200) NOT NULL,
  `num_cheque` varchar(200) NOT NULL,
  `montant` float NOT NULL,
  `date_` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `gestion_cheque`
--

INSERT INTO `gestion_cheque` (`id`, `liebelle`, `num_cheque`, `montant`, `date_`) VALUES
(1, 'test', 'JHG765', 120, '2022-05-20 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `gest_honoraire`
--

CREATE TABLE `gest_honoraire` (
  `id` int(11) NOT NULL,
  `noms_ens` varchar(30) NOT NULL,
  `grade_ens` varchar(30) NOT NULL,
  `cours` varchar(50) NOT NULL,
  `heure_th` int(11) NOT NULL,
  `montant_ht` float NOT NULL,
  `heure_pr` int(11) NOT NULL,
  `montant_hp` float NOT NULL,
  `taux` int(11) NOT NULL,
  `total` float NOT NULL,
  `total_payer` float DEFAULT '0',
  `type_enseig` varchar(50) NOT NULL,
  `prestation` varchar(50) NOT NULL,
  `id_annee` int(11) NOT NULL,
  `id_section` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `gest_honoraire`
--

INSERT INTO `gest_honoraire` (`id`, `noms_ens`, `grade_ens`, `cours`, `heure_th`, `montant_ht`, `heure_pr`, `montant_hp`, `taux`, `total`, `total_payer`, `type_enseig`, `prestation`, `id_annee`, `id_section`) VALUES
(1, 'Micah', 'ASS', 'Java', 20, 100, 30, 150, 5, 250, 20, 'Visiteur', 'Ordinaire', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `log_user`
--

CREATE TABLE `log_user` (
  `id_log` int(11) NOT NULL,
  `log_action` text NOT NULL,
  `date_action` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `log_user`
--

INSERT INTO `log_user` (`id_log`, `log_action`, `date_action`, `id_user`) VALUES
(1, 'ajout de l\'annÃ©e academique.', '2022-05-20 15:51:32', 1),
(2, 'a ajoutÃ© une facultÃ©', '2022-05-20 15:52:12', 1),
(3, 'ajout de d\'un departement.', '2022-05-20 15:52:35', 1),
(4, 'ajout d\'une option.', '2022-05-20 15:54:33', 1),
(5, 'il a uploader le fichier [ins_etudiants.xlsx] contenant les identitÃ©s des Ã©tudiants', '2022-05-20 16:01:53', 1),
(6, 'il a uploader le fichier [ins_etudiants.xlsx] contenant les identitÃ©s des Ã©tudiants', '2022-05-20 16:01:59', 1),
(7, 'il a uploader le fichier [ins_etudiants.xlsx] contenant les identitÃ©s des Ã©tudiants', '2022-05-20 16:02:02', 1),
(8, 'il a uploader le fichier [ins_etudiants.xlsx] contenant les identitÃ©s des Ã©tudiants', '2022-05-20 16:02:03', 1),
(9, 'il a uploader le fichier [ins_etudiants.xlsx] contenant les identitÃ©s des Ã©tudiants', '2022-05-20 16:02:03', 1),
(10, 'il a uploader le fichier [ins_etudiants.xlsx] contenant les identitÃ©s des Ã©tudiants', '2022-05-20 16:02:04', 1),
(11, 'a ajoutÃ© \'acadÃ©mique :120$\' comme prevision de frais pour les etudiants.', '2022-05-20 16:05:38', 1),
(12, 'a mis Ã  jour le montant de prÃ©vision', '2022-05-20 16:06:06', 1),
(13, 's\'est connectÃ©(e) Connexion au system.', '2022-05-20 18:38:16', 1),
(14, 'il/elle a supprimÃ©(e) certains type de frais affecter Ã  ', '2022-05-20 19:23:51', 1),
(15, 'il/elle a supprimÃ©(e) certains type de frais affecter Ã  ', '2022-05-20 19:23:52', 1),
(16, 'il/elle a supprimÃ©(e) certains type de frais affecter Ã  l Ã©tudiant(e)', '2022-05-20 19:24:42', 1),
(17, 'il/elle a supprimÃ©(e) certains type de frais affecter Ã  l Ã©tudiant(e)', '2022-05-20 19:25:28', 1),
(18, 's\'est connectÃ©(e) Connexion au system.', '2022-05-20 20:57:18', 1),
(19, 'a il(elle) Ã©ffectuÃ©(e) sur le honohaire des enseignants', '2022-05-20 21:10:55', 1),
(20, 'il(elle) a update les informations de l\' enseignants \'Micah\'.', '2022-05-20 21:17:34', 1),
(21, 'il(elle) a update les informations de l\' enseignants \'Micah\'.', '2022-05-20 21:18:24', 1),
(22, 'il(elle) a update les informations de l\' enseignants \'Micah\'.', '2022-05-20 21:18:46', 1),
(23, 'a il(elle) Ã©ffectuÃ©(e) sur le honohaire des enseignants', '2022-05-20 21:19:37', 1),
(27, 's\'est connectÃ©(e) Connexion au system.', '2022-05-21 08:38:25', 1),
(28, 'il(elle) ajoutã©(e)  [jean] parmi les les utilisateurs administrateur !.', '2022-05-21 08:40:33', 1),
(30, 's\'est connectÃ©(e) Connexion au system.', '2022-05-21 20:35:46', 1),
(37, 's\'est connectÃ©(e) Connexion au system.', '2022-05-22 10:31:37', 1),
(38, 'il(elle) ajoutã©(e)  [mic] parmi les les utilisateurs administrateur !.', '2022-05-22 10:44:01', 1),
(39, 'il(elle) ajoutã©(e)  [mic] parmi les les utilisateurs administrateur !.', '2022-05-22 10:46:49', 1),
(40, 'il(elle) ajoutã©(e)  [admin@gmail.com] parmi les les utilisateurs administrateur !.', '2022-05-22 11:00:55', 1),
(41, 'il(elle) ajoutã©(e)  [mic] parmi les les utilisateurs administrateur !.', '2022-05-22 11:08:38', 1),
(42, 'il(elle) ajoutã©(e)  [mic] parmi les les utilisateurs administrateur !.', '2022-05-22 11:18:06', 1),
(43, 's\'est connectÃ©(e) Connexion au system.', '2022-05-22 11:20:50', 1),
(44, 'il(elle) ajoutã©(e)  [jean] parmi les les utilisateurs administrateur !.', '2022-05-22 11:23:57', 1),
(45, 's\'est connectÃ©(e) Connexion au system.', '2022-05-22 11:24:59', 41),
(46, 'a ajoutÃ©(e) un nouveau poste de dÃ©pense facultaire.', '2022-05-22 11:58:35', 41),
(47, 'a ajoutÃ©(e) un nouveau poste de dÃ©pense facultaire.', '2022-05-22 11:59:42', 41),
(48, 'a fait une transaction sur le poste facultaire.', '2022-05-22 12:41:24', 41),
(49, 'a fait une transaction sur le poste facultaire.', '2022-05-22 14:15:27', 41),
(50, 'a fait une transaction sur le poste facultaire.', '2022-05-22 14:15:42', 41),
(51, 'a fait une transaction sur le poste facultaire.', '2022-05-22 14:16:01', 41),
(52, 'a fait une transaction sur le poste facultaire.', '2022-05-22 14:16:19', 41),
(53, 'a supprimÃ©(e) un poste de dÃ©pense facultaire.', '2022-05-22 14:20:34', 41),
(54, 'a ajoutÃ©(e) un nouveau poste de dÃ©pense facultaire.', '2022-05-22 14:21:35', 41),
(55, 'a mofier le montant du poste de depense :)', '2022-05-22 14:24:10', 41),
(56, 's\'est connectÃ©(e) Connexion au system.', '2022-05-22 14:29:16', 1),
(57, 'a fait une transaction sur le poste de dÃ©pense...', '2022-05-22 14:29:59', 1),
(58, 'a mofier le montant du poste de depense :)', '2022-05-22 14:30:10', 1),
(59, 'a mofier le montant du poste de depense :)', '2022-05-22 14:30:21', 1),
(60, 'a fait une transaction sur le poste de dÃ©pense...', '2022-05-22 14:32:42', 1),
(61, 'a fait une transaction sur le poste de dÃ©pense...', '2022-05-22 14:33:03', 1),
(62, 'a supprimÃ©(e) un poste de dÃ©pense.', '2022-05-22 14:34:38', 1),
(63, 'a fait une transaction sur le poste de dÃ©pense...', '2022-05-22 14:36:50', 1),
(64, 'a fait une transaction sur le poste de dÃ©pense...', '2022-05-22 14:37:18', 1);

-- --------------------------------------------------------

--
-- Structure de la table `options`
--

CREATE TABLE `options` (
  `id_option` int(11) NOT NULL,
  `option_` varchar(50) NOT NULL,
  `promotion` varchar(10) NOT NULL,
  `code_` varchar(10) NOT NULL,
  `id_departement` int(11) NOT NULL,
  `id_section` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `options`
--

INSERT INTO `options` (`id_option`, `option_`, `promotion`, `code_`, `id_departement`, `id_section`, `id_annee`) VALUES
(1, 'Informatique Industrielle & rÃ©seau', 'G3', 'G3 IIR', 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `payement`
--

CREATE TABLE `payement` (
  `id_payement` int(11) NOT NULL,
  `montant` float NOT NULL,
  `date_payement` datetime NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `id_frais` int(11) NOT NULL,
  `id_section` int(11) NOT NULL,
  `id_departement` int(11) NOT NULL,
  `id_option` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `poste_depense`
--

CREATE TABLE `poste_depense` (
  `id_poste` int(11) NOT NULL,
  `poste` varchar(200) NOT NULL,
  `montant` float NOT NULL,
  `depense` float NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `poste_depense`
--

INSERT INTO `poste_depense` (`id_poste`, `poste`, `montant`, `depense`, `id_annee`) VALUES
(2, 'Communication', 1200, 20, 1);

-- --------------------------------------------------------

--
-- Structure de la table `poste_recette`
--

CREATE TABLE `poste_recette` (
  `id_post_rec` int(11) NOT NULL,
  `poste_rec` varchar(90) NOT NULL,
  `montant` float NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `poste_recette`
--

INSERT INTO `poste_recette` (`id_post_rec`, `poste_rec`, `montant`, `id_annee`) VALUES
(1, 'un poste', 1200, 1);

-- --------------------------------------------------------

--
-- Structure de la table `prevision_frais`
--

CREATE TABLE `prevision_frais` (
  `id_frais` int(11) NOT NULL,
  `type_frais` varchar(200) NOT NULL,
  `montant` float NOT NULL,
  `promotion` varchar(10) NOT NULL,
  `id_section` int(11) DEFAULT NULL,
  `id_departement` int(11) DEFAULT NULL,
  `id_option` int(11) DEFAULT NULL,
  `id_annee` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `prevision_frais`
--

INSERT INTO `prevision_frais` (`id_frais`, `type_frais`, `montant`, `promotion`, `id_section`, `id_departement`, `id_option`, `id_annee`) VALUES
(1, 'acadÃ©mique ', 100, 'G3', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `sections`
--

CREATE TABLE `sections` (
  `id_section` int(11) NOT NULL,
  `section` varchar(200) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `sections`
--

INSERT INTO `sections` (`id_section`, `section`, `id_annee`) VALUES
(1, 'SECTION INFORMATIQUE			', 1);

-- --------------------------------------------------------

--
-- Structure de la table `transaction_depense`
--

CREATE TABLE `transaction_depense` (
  `id_transaction` int(11) NOT NULL,
  `montant` float NOT NULL,
  `date_motif` datetime NOT NULL,
  `motif` varchar(200) NOT NULL,
  `id_poste` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL,
  `num_op` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `transaction_depense`
--

INSERT INTO `transaction_depense` (`id_transaction`, `montant`, `date_motif`, `motif`, `id_poste`, `id_annee`, `num_op`) VALUES
(4, 10, '2022-05-21 00:00:00', 'Communication Elias', 2, 1, '100UEJE'),
(5, 10, '2022-05-22 00:00:00', 'Communication Micah', 2, 1, 'Ei100');

-- --------------------------------------------------------

--
-- Structure de la table `transaction_pdf`
--

CREATE TABLE `transaction_pdf` (
  `id_transaction` int(11) NOT NULL,
  `montant` float NOT NULL,
  `motif` varchar(255) NOT NULL,
  `date_transaction` datetime NOT NULL,
  `id_pdf` int(11) NOT NULL,
  `id_section` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_user` int(11) NOT NULL,
  `noms` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `access` varchar(20) NOT NULL,
  `fonction` varchar(20) NOT NULL,
  `profil` varchar(200) NOT NULL,
  `pwd` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_user`, `noms`, `email`, `access`, `fonction`, `profil`, `pwd`) VALUES
(1, 'Micah', 'admin@gmail.com', 'Admin', 'Admin', 'img/undraw_profile.svg', 'fd112246381fb0d13223d0c71097a0cbedf0c2c1'),
(41, 'Jean', 'jean@gmail.com', '1', 'Sec. de fac.', 'img/undraw_profile.svg', 'ab2ae19f3df624c319af99d2c87803bf5bdac31c');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `affectation_frais`
--
ALTER TABLE `affectation_frais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_frais` (`id_frais`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `annee_acad`
--
ALTER TABLE `annee_acad`
  ADD PRIMARY KEY (`id_annee`),
  ADD UNIQUE KEY `annee_acad` (`annee_acad`);

--
-- Index pour la table `departement`
--
ALTER TABLE `departement`
  ADD PRIMARY KEY (`id_departement`),
  ADD KEY `id_section` (`id_section`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `depense_facultaire`
--
ALTER TABLE `depense_facultaire`
  ADD PRIMARY KEY (`id_pdf`),
  ADD KEY `id_section` (`id_section`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `etudiants_inscrits`
--
ALTER TABLE `etudiants_inscrits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_annee` (`id_annee`),
  ADD KEY `promotion` (`promotion`),
  ADD KEY `matricule` (`matricule`);

--
-- Index pour la table `gestion_cheque`
--
ALTER TABLE `gestion_cheque`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `gest_honoraire`
--
ALTER TABLE `gest_honoraire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_annee` (`id_annee`),
  ADD KEY `id_section` (`id_section`);

--
-- Index pour la table `log_user`
--
ALTER TABLE `log_user`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id_option`),
  ADD UNIQUE KEY `code_` (`code_`),
  ADD KEY `id_departement` (`id_departement`),
  ADD KEY `id_section` (`id_section`),
  ADD KEY `id_annee` (`id_annee`),
  ADD KEY `promotion` (`promotion`);

--
-- Index pour la table `payement`
--
ALTER TABLE `payement`
  ADD PRIMARY KEY (`id_payement`),
  ADD KEY `id_frais` (`id_frais`),
  ADD KEY `id_annee` (`id_annee`),
  ADD KEY `id_section` (`id_section`),
  ADD KEY `id_departement` (`id_departement`),
  ADD KEY `id_option` (`id_option`);

--
-- Index pour la table `poste_depense`
--
ALTER TABLE `poste_depense`
  ADD PRIMARY KEY (`id_poste`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `poste_recette`
--
ALTER TABLE `poste_recette`
  ADD PRIMARY KEY (`id_post_rec`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `prevision_frais`
--
ALTER TABLE `prevision_frais`
  ADD PRIMARY KEY (`id_frais`),
  ADD KEY `id_option` (`id_option`),
  ADD KEY `id_section` (`id_section`),
  ADD KEY `id_departement` (`id_departement`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id_section`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `transaction_depense`
--
ALTER TABLE `transaction_depense`
  ADD PRIMARY KEY (`id_transaction`),
  ADD KEY `id_poste` (`id_poste`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `transaction_pdf`
--
ALTER TABLE `transaction_pdf`
  ADD PRIMARY KEY (`id_transaction`),
  ADD KEY `id_pdf` (`id_pdf`),
  ADD KEY `id_section` (`id_section`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `mail` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `affectation_frais`
--
ALTER TABLE `affectation_frais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `annee_acad`
--
ALTER TABLE `annee_acad`
  MODIFY `id_annee` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `departement`
--
ALTER TABLE `departement`
  MODIFY `id_departement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `depense_facultaire`
--
ALTER TABLE `depense_facultaire`
  MODIFY `id_pdf` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `etudiants_inscrits`
--
ALTER TABLE `etudiants_inscrits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `gestion_cheque`
--
ALTER TABLE `gestion_cheque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `gest_honoraire`
--
ALTER TABLE `gest_honoraire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `log_user`
--
ALTER TABLE `log_user`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT pour la table `options`
--
ALTER TABLE `options`
  MODIFY `id_option` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `payement`
--
ALTER TABLE `payement`
  MODIFY `id_payement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `poste_depense`
--
ALTER TABLE `poste_depense`
  MODIFY `id_poste` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `poste_recette`
--
ALTER TABLE `poste_recette`
  MODIFY `id_post_rec` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `prevision_frais`
--
ALTER TABLE `prevision_frais`
  MODIFY `id_frais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `sections`
--
ALTER TABLE `sections`
  MODIFY `id_section` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `transaction_depense`
--
ALTER TABLE `transaction_depense`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `transaction_pdf`
--
ALTER TABLE `transaction_pdf`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `affectation_frais`
--
ALTER TABLE `affectation_frais`
  ADD CONSTRAINT `affectation_frais_ibfk_1` FOREIGN KEY (`id_frais`) REFERENCES `prevision_frais` (`id_frais`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affectation_frais_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `departement`
--
ALTER TABLE `departement`
  ADD CONSTRAINT `departement_ibfk_1` FOREIGN KEY (`id_section`) REFERENCES `sections` (`id_section`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `departement_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `depense_facultaire`
--
ALTER TABLE `depense_facultaire`
  ADD CONSTRAINT `depense_facultaire_ibfk_1` FOREIGN KEY (`id_section`) REFERENCES `sections` (`id_section`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `depense_facultaire_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `etudiants_inscrits`
--
ALTER TABLE `etudiants_inscrits`
  ADD CONSTRAINT `etudiants_inscrits_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON DELETE CASCADE,
  ADD CONSTRAINT `etudiants_inscrits_ibfk_2` FOREIGN KEY (`promotion`) REFERENCES `options` (`promotion`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `gest_honoraire`
--
ALTER TABLE `gest_honoraire`
  ADD CONSTRAINT `gest_honoraire_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gest_honoraire_ibfk_2` FOREIGN KEY (`id_section`) REFERENCES `sections` (`id_section`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `log_user`
--
ALTER TABLE `log_user`
  ADD CONSTRAINT `log_user_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateurs` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`id_departement`) REFERENCES `departement` (`id_departement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `options_ibfk_2` FOREIGN KEY (`id_section`) REFERENCES `sections` (`id_section`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `options_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `payement`
--
ALTER TABLE `payement`
  ADD CONSTRAINT `payement_ibfk_1` FOREIGN KEY (`id_frais`) REFERENCES `affectation_frais` (`id_frais`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payement_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payement_ibfk_3` FOREIGN KEY (`id_section`) REFERENCES `sections` (`id_section`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payement_ibfk_4` FOREIGN KEY (`id_departement`) REFERENCES `departement` (`id_departement`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payement_ibfk_5` FOREIGN KEY (`id_option`) REFERENCES `options` (`id_option`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `poste_depense`
--
ALTER TABLE `poste_depense`
  ADD CONSTRAINT `poste_depense_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `poste_recette`
--
ALTER TABLE `poste_recette`
  ADD CONSTRAINT `poste_recette_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `prevision_frais`
--
ALTER TABLE `prevision_frais`
  ADD CONSTRAINT `prevision_frais_ibfk_1` FOREIGN KEY (`id_option`) REFERENCES `options` (`id_option`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prevision_frais_ibfk_2` FOREIGN KEY (`id_section`) REFERENCES `sections` (`id_section`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prevision_frais_ibfk_3` FOREIGN KEY (`id_departement`) REFERENCES `departement` (`id_departement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prevision_frais_ibfk_4` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `transaction_depense`
--
ALTER TABLE `transaction_depense`
  ADD CONSTRAINT `transaction_depense_ibfk_1` FOREIGN KEY (`id_poste`) REFERENCES `poste_depense` (`id_poste`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_depense_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `transaction_pdf`
--
ALTER TABLE `transaction_pdf`
  ADD CONSTRAINT `transaction_pdf_ibfk_1` FOREIGN KEY (`id_pdf`) REFERENCES `depense_facultaire` (`id_pdf`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_pdf_ibfk_2` FOREIGN KEY (`id_section`) REFERENCES `sections` (`id_section`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_pdf_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annee_acad` (`id_annee`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

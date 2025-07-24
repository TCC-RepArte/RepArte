-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 24, 2025 at 06:42 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reparte`
--

-- --------------------------------------------------------

--
-- Table structure for table `comentarios`
--

DROP TABLE IF EXISTS `comentarios`;
CREATE TABLE IF NOT EXISTS `comentarios` (
  `id_conteudo` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_usuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_conteudo` enum('postagem','comentario') COLLATE utf8mb4_general_ci NOT NULL,
  `texto` text COLLATE utf8mb4_general_ci NOT NULL,
  `data` datetime NOT NULL,
  `data_edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_login_comentarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
CREATE TABLE IF NOT EXISTS `login` (
  `usuario` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `obras`
--

DROP TABLE IF EXISTS `obras`;
CREATE TABLE IF NOT EXISTS `obras` (
  `id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `autor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo` enum('livro','filme','serie','arte','musica') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ano_lancamento` year DEFAULT NULL,
  `descricao` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `perfil`
--

DROP TABLE IF EXISTS `perfil`;
CREATE TABLE IF NOT EXISTS `perfil` (
  `nomexi` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `caminho` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descri` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `data_perf` datetime NOT NULL,
  KEY `idx_login` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postagens`
--

DROP TABLE IF EXISTS `postagens`;
CREATE TABLE IF NOT EXISTS `postagens` (
  `id_usuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `id_obra` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `titulo` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `texto` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `data_post` datetime NOT NULL,
  `data_edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_login_postagens` (`id_usuario`),
  KEY `fk_obras_postagens` (`id_obra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reacoes`
--

DROP TABLE IF EXISTS `reacoes`;
CREATE TABLE IF NOT EXISTS `reacoes` (
  `id_usuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_conteudo` enum('postagem','comentario') COLLATE utf8mb4_general_ci NOT NULL,
  `id_conteudo` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('like','dislike') COLLATE utf8mb4_general_ci NOT NULL,
  `data` datetime DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_usuario` (`id_usuario`),
  KEY `idx_conteudo` (`id_conteudo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `fk_login_comentarios` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `perfil`
--
ALTER TABLE `perfil`
  ADD CONSTRAINT `fk_perfil_login` FOREIGN KEY (`id`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `postagens`
--
ALTER TABLE `postagens`
  ADD CONSTRAINT `fk_login_postagens` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_obras_postagens` FOREIGN KEY (`id_obra`) REFERENCES `obras` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reacoes`
--
ALTER TABLE `reacoes`
  ADD CONSTRAINT `fk_login_reacoes` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

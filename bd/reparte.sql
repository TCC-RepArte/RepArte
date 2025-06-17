-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 16, 2025 at 06:18 PM
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
  `id_conteudo` varchar(20) NOT NULL,
  `id_usuario` varchar(36) NOT NULL,
  `id` varchar(20) NOT NULL,
  `tipo_conteudo` enum('postagem','comentario') NOT NULL,
  `texto` text NOT NULL,
  `data` datetime NOT NULL,
  `data_edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_login_comentarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
CREATE TABLE IF NOT EXISTS `login` (
  `usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `id` varchar(36) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`usuario`, `email`, `senha`, `id`) VALUES
('@ooi', 'teste@gmail.com', '$2y$10$IXOk1.DgiWqhDINk9r62bua7o3PYyW8D7XCVBwra5hwtT4mxxzJBS', 'L3HQO5f-RQX');

-- --------------------------------------------------------

--
-- Table structure for table `obras`
--

DROP TABLE IF EXISTS `obras`;
CREATE TABLE IF NOT EXISTS `obras` (
  `id` varchar(36) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `autor` varchar(100) DEFAULT NULL,
  `tipo` enum('livro','filme','música','outro') NOT NULL DEFAULT 'outro',
  `ano_lancamento` year DEFAULT NULL,
  `descricao` text,
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `obras`
--

INSERT INTO `obras` (`id`, `titulo`, `autor`, `tipo`, `ano_lancamento`, `descricao`, `data_criacao`) VALUES
('obra_6845744d8bfc17.55123168', 'Obra não especificada', NULL, 'outro', NULL, 'Obra temporária para postagens sem referência específica', '2025-06-08 08:30:21');

-- --------------------------------------------------------

--
-- Table structure for table `perfil`
--

DROP TABLE IF EXISTS `perfil`;
CREATE TABLE IF NOT EXISTS `perfil` (
  `nomexi` varchar(50) NOT NULL,
  `caminho` varchar(255) NOT NULL,
  `id` varchar(36) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `descri` varchar(500) NOT NULL,
  `data_perf` datetime NOT NULL,
  KEY `idx_login` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `perfil`
--

INSERT INTO `perfil` (`nomexi`, `caminho`, `id`, `foto`, `descri`, `data_perf`) VALUES
('olaa', '../../imagens/684571dcb0175.png', 'L3HQO5f-RQX', '684571dcb0175.png', 'oii gente', '2025-06-08 08:19:56');

-- --------------------------------------------------------

--
-- Table structure for table `postagens`
--

DROP TABLE IF EXISTS `postagens`;
CREATE TABLE IF NOT EXISTS `postagens` (
  `id_usuario` varchar(36) NOT NULL,
  `id_obra` varchar(20) NOT NULL,
  `id` varchar(20) NOT NULL,
  `titulo` varchar(40) NOT NULL,
  `texto` text NOT NULL,
  `data` datetime NOT NULL,
  `data_edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_login_postagens` (`id_usuario`),
  KEY `fk_obras_postagens` (`id_obra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reacoes`
--

DROP TABLE IF EXISTS `reacoes`;
CREATE TABLE IF NOT EXISTS `reacoes` (
  `id_usuario` varchar(36) NOT NULL,
  `tipo_conteudo` enum('postagem','comentario') NOT NULL,
  `id_conteudo` varchar(20) NOT NULL,
  `tipo` enum('like','dislike') NOT NULL,
  `data` datetime DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_usuario` (`id_usuario`),
  KEY `idx_conteudo` (`id_conteudo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

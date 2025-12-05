-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 05, 2025 at 11:46 AM
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
-- Database: `if0_40154094_reparte`
--

-- --------------------------------------------------------

--
-- Table structure for table `codigos_verificacao`
--

DROP TABLE IF EXISTS `codigos_verificacao`;
CREATE TABLE IF NOT EXISTS `codigos_verificacao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `codigo` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `data_criacao` datetime NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT '0',
  `tentativas` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comentarios`
--

DROP TABLE IF EXISTS `comentarios`;
CREATE TABLE IF NOT EXISTS `comentarios` (
  `id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_conteudo` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_usuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_conteudo` enum('postagem','comentario') COLLATE utf8mb4_general_ci NOT NULL,
  `texto` text COLLATE utf8mb4_general_ci NOT NULL,
  `data` datetime NOT NULL,
  `data_edit` datetime DEFAULT NULL,
  `comentario_pai_id` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nivel` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_coment_user` (`id_usuario`),
  KEY `fk_coment_parent` (`comentario_pai_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `configuracoes_usuario`
--

DROP TABLE IF EXISTS `configuracoes_usuario`;
CREATE TABLE IF NOT EXISTS `configuracoes_usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `vlibras_ativo` tinyint(1) DEFAULT '1',
  `data_atualizacao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `configuracoes_usuario`
--

INSERT INTO `configuracoes_usuario` (`id`, `id_usuario`, `vlibras_ativo`, `data_atualizacao`) VALUES
(1, 'rFRCxqU-Yze', 1, '2025-12-04 07:34:54');

-- --------------------------------------------------------

--
-- Table structure for table `denuncias`
--

DROP TABLE IF EXISTS `denuncias`;
CREATE TABLE IF NOT EXISTS `denuncias` (
  `id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `id_denunciante` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_denuncia` enum('comentario','usuario','postagem') COLLATE utf8mb4_general_ci NOT NULL,
  `id_item_denunciado` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `motivo` text COLLATE utf8mb4_general_ci,
  `data_denuncia` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_denunciante` (`id_denunciante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favoritos`
--

DROP TABLE IF EXISTS `favoritos`;
CREATE TABLE IF NOT EXISTS `favoritos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `id_post` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `data_favorito` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_usuario` (`id_usuario`,`id_post`),
  KEY `fk_fav_post` (`id_post`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hashtags`
--

DROP TABLE IF EXISTS `hashtags`;
CREATE TABLE IF NOT EXISTS `hashtags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `contagem` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
CREATE TABLE IF NOT EXISTS `login` (
  `id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `usuario` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `usuario`, `email`, `senha`) VALUES
('CUVDa73-Ftn', 'Karina Barboza', 'kbstcc@gmail.com', '$2y$10$YeZTHjbER81Y920grlsvj.H0KC.lKfopJZgthU/21ZRg0yL7pAUaW'),
('rFRCxqU-Yze', 'reparte_oficial', 'equipereparte@gmail.com', '$2y$10$wp4ewNhKGrHPgQSh9N8Giub0EKjmnby.5ecPVXRzFV4lfgrvf3sle');

-- --------------------------------------------------------

--
-- Table structure for table `notificacoes`
--

DROP TABLE IF EXISTS `notificacoes`;
CREATE TABLE IF NOT EXISTS `notificacoes` (
  `id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `id_usuario_destino` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `id_usuario_origem` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('comentario','reacao','resposta') COLLATE utf8mb4_general_ci NOT NULL,
  `id_conteudo` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `mensagem` text COLLATE utf8mb4_general_ci NOT NULL,
  `lida` tinyint(1) DEFAULT '0',
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_notif_dest` (`id_usuario_destino`),
  KEY `fk_notif_orig` (`id_usuario_origem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `obras`
--

DROP TABLE IF EXISTS `obras`;
CREATE TABLE IF NOT EXISTS `obras` (
  `id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `titulo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `autor` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo` enum('livro','filme','serie','arte','musica') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ano_lancamento` year DEFAULT NULL,
  `descricao` mediumtext COLLATE utf8mb4_general_ci,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `perfil`
--

DROP TABLE IF EXISTS `perfil`;
CREATE TABLE IF NOT EXISTS `perfil` (
  `id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `nomexi` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `caminho` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descri` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `data_perf` datetime NOT NULL,
  `vlibras_ativo` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perfil`
--

INSERT INTO `perfil` (`id`, `nomexi`, `caminho`, `foto`, `descri`, `data_perf`, `vlibras_ativo`) VALUES
('rFRCxqU-Yze', 'RepArte', 'images/692c16f020f63.jpg', '692c16f020f63.jpg', 'Perfil Oficial da equipe RepArte', '2025-11-30 07:05:36', 1);

-- --------------------------------------------------------

--
-- Table structure for table `postagens`
--

DROP TABLE IF EXISTS `postagens`;
CREATE TABLE IF NOT EXISTS `postagens` (
  `id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_usuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `id_obra` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `titulo` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `texto` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `data_post` datetime NOT NULL,
  `data_edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_post_user` (`id_usuario`),
  KEY `fk_post_obra` (`id_obra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_hashtags`
--

DROP TABLE IF EXISTS `post_hashtags`;
CREATE TABLE IF NOT EXISTS `post_hashtags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `hashtag_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `hashtag_id` (`hashtag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `preferencias_notificacoes`
--

DROP TABLE IF EXISTS `preferencias_notificacoes`;
CREATE TABLE IF NOT EXISTS `preferencias_notificacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `notif_comentarios` tinyint(1) DEFAULT '1',
  `notif_reacoes` tinyint(1) DEFAULT '1',
  `notif_respostas` tinyint(1) DEFAULT '1',
  `data_atualizacao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reacoes`
--

DROP TABLE IF EXISTS `reacoes`;
CREATE TABLE IF NOT EXISTS `reacoes` (
  `id` varchar(14) COLLATE utf8mb4_general_ci NOT NULL,
  `id_usuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_conteudo` enum('postagem','comentario') COLLATE utf8mb4_general_ci NOT NULL,
  `id_conteudo` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `data` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_reac_user` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recuperacao_senha`
--

DROP TABLE IF EXISTS `recuperacao_senha`;
CREATE TABLE IF NOT EXISTS `recuperacao_senha` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `data_criacao` datetime NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_token` (`token`),
  KEY `idx_expiracao` (`data_expiracao`),
  KEY `fk_recuperacao_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `fk_coment_parent` FOREIGN KEY (`comentario_pai_id`) REFERENCES `comentarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_coment_user` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `configuracoes_usuario`
--
ALTER TABLE `configuracoes_usuario`
  ADD CONSTRAINT `fk_config_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `denuncias`
--
ALTER TABLE `denuncias`
  ADD CONSTRAINT `denuncias_ibfk_1` FOREIGN KEY (`id_denunciante`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `fk_fav_post` FOREIGN KEY (`id_post`) REFERENCES `postagens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fav_user` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `fk_notif_dest` FOREIGN KEY (`id_usuario_destino`) REFERENCES `login` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_notif_orig` FOREIGN KEY (`id_usuario_origem`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `perfil`
--
ALTER TABLE `perfil`
  ADD CONSTRAINT `fk_perfil_usuario` FOREIGN KEY (`id`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `postagens`
--
ALTER TABLE `postagens`
  ADD CONSTRAINT `fk_post_obra` FOREIGN KEY (`id_obra`) REFERENCES `obras` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_post_user` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_hashtags`
--
ALTER TABLE `post_hashtags`
  ADD CONSTRAINT `post_hashtags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `postagens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_hashtags_ibfk_2` FOREIGN KEY (`hashtag_id`) REFERENCES `hashtags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `preferencias_notificacoes`
--
ALTER TABLE `preferencias_notificacoes`
  ADD CONSTRAINT `fk_pref_user` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reacoes`
--
ALTER TABLE `reacoes`
  ADD CONSTRAINT `fk_reac_user` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  ADD CONSTRAINT `fk_recuperacao_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

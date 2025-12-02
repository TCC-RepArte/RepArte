-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 30, 2025 at 11:57 PM
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
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_codigo` (`codigo`),
  KEY `idx_expiracao` (`data_expiracao`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `comentario_pai_id` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nivel` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_login_comentarios` (`id_usuario`),
  KEY `idx_comentario_pai` (`comentario_pai_id`),
  KEY `idx_nivel` (`nivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comentarios`
--

INSERT INTO `comentarios` (`id_conteudo`, `id_usuario`, `id`, `tipo_conteudo`, `texto`, `data`, `data_edit`, `comentario_pai_id`, `nivel`) VALUES
('68f247448db17', '7WhmT6p-Jmm', 'comment_68f247599bad', 'postagem', 'gosteiii', '2025-10-17 06:40:41', NULL, NULL, 0),
('68f249f576c20', '1NYz7Fz-kGq', 'comment_68f24a0d01a2', 'postagem', 'nao gostei', '2025-10-17 06:52:13', NULL, NULL, 0),
('68f24bcd3dbee', 'zHamNyo-1Tf', 'comment_68f24be9cf56', 'postagem', 'n gostei', '2025-10-17 07:00:09', NULL, NULL, 0);

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
(1, 'rFRCxqU-Yze', 1, '2025-11-30 14:46:34');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `denuncias`
--

INSERT INTO `denuncias` (`id`, `id_denunciante`, `tipo_denuncia`, `id_item_denunciado`, `motivo`, `data_denuncia`) VALUES
('7swVgrHDinuf-qvl', 'rFRCxqU-Yze', 'postagem', '68f24bcd3dbee', 'oi', '2025-11-30 18:28:24');

-- --------------------------------------------------------

--
-- Table structure for table `favoritos`
--

DROP TABLE IF EXISTS `favoritos`;
CREATE TABLE IF NOT EXISTS `favoritos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `id_post` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `data_favorito` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favorito` (`id_usuario`,`id_post`),
  KEY `id_post` (`id_post`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `usuario` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`usuario`, `email`, `senha`, `id`) VALUES
('Lucas cerqueira', 'lucas@gmail.com', '$2y$10$HMII1vOkIxmatg3IqhtgfeXzmlu1cuTINdw1y9DvHjKDezFdnNQbK', '1NYz7Fz-kGq'),
('suzane', 'suzane@gmail.com', '$2y$10$Jq5XNoOiXYJt33GnhnfZZuct9BEPpLrrm.4ymijDjLHRgQBp.MZ1e', '7WhmT6p-Jmm'),
('Karina Barboza', 'kbstcc@gmail.com', '$2y$10$YeZTHjbER81Y920grlsvj.H0KC.lKfopJZgthU/21ZRg0yL7pAUaW', 'CUVDa73-Ftn'),
('testando', 'etecluisgust@gmail.com', '$2y$10$SkEYxbvFN1Fys94mmqJkfuPwpje46reY55f/eY9NuW8pEK5B5tbL6', 'jkg6Jp6-vzf'),
('ooi', 'luisluxando@gmail.com', '$2y$10$7pAks8Ge5nD9vN/XD5siR.qRzLTb.FHqeloOELNf0ocZFrl0MkrM6', 'MxciYvJ-HgM'),
('reparte_oficial', 'equipereparte@gmail.com', '$2y$10$wp4ewNhKGrHPgQSh9N8Giub0EKjmnby.5ecPVXRzFV4lfgrvf3sle', 'rFRCxqU-Yze'),
('laistestee', 'lais.ferro00@gmail.com', '$2y$10$ur/nznqLABk2jT2.N7Gaw.23wp4q1G153BoRd0TEH0I6tzEn.UXBO', 's52zJxe-ExR'),
('laisteste', 'lais.ferro05@gmail.com', '$2y$10$ikCDobZAR6q0aypwdQH4buypOFS5V09wUy7jLmSOISBWnvJA7Uqae', 'uM8O5YT-tmB'),
('ok', 'ok@gmail.com', '$2y$10$sab6Q36q00H6eM6dkEuNcurMLPYL3NTZMS.aECQ.rE1HaTK/oT5eK', 'VQuHX6G-x7G'),
('Cleiton', 'cleiton@gmail.com', '$2y$10$E7RELwGU88hXpYlqaRieauukbG9VIHC3i7ni8vuIA.q.bWnXSodiy', 'zHamNyo-1Tf');

-- --------------------------------------------------------

--
-- Table structure for table `mensagens`
--

DROP TABLE IF EXISTS `mensagens`;
CREATE TABLE IF NOT EXISTS `mensagens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_remetente` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `id_destinatario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `mensagem` text COLLATE utf8mb4_general_ci NOT NULL,
  `data_envio` datetime DEFAULT CURRENT_TIMESTAMP,
  `lida` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_remetente` (`id_remetente`),
  KEY `id_destinatario` (`id_destinatario`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mensagens`
--

INSERT INTO `mensagens` (`id`, `id_remetente`, `id_destinatario`, `mensagem`, `data_envio`, `lida`) VALUES
(1, 'rFRCxqU-Yze', 'MxciYvJ-HgM', 'teste', '2025-11-30 10:02:23', 0),
(2, 'jkg6Jp6-vzf', 'rFRCxqU-Yze', 'testando', '2025-11-30 10:03:21', 0),
(3, 'rFRCxqU-Yze', 'jkg6Jp6-vzf', 'eaeeee', '2025-11-30 10:04:05', 0);

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
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `obras`
--

INSERT INTO `obras` (`id`, `titulo`, `autor`, `tipo`, `ano_lancamento`, `descricao`, `data_criacao`) VALUES
('27qC3KK5wjQzyNenY53fep', 'F1', 'Hans Zimmer', 'musica', '2025', '', '2025-10-17 10:40:19'),
('933260', 'A SubstÃ¢ncia', '', 'filme', '2024', 'Uma estrela em declÃ­nio descobre uma substÃ¢ncia misteriosa que lhe permite criar uma versÃ£o mais jovem e perfeita de si mesma. No entanto, o que parece ser uma soluÃ§Ã£o milagrosa para recuperar sua glÃ³ria logo se transforma em um pesadelo perturbador quando as consequÃªncias desta decisÃ£o comeÃ§am a se manifestar de formas sombrias e irreversÃ­veis.', '2025-10-17 10:59:41'),
('9799', 'Velozes e Furiosos', '', 'filme', '2001', 'Um investigador da polÃ­cia se infiltra em uma turma de rachas suspeita de roubar caminhÃµes, mas acaba apaixonando-se pela irmÃ£ do lÃ­der.', '2025-10-17 10:51:49');

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
  `vlibras_ativo` tinyint(1) DEFAULT '0',
  KEY `idx_login` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perfil`
--

INSERT INTO `perfil` (`nomexi`, `caminho`, `id`, `foto`, `descri`, `data_perf`, `vlibras_ativo`) VALUES
('suzane01', 'images/68f246f362d3f.png', '7WhmT6p-Jmm', '68f246f362d3f.png', '15 anos', '2025-10-17 10:38:59', 0),
('lucas', 'images/68f2499c842dc.png', '1NYz7Fz-kGq', '68f2499c842dc.png', '17 anos', '2025-10-17 10:50:20', 0),
('cleiton', 'images/68f24b7e61c7c.png', 'zHamNyo-1Tf', '68f24b7e61c7c.png', '50 anos', '2025-10-17 10:58:22', 0),
('ooooi', 'images/691ceb8e7a19d.png', 'MxciYvJ-HgM', '691ceb8e7a19d.png', 'sim', '2025-11-18 18:56:30', 0),
('lais', 'images/692204e95204d.jpg', 's52zJxe-ExR', '692204e95204d.jpg', 'estudante da etec', '2025-11-22 15:46:01', 0),
('lais', 'images/692205696ca99.jpg', 'uM8O5YT-tmB', '692205696ca99.jpg', 'testee', '2025-11-22 15:48:09', 0),
('ok', 'images/69220731c4eff.jpg', 'VQuHX6G-x7G', '69220731c4eff.jpg', 'etec', '2025-11-22 15:55:45', 0),
('ok', 'images/6922088ccbb15.jpg', 'VQuHX6G-x7G', '6922088ccbb15.jpg', 'etec', '2025-11-22 16:01:32', 0),
('ooooii', 'images/69279d3d891f5.png', 'jkg6Jp6-vzf', '69279d3d891f5.png', 'sim', '2025-11-26 21:37:17', 0),
('RepArte', 'images/692c16f020f63.jpg', 'rFRCxqU-Yze', '692c16f020f63.jpg', 'Perfil Oficial da equipe RepArte', '2025-11-30 07:05:36', 1);

-- --------------------------------------------------------

--
-- Table structure for table `postagens`
--

DROP TABLE IF EXISTS `postagens`;
CREATE TABLE IF NOT EXISTS `postagens` (
  `id_usuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `id_obra` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `titulo` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `texto` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `data_post` datetime NOT NULL,
  `data_edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_login_postagens` (`id_usuario`),
  KEY `fk_obras_postagens` (`id_obra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `postagens`
--

INSERT INTO `postagens` (`id_usuario`, `id_obra`, `id`, `titulo`, `texto`, `data_post`, `data_edit`) VALUES
('7WhmT6p-Jmm', '27qC3KK5wjQzyNenY53fep', '68f247435d7da', 'ayrton senna', 'gostei musica', '2025-10-17 10:40:19', NULL),
('7WhmT6p-Jmm', '27qC3KK5wjQzyNenY53fep', '68f247448db17', 'ayrton senna', 'gostei musica', '2025-10-17 10:40:20', NULL),
('1NYz7Fz-kGq', '9799', '68f249f576c20', 'toretto', 'gostei muito', '2025-10-17 10:51:49', NULL),
('zHamNyo-1Tf', '933260', '68f24bcd3dbee', 'sue', 'gostei', '2025-10-17 10:59:41', NULL);

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
-- Table structure for table `reacoes`
--

DROP TABLE IF EXISTS `reacoes`;
CREATE TABLE IF NOT EXISTS `reacoes` (
  `id_usuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_conteudo` enum('postagem','comentario') COLLATE utf8mb4_general_ci NOT NULL,
  `id_conteudo` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id` varchar(14) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('like','dislike') COLLATE utf8mb4_general_ci NOT NULL,
  `data` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`id_usuario`),
  KEY `idx_conteudo` (`id_conteudo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reacoes`
--

INSERT INTO `reacoes` (`id_usuario`, `tipo_conteudo`, `id_conteudo`, `id`, `tipo`, `data`) VALUES
('7WhmT6p-Jmm', 'postagem', '68f247448db17', '68f2474d4c856', 'dislike', '2025-10-17 06:40:30'),
('1NYz7Fz-kGq', 'postagem', '68f249f576c20', '68f249fd52c4e', 'dislike', '2025-10-17 06:51:58'),
('zHamNyo-1Tf', 'postagem', '68f24bcd3dbee', '68f24bd2d34c7', 'dislike', '2025-10-17 06:59:47'),
('jkg6Jp6-vzf', 'comentario', 'comment_68f24a0d01a2', 'react_69279d65', 'like', '2025-11-26 16:37:57');

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
  KEY `id_usuario` (`id_usuario`),
  KEY `idx_token` (`token`),
  KEY `idx_expiracao` (`data_expiracao`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `fk_comentario_pai` FOREIGN KEY (`comentario_pai_id`) REFERENCES `comentarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_login_comentarios` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `configuracoes_usuario`
--
ALTER TABLE `configuracoes_usuario`
  ADD CONSTRAINT `fk_config_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`id_remetente`) REFERENCES `login` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`id_destinatario`) REFERENCES `login` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `post_hashtags`
--
ALTER TABLE `post_hashtags`
  ADD CONSTRAINT `post_hashtags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `postagens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_hashtags_ibfk_2` FOREIGN KEY (`hashtag_id`) REFERENCES `hashtags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reacoes`
--
ALTER TABLE `reacoes`
  ADD CONSTRAINT `fk_login_reacoes` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  ADD CONSTRAINT `recuperacao_senha_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql103.infinityfree.com
-- Generation Time: Nov 27, 2025 at 06:21 AM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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

CREATE TABLE `codigos_verificacao` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `codigo` varchar(6) NOT NULL,
  `data_criacao` datetime NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT 0,
  `tentativas` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `codigos_verificacao`
--

INSERT INTO `codigos_verificacao` (`id`, `email`, `codigo`, `data_criacao`, `data_expiracao`, `usado`, `tentativas`) VALUES
(8, 'ooi@gmail.com', '900456', '2025-11-27 05:28:54', '2025-11-27 05:38:54', 0, 0),
(9, 'dszgsdh@gmail.com', '995492', '2025-11-27 05:38:15', '2025-11-27 05:48:15', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `comentarios`
--

CREATE TABLE `comentarios` (
  `id_conteudo` varchar(20) NOT NULL,
  `id_usuario` varchar(36) NOT NULL,
  `id` varchar(20) NOT NULL,
  `tipo_conteudo` enum('postagem','comentario') NOT NULL,
  `texto` text NOT NULL,
  `data` datetime NOT NULL,
  `data_edit` datetime DEFAULT NULL,
  `comentario_pai_id` varchar(20) DEFAULT NULL,
  `nivel` int(11) DEFAULT 0
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
-- Table structure for table `hashtags`
--

CREATE TABLE `hashtags` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `contagem` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `id` varchar(36) NOT NULL
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
('laistestee', 'lais.ferro00@gmail.com', '$2y$10$ur/nznqLABk2jT2.N7Gaw.23wp4q1G153BoRd0TEH0I6tzEn.UXBO', 's52zJxe-ExR'),
('laisteste', 'lais.ferro05@gmail.com', '$2y$10$ikCDobZAR6q0aypwdQH4buypOFS5V09wUy7jLmSOISBWnvJA7Uqae', 'uM8O5YT-tmB'),
('ok', 'ok@gmail.com', '$2y$10$sab6Q36q00H6eM6dkEuNcurMLPYL3NTZMS.aECQ.rE1HaTK/oT5eK', 'VQuHX6G-x7G'),
('Cleiton', 'cleiton@gmail.com', '$2y$10$E7RELwGU88hXpYlqaRieauukbG9VIHC3i7ni8vuIA.q.bWnXSodiy', 'zHamNyo-1Tf');

-- --------------------------------------------------------

--
-- Table structure for table `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `id_remetente` varchar(36) NOT NULL,
  `id_destinatario` varchar(36) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` datetime DEFAULT current_timestamp(),
  `lida` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `obras`
--

CREATE TABLE `obras` (
  `id` varchar(100) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `autor` varchar(100) DEFAULT NULL,
  `tipo` enum('livro','filme','serie','arte','musica') DEFAULT NULL,
  `ano_lancamento` year(4) DEFAULT NULL,
  `descricao` mediumtext DEFAULT NULL,
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `obras`
--

INSERT INTO `obras` (`id`, `titulo`, `autor`, `tipo`, `ano_lancamento`, `descricao`, `data_criacao`) VALUES
('27qC3KK5wjQzyNenY53fep', 'F1', 'Hans Zimmer', 'musica', 2025, '', '2025-10-17 10:40:19'),
('933260', 'A SubstÃ¢ncia', '', 'filme', 2024, 'Uma estrela em declÃ­nio descobre uma substÃ¢ncia misteriosa que lhe permite criar uma versÃ£o mais jovem e perfeita de si mesma. No entanto, o que parece ser uma soluÃ§Ã£o milagrosa para recuperar sua glÃ³ria logo se transforma em um pesadelo perturbador quando as consequÃªncias desta decisÃ£o comeÃ§am a se manifestar de formas sombrias e irreversÃ­veis.', '2025-10-17 10:59:41'),
('9799', 'Velozes e Furiosos', '', 'filme', 2001, 'Um investigador da polÃ­cia se infiltra em uma turma de rachas suspeita de roubar caminhÃµes, mas acaba apaixonando-se pela irmÃ£ do lÃ­der.', '2025-10-17 10:51:49');

-- --------------------------------------------------------

--
-- Table structure for table `perfil`
--

CREATE TABLE `perfil` (
  `nomexi` varchar(50) NOT NULL,
  `caminho` varchar(255) NOT NULL,
  `id` varchar(36) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `descri` varchar(500) NOT NULL,
  `data_perf` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perfil`
--

INSERT INTO `perfil` (`nomexi`, `caminho`, `id`, `foto`, `descri`, `data_perf`) VALUES
('suzane01', 'images/68f246f362d3f.png', '7WhmT6p-Jmm', '68f246f362d3f.png', '15 anos', '2025-10-17 10:38:59'),
('lucas', 'images/68f2499c842dc.png', '1NYz7Fz-kGq', '68f2499c842dc.png', '17 anos', '2025-10-17 10:50:20'),
('cleiton', 'images/68f24b7e61c7c.png', 'zHamNyo-1Tf', '68f24b7e61c7c.png', '50 anos', '2025-10-17 10:58:22'),
('ooooi', 'images/691ceb8e7a19d.png', 'MxciYvJ-HgM', '691ceb8e7a19d.png', 'sim', '2025-11-18 18:56:30'),
('lais', 'images/692204e95204d.jpg', 's52zJxe-ExR', '692204e95204d.jpg', 'estudante da etec', '2025-11-22 15:46:01'),
('lais', 'images/692205696ca99.jpg', 'uM8O5YT-tmB', '692205696ca99.jpg', 'testee', '2025-11-22 15:48:09'),
('ok', 'images/69220731c4eff.jpg', 'VQuHX6G-x7G', '69220731c4eff.jpg', 'etec', '2025-11-22 15:55:45'),
('ok', 'images/6922088ccbb15.jpg', 'VQuHX6G-x7G', '6922088ccbb15.jpg', 'etec', '2025-11-22 16:01:32'),
('ooooii', 'images/69279d3d891f5.png', 'jkg6Jp6-vzf', '69279d3d891f5.png', 'sim', '2025-11-26 21:37:17');

-- --------------------------------------------------------

--
-- Table structure for table `postagens`
--

CREATE TABLE `postagens` (
  `id_usuario` varchar(36) NOT NULL,
  `id_obra` varchar(100) DEFAULT NULL,
  `id` varchar(20) NOT NULL,
  `titulo` varchar(40) NOT NULL,
  `texto` mediumtext NOT NULL,
  `data_post` datetime NOT NULL,
  `data_edit` datetime DEFAULT NULL
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

CREATE TABLE `post_hashtags` (
  `id` int(11) NOT NULL,
  `post_id` varchar(20) NOT NULL,
  `hashtag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reacoes`
--

CREATE TABLE `reacoes` (
  `id_usuario` varchar(36) NOT NULL,
  `tipo_conteudo` enum('postagem','comentario') NOT NULL,
  `id_conteudo` varchar(20) NOT NULL,
  `id` varchar(14) NOT NULL,
  `tipo` enum('like','dislike') NOT NULL,
  `data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reacoes`
--

INSERT INTO `reacoes` (`id_usuario`, `tipo_conteudo`, `id_conteudo`, `id`, `tipo`, `data`) VALUES
('7WhmT6p-Jmm', 'postagem', '68f247448db17', '68f2474d4c856', 'dislike', '2025-10-17 06:40:30'),
('1NYz7Fz-kGq', 'postagem', '68f249f576c20', '68f249fd52c4e', 'dislike', '2025-10-17 06:51:58'),
('zHamNyo-1Tf', 'postagem', '68f24bcd3dbee', '68f24bd2d34c7', 'dislike', '2025-10-17 06:59:47'),
('jkg6Jp6-vzf', 'postagem', '68f24bcd3dbee', '69279d55dbc02', 'dislike', '2025-11-27 01:44:43'),
('jkg6Jp6-vzf', 'comentario', 'comment_68f24a0d01a2', 'react_69279d65', 'like', '2025-11-26 16:37:57');

-- --------------------------------------------------------

--
-- Table structure for table `recuperacao_senha`
--

CREATE TABLE `recuperacao_senha` (
  `id` int(11) NOT NULL,
  `id_usuario` varchar(36) NOT NULL,
  `token` varchar(64) NOT NULL,
  `data_criacao` datetime NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recuperacao_senha`
--

INSERT INTO `recuperacao_senha` (`id`, `id_usuario`, `token`, `data_criacao`, `data_expiracao`, `usado`) VALUES
(5, 'jkg6Jp6-vzf', '176321', '2025-11-27 05:01:50', '2025-11-27 06:01:50', 0),
(6, 'MxciYvJ-HgM', '367180', '2025-11-27 05:05:53', '2025-11-27 06:05:53', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `codigos_verificacao`
--
ALTER TABLE `codigos_verificacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_codigo` (`codigo`),
  ADD KEY `idx_expiracao` (`data_expiracao`);

--
-- Indexes for table `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_login_comentarios` (`id_usuario`),
  ADD KEY `idx_comentario_pai` (`comentario_pai_id`),
  ADD KEY `idx_nivel` (`nivel`);

--
-- Indexes for table `hashtags`
--
ALTER TABLE `hashtags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_remetente` (`id_remetente`),
  ADD KEY `id_destinatario` (`id_destinatario`);

--
-- Indexes for table `obras`
--
ALTER TABLE `obras`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `perfil`
--
ALTER TABLE `perfil`
  ADD KEY `idx_login` (`id`);

--
-- Indexes for table `postagens`
--
ALTER TABLE `postagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_login_postagens` (`id_usuario`),
  ADD KEY `fk_obras_postagens` (`id_obra`);

--
-- Indexes for table `post_hashtags`
--
ALTER TABLE `post_hashtags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `hashtag_id` (`hashtag_id`);

--
-- Indexes for table `reacoes`
--
ALTER TABLE `reacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`id_usuario`),
  ADD KEY `idx_conteudo` (`id_conteudo`);

--
-- Indexes for table `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expiracao` (`data_expiracao`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `codigos_verificacao`
--
ALTER TABLE `codigos_verificacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `hashtags`
--
ALTER TABLE `hashtags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_hashtags`
--
ALTER TABLE `post_hashtags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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

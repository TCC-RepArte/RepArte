-- Adicionar colunas para "Lembre de mim" e "2FA" na tabela login

-- Adicionar coluna para armazenar token de "lembre de mim"
ALTER TABLE `login` 
ADD COLUMN `remember_token` VARCHAR(64) DEFAULT NULL AFTER `senha`,
ADD COLUMN `remember_token_expira` DATETIME DEFAULT NULL AFTER `remember_token`;

-- Adicionar coluna para 2FA
ALTER TABLE `login`
ADD COLUMN `2fa_ativo` TINYINT(1) DEFAULT 0 AFTER `remember_token_expira`,
ADD COLUMN `2fa_secret` VARCHAR(32) DEFAULT NULL AFTER `2fa_ativo`;

-- Criar índices para melhor performance
ALTER TABLE `login`
ADD INDEX `idx_remember_token` (`remember_token`),
ADD INDEX `idx_2fa_ativo` (`2fa_ativo`);

-- Criar tabela para armazenar códigos 2FA temporários
CREATE TABLE IF NOT EXISTS `codigos_2fa` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_usuario` VARCHAR(36) NOT NULL,
  `codigo` VARCHAR(6) NOT NULL,
  `data_criacao` DATETIME NOT NULL,
  `data_expiracao` DATETIME NOT NULL,
  `usado` TINYINT(1) DEFAULT 0,
  `tentativas` INT DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`id_usuario`),
  KEY `idx_codigo` (`codigo`),
  KEY `idx_expiracao` (`data_expiracao`),
  CONSTRAINT `fk_2fa_login` FOREIGN KEY (`id_usuario`) REFERENCES `login` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

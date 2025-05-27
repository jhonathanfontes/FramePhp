-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS framephp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar o banco de dados
USE framephp;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    remember_token VARCHAR(100) NULL,
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de tokens de redefinição de senha
CREATE TABLE IF NOT EXISTS password_resets (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX password_resets_email_index (email)
) ENGINE=InnoDB;

-- Inserir usuário administrador padrão
-- Senha: senha123 (em produção, use password_hash)
INSERT INTO users (name, email, password, role) VALUES 
('Administrador', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

CREATE TABLE `cad_usuario` (
  `id_usuario` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `use_nome` VARCHAR(100) NOT NULL,
  `use_apelido` VARCHAR(50) DEFAULT NULL,
  `use_username` VARCHAR(50) NOT NULL UNIQUE,
  `use_password` VARCHAR(255) NOT NULL,
  `use_email` VARCHAR(100) NOT NULL UNIQUE,
  `use_telefone` VARCHAR(20) DEFAULT NULL,
  `use_avatar` VARCHAR(255) DEFAULT NULL,
  `use_sexo` ENUM('M', 'F', 'O') DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `permissao_id` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `created_user_id` INT UNSIGNED DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_user_id` INT UNSIGNED DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL,
  `deleted_user_id` INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  INDEX `idx_permissao` (`permissao_id`),
  INDEX `idx_created_user` (`created_user_id`),
  INDEX `idx_updated_user` (`updated_user_id`),
  INDEX `idx_deleted_user` (`deleted_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `password_resets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  UNIQUE KEY `uniq_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

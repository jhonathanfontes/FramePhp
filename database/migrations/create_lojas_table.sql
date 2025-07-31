
CREATE TABLE IF NOT EXISTS lojas (
    id_loja INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    nome_loja VARCHAR(255) NOT NULL,
    dominio VARCHAR(255),
    slug VARCHAR(255) NOT NULL,
    descricao TEXT,
    logo VARCHAR(500),
    banner VARCHAR(500),
    tema_id INT DEFAULT 1,
    configuracoes_json JSON,
    status ENUM('ativo', 'inativo', 'manutencao') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (empresa_id) REFERENCES empresas(id_empresa) ON DELETE CASCADE,
    INDEX idx_empresa (empresa_id),
    INDEX idx_dominio (dominio),
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    UNIQUE KEY unique_slug (slug),
    UNIQUE KEY unique_dominio (dominio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

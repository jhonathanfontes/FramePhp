
-- Tabela de Categorias
CREATE TABLE IF NOT EXISTS cad_categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    cat_nome VARCHAR(100) NOT NULL,
    cat_descricao TEXT,
    cat_imagem VARCHAR(255),
    cat_slug VARCHAR(100) UNIQUE,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    created_user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_user_id INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_user_id INT,
    deleted_at TIMESTAMP NULL
);

-- Tabela de Fabricantes
CREATE TABLE IF NOT EXISTS cad_fabricante (
    id_fabricante INT AUTO_INCREMENT PRIMARY KEY,
    fab_nome VARCHAR(100) NOT NULL,
    fab_descricao TEXT,
    fab_logo VARCHAR(255),
    fab_site VARCHAR(255),
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    created_user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_user_id INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_user_id INT,
    deleted_at TIMESTAMP NULL
);

-- Tabela de Produtos
CREATE TABLE IF NOT EXISTS cad_produto (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    pro_nome VARCHAR(200) NOT NULL,
    pro_descricao TEXT,
    pro_preco DECIMAL(10,2) NOT NULL,
    pro_preco_promocional DECIMAL(10,2) NULL,
    pro_estoque INT DEFAULT 0,
    pro_sku VARCHAR(50) UNIQUE,
    pro_imagem VARCHAR(255),
    pro_peso VARCHAR(20),
    pro_dimensoes VARCHAR(50),
    pro_tags TEXT,
    pro_destaque BOOLEAN DEFAULT FALSE,
    categoria_id INT,
    fabricante_id INT,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    created_user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_user_id INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_user_id INT,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (categoria_id) REFERENCES cad_categoria(id_categoria),
    FOREIGN KEY (fabricante_id) REFERENCES cad_fabricante(id_fabricante)
);

-- Tabela de Pedidos
CREATE TABLE IF NOT EXISTS cad_pedido (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    ped_numero VARCHAR(20) UNIQUE NOT NULL,
    cliente_id INT NOT NULL,
    ped_total DECIMAL(10,2) NOT NULL,
    ped_desconto DECIMAL(10,2) DEFAULT 0,
    ped_frete DECIMAL(10,2) DEFAULT 0,
    ped_status ENUM('pendente', 'confirmado', 'preparando', 'enviado', 'entregue', 'cancelado') DEFAULT 'pendente',
    ped_observacoes TEXT,
    ped_data_entrega DATE,
    endereco_entrega TEXT,
    created_user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_user_id INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_user_id INT,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (cliente_id) REFERENCES cad_pessoa(id_pessoa)
);

-- Tabela de Itens do Pedido
CREATE TABLE IF NOT EXISTS cad_pedido_item (
    id_pedido_item INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES cad_pedido(id_pedido),
    FOREIGN KEY (produto_id) REFERENCES cad_produto(id_produto)
);

-- Inserir dados de exemplo
INSERT INTO cad_categoria (cat_nome, cat_descricao, cat_slug) VALUES
('Eletrônicos', 'Produtos eletrônicos em geral', 'eletronicos'),
('Roupas', 'Vestuário masculino e feminino', 'roupas'),
('Casa e Jardim', 'Produtos para casa e jardim', 'casa-jardim'),
('Esportes', 'Artigos esportivos', 'esportes');

INSERT INTO cad_fabricante (fab_nome, fab_descricao) VALUES
('Samsung', 'Fabricante de eletrônicos'),
('Nike', 'Marca esportiva'),
('Apple', 'Tecnologia e inovação'),
('Adidas', 'Artigos esportivos');

INSERT INTO cad_produto (pro_nome, pro_descricao, pro_preco, pro_estoque, pro_sku, categoria_id, fabricante_id, pro_destaque) VALUES
('Smartphone Galaxy S21', 'Smartphone Samsung com 128GB de armazenamento', 1299.99, 50, 'SAMS21-128', 1, 1, TRUE),
('Tênis Nike Air Max', 'Tênis esportivo Nike Air Max', 299.99, 30, 'NIKE-AIRMAX', 4, 2, TRUE),
('iPhone 13', 'iPhone 13 com 128GB', 2999.99, 25, 'IPH13-128', 1, 3, TRUE),
('Tênis Adidas Ultraboost', 'Tênis para corrida Adidas', 399.99, 40, 'ADI-ULTRA', 4, 4, FALSE);

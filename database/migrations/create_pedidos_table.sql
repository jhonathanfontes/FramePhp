
CREATE TABLE IF NOT EXISTS pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    loja_id INT NOT NULL,
    cliente_id INT,
    numero_pedido VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pendente', 'confirmado', 'preparando', 'enviado', 'entregue', 'cancelado') DEFAULT 'pendente',
    valor_produtos DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    valor_frete DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    valor_desconto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    valor_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    forma_pagamento ENUM('cartao_credito', 'cartao_debito', 'pix', 'boleto', 'dinheiro') NOT NULL,
    status_pagamento ENUM('pendente', 'pago', 'cancelado', 'estornado') DEFAULT 'pendente',
    observacoes TEXT,
    endereco_entrega_json JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (loja_id) REFERENCES lojas(id_loja) ON DELETE CASCADE,
    INDEX idx_loja (loja_id),
    INDEX idx_cliente (cliente_id),
    INDEX idx_numero_pedido (numero_pedido),
    INDEX idx_status (status),
    INDEX idx_status_pagamento (status_pagamento),
    INDEX idx_data_criacao (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

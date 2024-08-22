CREATE DATABASE apirestfull;

USE apirestfull;

CREATE TABLE carnes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    valor_total DECIMAL(10, 2) NOT NULL,
    qtd_parcelas INT NOT NULL,
    data_primeiro_vencimento DATE NOT NULL,
    periodicidade VARCHAR(10) NOT NULL,
    valor_entrada DECIMAL(10, 2) DEFAULT 0
);

-- database.sql
-- Cole isso no seu phpMyAdmin ou SGBD preferido para inciar seu banco

-- 1. Cria o banco se não existir
CREATE DATABASE IF NOT EXISTS projetogastos;

-- 2. Seleciona o banco correto para manipular as tabelas
USE projetogastos;

-- 3. Cria a tabela 'gastos' seguindo os datatypes corretos
CREATE TABLE IF NOT EXISTS gastos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(255) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    parcela VARCHAR(50),
    pagamento VARCHAR(100),
    instituicao VARCHAR(100),
    tipo VARCHAR(100),
    classificacao VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

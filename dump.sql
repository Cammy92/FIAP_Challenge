-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS fiap;

-- Seleciona o banco de dados
USE fiap;

-- Criação da tabela de Alunos
CREATE TABLE IF NOT EXISTS alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    data_nascimento DATE NOT NULL,
    usuario VARCHAR(100) NOT NULL UNIQUE  -- nickname do aluno, único
);

-- Criação da tabela de Turmas
CREATE TABLE IF NOT EXISTS turmas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    tipo VARCHAR(50) NOT NULL
);

-- Criação da tabela de Matrículas
CREATE TABLE IF NOT EXISTS matriculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    turma_id INT NOT NULL,
    data_matricula TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    FOREIGN KEY (turma_id) REFERENCES turmas(id) ON DELETE CASCADE,
    UNIQUE(aluno_id, turma_id) -- Não permite matrícula duplicada
);

-- Criação da tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,        -- Senha armazenada normalmente
    permissao VARCHAR(255) NOT NULL      -- Permissão diretamente na tabela de usuários
);

-- Inserir um usuário de exemplo
INSERT INTO usuarios (usuario, senha, permissao) VALUES ('admin', 'senha123', 'admin');

-- Inserir Alunos
INSERT INTO alunos (nome, data_nascimento) VALUES
('Sara Melo', '1999-11-16'),
('Maria Helena Ferreira', '1967-07-16'),
('Leonardo Apoteóse', '1990-01-01'),
('César de Castro', '1985-05-20'),
('Laura Correia', '1978-07-22');

-- Inserir Turmas
INSERT INTO turmas (nome, descricao, tipo) VALUES
('Programação em Python', 'Curso de Introdução à Programação com Python', 'Presencial'),
('Desenvolvimento Web com JavaScript', 'Curso de Desenvolvimento Web usando JavaScript', 'Online'),
('Estruturas de Dados em C', 'Curso de Estruturas de Dados utilizando C', 'Presencial'),
('Desenvolvimento Mobile com React Native', 'Curso de Desenvolvimento de Aplicativos Mobile com React Native', 'Online'),
('Programação Orientada a Objetos com Java', 'Curso de Programação Orientada a Objetos utilizando Java', 'Presencial'),
('Fundamentos de Algoritmos', 'Curso básico de algoritmos e resolução de problemas', 'Online'),
('Banco de Dados e SQL', 'Curso de Banco de Dados, SQL e Modelagem de Dados', 'Presencial'),
('Desenvolvimento Web com Node.js', 'Curso de Desenvolvimento Backend com Node.js', 'Online'),
('Inteligência Artificial com Python', 'Curso de Inteligência Artificial e Machine Learning utilizando Python', 'Presencial'),
('Desenvolvimento de Games com Unity', 'Curso de Desenvolvimento de Jogos utilizando Unity', 'Online');

-- Inserir Matrículas
INSERT INTO matriculas (aluno_id, turma_id) VALUES
(1, 1),  -- Sara Melo se matriculou em Programação em Python
(2, 2);  -- Maria Helena Ferreira se matriculou em Desenvolvimento Web com JavaScript

-- ============================================================
-- SISTEMA DE GERENCIAMENTO DE BIBLIOTECA — BiblioSys
-- Banco de Dados : biblioteca_db
-- PHP            : 8.1+
-- MySQL          : 8.0+ / MariaDB 10.6+
--
-- COMO USAR:
--   1. Abra o phpMyAdmin ou seu cliente MySQL preferido
--   2. Execute este script inteiro
--   3. Configure config/database.php com suas credenciais
--   4. Acesse o sistema e faça login com os usuários abaixo
--
-- USUÁRIOS PADRÃO:
--   Admin  → admin@biblioteca.com  / admin123
--   Leitor → joao@email.com        / user123
--   Leitor → maria@email.com       / user123
-- ============================================================

CREATE DATABASE IF NOT EXISTS biblioteca_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE biblioteca_db;

-- ── Desabilita checagem de FK durante criação ────────────────
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS emprestimos;
DROP TABLE IF EXISTS livros;
DROP TABLE IF EXISTS autores;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- TABELA: categorias
-- ------------------------------------------------------------
CREATE TABLE categorias (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(100)  NOT NULL,
    descricao     TEXT,
    criado_em     DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- TABELA: autores
-- ------------------------------------------------------------
CREATE TABLE autores (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(150) NOT NULL,
    email         VARCHAR(150),
    nacionalidade VARCHAR(100),
    criado_em     DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- TABELA: livros
-- ------------------------------------------------------------
CREATE TABLE livros (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo         VARCHAR(200) NOT NULL,
    isbn           VARCHAR(20)  UNIQUE,
    ano_publicacao YEAR,
    quantidade     INT UNSIGNED DEFAULT 1,
    categoria_id   INT UNSIGNED NOT NULL,
    autor_id       INT UNSIGNED NOT NULL,
    criado_em      DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_livro_categoria FOREIGN KEY (categoria_id)
        REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_livro_autor FOREIGN KEY (autor_id)
        REFERENCES autores(id)    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- TABELA: usuarios  (com autenticação e roles)
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(150) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    senha         VARCHAR(255) NOT NULL,
    role          ENUM('admin','usuario') NOT NULL DEFAULT 'usuario',
    telefone      VARCHAR(20),
    status        ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo',
    criado_em     DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- TABELA: emprestimos
-- ------------------------------------------------------------
CREATE TABLE emprestimos (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    livro_id        INT UNSIGNED NOT NULL,
    usuario_id      INT UNSIGNED NOT NULL,
    data_emprestimo DATE NOT NULL DEFAULT (CURRENT_DATE),
    data_devolucao  DATE NOT NULL,
    devolvido       TINYINT(1)   NOT NULL DEFAULT 0,
    observacao      TEXT,
    criado_em       DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_emp_livro   FOREIGN KEY (livro_id)   REFERENCES livros(id)   ON DELETE RESTRICT,
    CONSTRAINT fk_emp_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DADOS DE EXEMPLO
-- ============================================================

-- Categorias
INSERT INTO categorias (nome, descricao) VALUES
    ('Ficção Científica', 'Obras que exploram ciência e tecnologia futuristas'),
    ('Romance',           'Histórias de relacionamentos e sentimentos'),
    ('Programação',       'Livros técnicos sobre desenvolvimento de software'),
    ('História',          'Obras sobre eventos e períodos históricos'),
    ('Filosofia',         'Obras de pensamento filosófico');

-- Autores
INSERT INTO autores (nome, email, nacionalidade) VALUES
    ('Isaac Asimov',       'asimov@exemplo.com',     'Americano'),
    ('Machado de Assis',   'machado@exemplo.com',    'Brasileiro'),
    ('Robert C. Martin',   'uncle.bob@exemplo.com',  'Americano'),
    ('Yuval Noah Harari',  'harari@exemplo.com',     'Israelense'),
    ('Fiódor Dostoiévski', 'dostoievski@exemplo.com','Russo');

-- Livros
INSERT INTO livros (titulo, isbn, ano_publicacao, quantidade, categoria_id, autor_id) VALUES
    ('Fundação',        '978-0553293357', 1951, 3, 1, 1),
    ('Dom Casmurro',    '978-8535902778', 1899, 5, 2, 2),
    ('Código Limpo',    '978-0132350884', 2008, 2, 3, 3),
    ('Sapiens',         '978-0062316097', 2011, 4, 4, 4),
    ('Crime e Castigo', '978-0486415871', 1866, 2, 5, 5);

-- ============================================================
-- USUÁRIOS PADRÃO
-- Senhas geradas com password_hash(..., PASSWORD_BCRYPT)
--   admin@biblioteca.com → admin123
--   joao@email.com       → user123
--   maria@email.com      → user123
-- ============================================================
INSERT INTO usuarios (nome, email, senha, role, telefone, status) VALUES
    (
        'Administrador',
        'admin@biblioteca.com',
        '$2b$12$yfdBprgovNvRlOruK.2Hw..QGsBkstyHSQ5TTS0mLCG7hcOIyh1kq',
        'admin',
        '',
        'ativo'
    ),
    (
        'João Silva',
        'joao@email.com',
        '$2b$12$dfPGGOrxMjPC2rsieStdQOFnL1JNFA4DZnIwgj4223SN6lsKCMiAW',
        'usuario',
        '(11) 91234-5678',
        'ativo'
    ),
    (
        'Maria Costa',
        'maria@email.com',
        '$2b$12$dfPGGOrxMjPC2rsieStdQOFnL1JNFA4DZnIwgj4223SN6lsKCMiAW',
        'usuario',
        '(21) 98765-4321',
        'ativo'
    );

-- ============================================================
-- SISTEMA DE GERENCIAMENTO DE BIBLIOTECA
-- Banco de Dados: biblioteca_db  (versão com autenticação)
-- ============================================================

CREATE DATABASE IF NOT EXISTS biblioteca_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE biblioteca_db;

-- ------------------------------------------------------------
-- TABELA: categorias
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categorias (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(100) NOT NULL,
    descricao     TEXT,
    criado_em     DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- TABELA: autores
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS autores (
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
CREATE TABLE IF NOT EXISTS livros (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo         VARCHAR(200) NOT NULL,
    isbn           VARCHAR(20) UNIQUE,
    ano_publicacao YEAR,
    quantidade     INT UNSIGNED DEFAULT 1,
    categoria_id   INT UNSIGNED NOT NULL,
    autor_id       INT UNSIGNED NOT NULL,
    criado_em      DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_livro_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    CONSTRAINT fk_livro_autor     FOREIGN KEY (autor_id)     REFERENCES autores(id)    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- TABELA: usuarios (com autenticação e roles)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(150) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    senha         VARCHAR(255) NOT NULL,
    role          ENUM('admin','usuario') NOT NULL DEFAULT 'usuario',
    telefone      VARCHAR(20),
    status        ENUM('ativo','inativo') DEFAULT 'ativo',
    criado_em     DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- TABELA: emprestimos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS emprestimos (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    livro_id        INT UNSIGNED NOT NULL,
    usuario_id      INT UNSIGNED NOT NULL,
    data_emprestimo DATE NOT NULL DEFAULT (CURRENT_DATE),
    data_devolucao  DATE NOT NULL,
    devolvido       TINYINT(1) DEFAULT 0,
    observacao      TEXT,
    criado_em       DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_emp_livro   FOREIGN KEY (livro_id)   REFERENCES livros(id)   ON DELETE RESTRICT,
    CONSTRAINT fk_emp_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- DADOS DE EXEMPLO
-- ------------------------------------------------------------

INSERT INTO categorias (nome, descricao) VALUES
    ('Ficção Científica', 'Obras que exploram ciência e tecnologia futuristas'),
    ('Romance',           'Histórias de relacionamentos e sentimentos'),
    ('Programação',       'Livros técnicos sobre desenvolvimento de software'),
    ('História',          'Obras sobre eventos e períodos históricos'),
    ('Filosofia',         'Obras de pensamento filosófico');

INSERT INTO autores (nome, email, nacionalidade) VALUES
    ('Isaac Asimov',       'asimov@exemplo.com',     'Americano'),
    ('Machado de Assis',   'machado@exemplo.com',    'Brasileiro'),
    ('Robert C. Martin',   'uncle.bob@exemplo.com',  'Americano'),
    ('Yuval Noah Harari',  'harari@exemplo.com',     'Israelense'),
    ('Fiódor Dostoiévski', 'dostoievski@exemplo.com','Russo');

INSERT INTO livros (titulo, isbn, ano_publicacao, quantidade, categoria_id, autor_id) VALUES
    ('Fundação',       '978-0553293357', 1951, 3, 1, 1),
    ('Dom Casmurro',   '978-8535902778', 1899, 5, 2, 2),
    ('Código Limpo',   '978-0132350884', 2008, 2, 3, 3),
    ('Sapiens',        '978-0062316097', 2011, 4, 4, 4),
    ('Crime e Castigo','978-0486415871', 1866, 2, 5, 5);

-- Usuário admin padrão  →  senha: admin123
-- Usuário comum padrão  →  senha: user123
-- (hashes gerados pelo setup.php — execute-o após importar este SQL)

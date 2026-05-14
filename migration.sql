-- ============================================================
-- MIGRATION: Adiciona sistema de autenticação e roles
-- Execute este arquivo se já tiver o banco criado.
-- Se for instalação nova, use database.sql (já inclui tudo).
-- ============================================================

USE biblioteca_db;

-- Adiciona colunas de autenticação na tabela usuarios
ALTER TABLE usuarios
    ADD COLUMN senha VARCHAR(255) NOT NULL DEFAULT '' AFTER email,
    ADD COLUMN role  ENUM('admin','usuario') NOT NULL DEFAULT 'usuario' AFTER senha;

-- Índice para busca rápida por email (login)
ALTER TABLE usuarios ADD INDEX idx_email (email);

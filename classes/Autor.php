<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Classe Autor
 * Representa um autor de livros na biblioteca.
 */
class Autor {
    private PDO $db;

    public int    $id;
    public string $nome;
    public string $email;
    public string $nacionalidade;

    public function __construct() {
        $this->db = getConexao();
    }

    /**
     * Cadastra um novo autor.
     */
    public function criar(string $nome, string $email, string $nacionalidade): bool {
        $sql = "INSERT INTO autores (nome, email, nacionalidade) VALUES (:nome, :email, :nacionalidade)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'          => trim($nome),
            ':email'         => trim($email),
            ':nacionalidade' => trim($nacionalidade),
        ]);
    }

    /**
     * Retorna todos os autores ordenados por nome.
     */
    public function listarTodos(): array {
        $stmt = $this->db->query("SELECT * FROM autores ORDER BY nome");
        return $stmt->fetchAll();
    }

    /**
     * Busca um autor pelo ID.
     */
    public function buscarPorId(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM autores WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Atualiza os dados de um autor existente.
     */
    public function atualizar(int $id, string $nome, string $email, string $nacionalidade): bool {
        $sql = "UPDATE autores SET nome = :nome, email = :email, nacionalidade = :nacionalidade WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'          => trim($nome),
            ':email'         => trim($email),
            ':nacionalidade' => trim($nacionalidade),
            ':id'            => $id,
        ]);
    }

    /**
     * Remove um autor. Só é possível se não houver livros vinculados.
     */
    public function deletar(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM livros WHERE autor_id = :id");
        $stmt->execute([':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new RuntimeException("Não é possível excluir: existem livros vinculados a este autor.");
        }
        $stmt = $this->db->prepare("DELETE FROM autores WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Conta o total de autores cadastrados.
     */
    public function total(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM autores")->fetchColumn();
    }
}

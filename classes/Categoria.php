<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Classe Categoria
 * Representa uma categoria de livros na biblioteca.
 */
class Categoria {
    private PDO $db;

    public int    $id;
    public string $nome;
    public string $descricao;

    public function __construct() {
        $this->db = getConexao();
    }

    /**
     * Cria uma nova categoria no banco de dados.
     */
    public function criar(string $nome, string $descricao): bool {
        $sql = "INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'      => trim($nome),
            ':descricao' => trim($descricao),
        ]);
    }

    /**
     * Retorna todas as categorias ordenadas por nome.
     */
    public function listarTodas(): array {
        $stmt = $this->db->query("SELECT * FROM categorias ORDER BY nome");
        return $stmt->fetchAll();
    }

    /**
     * Busca uma categoria pelo ID.
     */
    public function buscarPorId(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Atualiza os dados de uma categoria existente.
     */
    public function atualizar(int $id, string $nome, string $descricao): bool {
        $sql = "UPDATE categorias SET nome = :nome, descricao = :descricao WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'      => trim($nome),
            ':descricao' => trim($descricao),
            ':id'        => $id,
        ]);
    }

    /**
     * Remove uma categoria. Só é possível se não houver livros vinculados.
     */
    public function deletar(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM livros WHERE categoria_id = :id");
        $stmt->execute([':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new RuntimeException("Não é possível excluir: existem livros vinculados a esta categoria.");
        }
        $stmt = $this->db->prepare("DELETE FROM categorias WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Conta o total de categorias cadastradas.
     */
    public function total(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
    }
}

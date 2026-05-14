<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Classe Livro
 * Representa um livro do acervo da biblioteca.
 */
class Livro {
    private PDO $db;

    public int    $id;
    public string $titulo;
    public string $isbn;
    public int    $ano_publicacao;
    public int    $quantidade;
    public int    $categoria_id;
    public int    $autor_id;

    public function __construct() {
        $this->db = getConexao();
    }

    /**
     * Cadastra um novo livro no acervo.
     * Regra: quantidade deve ser >= 1.
     */
    public function criar(string $titulo, string $isbn, int $ano, int $quantidade, int $categoriaId, int $autorId): bool {
        if ($quantidade < 1) {
            throw new InvalidArgumentException("A quantidade deve ser pelo menos 1.");
        }
        $sql = "INSERT INTO livros (titulo, isbn, ano_publicacao, quantidade, categoria_id, autor_id)
                VALUES (:titulo, :isbn, :ano, :quantidade, :categoria_id, :autor_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titulo'      => trim($titulo),
            ':isbn'        => trim($isbn),
            ':ano'         => $ano,
            ':quantidade'  => $quantidade,
            ':categoria_id'=> $categoriaId,
            ':autor_id'    => $autorId,
        ]);
    }

    /**
     * Retorna todos os livros com nome do autor e categoria.
     */
    public function listarTodos(): array {
        $sql = "SELECT l.*, a.nome AS autor_nome, c.nome AS categoria_nome
                FROM livros l
                INNER JOIN autores    a ON a.id = l.autor_id
                INNER JOIN categorias c ON c.id = l.categoria_id
                ORDER BY l.titulo";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Busca um livro pelo ID.
     */
    public function buscarPorId(int $id): array|false {
        $sql = "SELECT l.*, a.nome AS autor_nome, c.nome AS categoria_nome
                FROM livros l
                INNER JOIN autores    a ON a.id = l.autor_id
                INNER JOIN categorias c ON c.id = l.categoria_id
                WHERE l.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Atualiza os dados de um livro.
     */
    public function atualizar(int $id, string $titulo, string $isbn, int $ano, int $quantidade, int $categoriaId, int $autorId): bool {
        if ($quantidade < 1) {
            throw new InvalidArgumentException("A quantidade deve ser pelo menos 1.");
        }
        $sql = "UPDATE livros SET titulo = :titulo, isbn = :isbn, ano_publicacao = :ano,
                quantidade = :quantidade, categoria_id = :categoria_id, autor_id = :autor_id
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titulo'      => trim($titulo),
            ':isbn'        => trim($isbn),
            ':ano'         => $ano,
            ':quantidade'  => $quantidade,
            ':categoria_id'=> $categoriaId,
            ':autor_id'    => $autorId,
            ':id'          => $id,
        ]);
    }

    /**
     * Remove um livro. Só é possível se não houver empréstimos ativos.
     */
    public function deletar(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM emprestimos WHERE livro_id = :id AND devolvido = 0");
        $stmt->execute([':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new RuntimeException("Não é possível excluir: o livro possui empréstimos ativos.");
        }
        $stmt = $this->db->prepare("DELETE FROM livros WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Verifica se há exemplares disponíveis para empréstimo.
     */
    public function verificarDisponibilidade(int $id): bool {
        $sql = "SELECT l.quantidade,
                       COUNT(e.id) AS emprestados
                FROM livros l
                LEFT JOIN emprestimos e ON e.livro_id = l.id AND e.devolvido = 0
                WHERE l.id = :id
                GROUP BY l.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row && ($row['quantidade'] - $row['emprestados']) > 0;
    }

    /**
     * Conta o total de livros no acervo.
     */
    public function total(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM livros")->fetchColumn();
    }
}

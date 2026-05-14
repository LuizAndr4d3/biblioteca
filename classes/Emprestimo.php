<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Livro.php';
require_once __DIR__ . '/Usuario.php';

/**
 * Classe Emprestimo
 * Gerencia o ciclo de vida de empréstimos de livros.
 */
class Emprestimo {
    private PDO $db;

    public const PRAZO_PADRAO_DIAS = 14; // Regra: prazo padrão de devolução é 14 dias

    public int    $id;
    public int    $livro_id;
    public int    $usuario_id;
    public string $data_emprestimo;
    public string $data_devolucao;
    public bool   $devolvido;
    public string $observacao;

    public function __construct() {
        $this->db = getConexao();
    }

    /**
     * Registra um novo empréstimo.
     * Regras de negócio:
     *  - Usuário deve estar ativo e dentro do limite de empréstimos.
     *  - Livro deve ter exemplares disponíveis.
     *  - Prazo de devolução não pode ser anterior ao empréstimo.
     */
    public function criar(int $livroId, int $usuarioId, string $dataDevolucao, string $observacao = ''): bool {
        $usuario = new Usuario();
        if (!$usuario->podeEmprestar($usuarioId)) {
            throw new RuntimeException(
                "Usuário não pode realizar empréstimo: verifique o status ou o limite de " .
                Usuario::LIMITE_EMPRESTIMOS . " empréstimos simultâneos."
            );
        }

        $livro = new Livro();
        if (!$livro->verificarDisponibilidade($livroId)) {
            throw new RuntimeException("Não há exemplares disponíveis para este livro.");
        }

        if ($dataDevolucao < date('Y-m-d')) {
            throw new InvalidArgumentException("A data de devolução não pode ser no passado.");
        }

        $sql = "INSERT INTO emprestimos (livro_id, usuario_id, data_emprestimo, data_devolucao, devolvido, observacao)
                VALUES (:livro_id, :usuario_id, CURRENT_DATE, :data_devolucao, 0, :observacao)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':livro_id'       => $livroId,
            ':usuario_id'     => $usuarioId,
            ':data_devolucao' => $dataDevolucao,
            ':observacao'     => trim($observacao),
        ]);
    }

    /**
     * Retorna todos os empréstimos com dados do livro e usuário.
     */
    public function listarTodos(): array {
        $sql = "SELECT e.*,
                       l.titulo      AS livro_titulo,
                       u.nome        AS usuario_nome,
                       CASE WHEN e.devolvido = 0 AND e.data_devolucao < CURRENT_DATE THEN 1 ELSE 0 END AS atrasado
                FROM emprestimos e
                INNER JOIN livros   l ON l.id = e.livro_id
                INNER JOIN usuarios u ON u.id = e.usuario_id
                ORDER BY e.devolvido ASC, e.data_devolucao ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Busca um empréstimo pelo ID.
     */
    public function buscarPorId(int $id): array|false {
        $sql = "SELECT e.*,
                       l.titulo  AS livro_titulo,
                       u.nome    AS usuario_nome
                FROM emprestimos e
                INNER JOIN livros   l ON l.id = e.livro_id
                INNER JOIN usuarios u ON u.id = e.usuario_id
                WHERE e.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Atualiza os dados de um empréstimo (data de devolução e observação).
     */
    public function atualizar(int $id, string $dataDevolucao, string $observacao): bool {
        $sql = "UPDATE emprestimos SET data_devolucao = :data_devolucao, observacao = :observacao WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':data_devolucao' => $dataDevolucao,
            ':observacao'     => trim($observacao),
            ':id'             => $id,
        ]);
    }

    /**
     * Registra a devolução de um livro.
     */
    public function registrarDevolucao(int $id): bool {
        $stmt = $this->db->prepare("UPDATE emprestimos SET devolvido = 1 WHERE id = :id AND devolvido = 0");
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException("Empréstimo não encontrado ou já foi devolvido.");
        }
        return true;
    }

    /**
     * Remove um empréstimo (somente se já devolvido).
     */
    public function deletar(int $id): bool {
        $emp = $this->buscarPorId($id);
        if ($emp && !$emp['devolvido']) {
            throw new RuntimeException("Não é possível excluir um empréstimo ativo. Registre a devolução primeiro.");
        }
        $stmt = $this->db->prepare("DELETE FROM emprestimos WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Retorna empréstimos em atraso.
     */
    public function listarAtrasados(): array {
        $sql = "SELECT e.*,
                       l.titulo  AS livro_titulo,
                       u.nome    AS usuario_nome,
                       u.email   AS usuario_email,
                       DATEDIFF(CURRENT_DATE, e.data_devolucao) AS dias_atraso
                FROM emprestimos e
                INNER JOIN livros   l ON l.id = e.livro_id
                INNER JOIN usuarios u ON u.id = e.usuario_id
                WHERE e.devolvido = 0 AND e.data_devolucao < CURRENT_DATE
                ORDER BY dias_atraso DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Conta o total de empréstimos ativos.
     */
    public function totalAtivos(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM emprestimos WHERE devolvido = 0")->fetchColumn();
    }
}

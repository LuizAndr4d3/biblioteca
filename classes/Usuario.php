<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Classe Usuario
 * Representa um usuário/membro da biblioteca (com autenticação e roles).
 */
class Usuario {
    private PDO $db;

    public const LIMITE_EMPRESTIMOS = 3;

    public function __construct() {
        $this->db = getConexao();
    }

    // ─── AUTENTICAÇÃO ────────────────────────────────────────────

    /**
     * Valida e-mail e senha. Retorna os dados do usuário ou false.
     */
    public function login(string $email, string $senha): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios WHERE email = :email AND status = 'ativo' LIMIT 1"
        );
        $stmt->execute([':email' => strtolower(trim($email))]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return false;
    }

    // ─── CRUD ─────────────────────────────────────────────────────

    /**
     * Cadastra um novo usuário (role 'usuario' por padrão).
     */
    public function criar(string $nome, string $email, string $senha, string $telefone = '', string $role = 'usuario'): bool {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("E-mail inválido.");
        }
        if (strlen($senha) < 6) {
            throw new InvalidArgumentException("A senha deve ter no mínimo 6 caracteres.");
        }
        if (!in_array($role, ['admin', 'usuario'])) {
            throw new InvalidArgumentException("Role inválida.");
        }

        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql  = "INSERT INTO usuarios (nome, email, senha, role, telefone, status)
                 VALUES (:nome, :email, :senha, :role, :telefone, 'ativo')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'     => trim($nome),
            ':email'    => strtolower(trim($email)),
            ':senha'    => $hash,
            ':role'     => $role,
            ':telefone' => trim($telefone),
        ]);
    }

    /**
     * Retorna todos os usuários ordenados por nome.
     */
    public function listarTodos(): array {
        return $this->db->query("SELECT * FROM usuarios ORDER BY nome")->fetchAll();
    }

    /**
     * Busca um usuário pelo ID.
     */
    public function buscarPorId(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Verifica se um e-mail já existe (excluindo um ID específico).
     */
    public function emailExiste(string $email, int $excluirId = 0): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email AND id != :id");
        $stmt->execute([':email' => strtolower(trim($email)), ':id' => $excluirId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Atualiza dados do usuário. Senha só é alterada se fornecida.
     */
    public function atualizar(int $id, string $nome, string $email, string $telefone, string $status, string $role, string $senha = ''): bool {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException("E-mail inválido.");
        if ($this->emailExiste($email, $id)) throw new RuntimeException("E-mail já está em uso.");
        if (!in_array($status, ['ativo', 'inativo'])) throw new InvalidArgumentException("Status inválido.");
        if (!in_array($role, ['admin', 'usuario'])) throw new InvalidArgumentException("Role inválida.");

        if ($senha !== '') {
            if (strlen($senha) < 6) throw new InvalidArgumentException("Senha deve ter mínimo 6 caracteres.");
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql  = "UPDATE usuarios SET nome=:nome, email=:email, telefone=:telefone, status=:status, role=:role, senha=:senha WHERE id=:id";
            $params = [':nome'=>trim($nome),':email'=>strtolower(trim($email)),':telefone'=>trim($telefone),':status'=>$status,':role'=>$role,':senha'=>$hash,':id'=>$id];
        } else {
            $sql  = "UPDATE usuarios SET nome=:nome, email=:email, telefone=:telefone, status=:status, role=:role WHERE id=:id";
            $params = [':nome'=>trim($nome),':email'=>strtolower(trim($email)),':telefone'=>trim($telefone),':status'=>$status,':role'=>$role,':id'=>$id];
        }
        return $this->db->prepare($sql)->execute($params);
    }

    /**
     * Remove um usuário. Só é possível se não tiver empréstimos ativos.
     */
    public function deletar(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM emprestimos WHERE usuario_id = :id AND devolvido = 0");
        $stmt->execute([':id' => $id]);
        if ($stmt->fetchColumn() > 0) throw new RuntimeException("Usuário possui empréstimos ativos.");
        return $this->db->prepare("DELETE FROM usuarios WHERE id = :id")->execute([':id' => $id]);
    }

    /**
     * Verifica se o usuário pode pegar mais livros emprestados.
     */
    public function podeEmprestar(int $id): bool {
        $usuario = $this->buscarPorId($id);
        if (!$usuario || $usuario['status'] !== 'ativo') return false;
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM emprestimos WHERE usuario_id = :id AND devolvido = 0");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() < self::LIMITE_EMPRESTIMOS;
    }

    /**
     * Conta o total de usuários.
     */
    public function total(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    }
}

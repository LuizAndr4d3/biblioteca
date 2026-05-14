<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Usuario.php';
$usu   = new Usuario();
$lista = $usu->listarTodos();
cabecalho('Usuários');
exibirFlash();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <span class="text-muted"><?= count($lista) ?> usuário(s) cadastrado(s)</span>
  <a href="create.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Novo Usuário</a>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>#</th><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Role</th><th>Status</th><th class="text-end">Ações</th></tr></thead>
      <tbody>
      <?php if ($lista): foreach ($lista as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($u['nome']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['telefone']) ?></td>
          <td>
            <?php if ($u['role'] === 'admin'): ?>
              <span class="badge" style="background:#fef3c7;color:#92400e"><i class="bi bi-shield-fill me-1"></i>Admin</span>
            <?php else: ?>
              <span class="badge" style="background:#e0f2fe;color:#075985"><i class="bi bi-person me-1"></i>Leitor</span>
            <?php endif; ?>
          </td>
          <td>
            <span class="badge <?= $u['status'] === 'ativo' ? 'bg-success' : 'bg-secondary' ?>">
              <?= ucfirst($u['status']) ?>
            </span>
          </td>
          <td class="text-end">
            <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
            <a href="delete.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Confirmar exclusão?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Nenhum usuário cadastrado.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php rodape(); ?>

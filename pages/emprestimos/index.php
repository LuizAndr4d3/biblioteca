<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Emprestimo.php';
$emp   = new Emprestimo();
$lista = $emp->listarTodos();
cabecalho('Empréstimos');
exibirFlash();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <span class="text-muted"><?= count($lista) ?> empréstimo(s) registrado(s)</span>
  <a href="create.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Novo Empréstimo</a>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr><th>#</th><th>Livro</th><th>Usuário</th><th>Empréstimo</th><th>Devolução</th><th>Status</th><th class="text-end">Ações</th></tr>
      </thead>
      <tbody>
      <?php if ($lista): foreach ($lista as $e): ?>
        <tr class="<?= (!$e['devolvido'] && $e['atrasado']) ? 'table-danger' : '' ?>">
          <td><?= $e['id'] ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($e['livro_titulo']) ?></td>
          <td><?= htmlspecialchars($e['usuario_nome']) ?></td>
          <td><?= date('d/m/Y', strtotime($e['data_emprestimo'])) ?></td>
          <td><?= date('d/m/Y', strtotime($e['data_devolucao'])) ?></td>
          <td>
            <?php if ($e['devolvido']): ?>
              <span class="badge bg-success">Devolvido</span>
            <?php elseif ($e['atrasado']): ?>
              <span class="badge bg-danger">Em Atraso</span>
            <?php else: ?>
              <span class="badge bg-warning text-dark">Em Aberto</span>
            <?php endif; ?>
          </td>
          <td class="text-end">
            <?php if (!$e['devolvido']): ?>
              <a href="devolver.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-success"
                 onclick="return confirm('Registrar devolução?')" title="Devolver">
                <i class="bi bi-check-circle"></i>
              </a>
              <a href="edit.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
            <?php else: ?>
              <a href="delete.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('Excluir registro?')" title="Excluir">
                <i class="bi bi-trash"></i>
              </a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Nenhum empréstimo registrado.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php rodape(); ?>

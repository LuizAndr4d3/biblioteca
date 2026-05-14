<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Autor.php';
$aut   = new Autor();
$lista = $aut->listarTodos();
cabecalho('Autores');
exibirFlash();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <span class="text-muted"><?= count($lista) ?> autor(es) cadastrado(s)</span>
  <a href="create.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Novo Autor</a>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>#</th><th>Nome</th><th>E-mail</th><th>Nacionalidade</th><th class="text-end">Ações</th></tr></thead>
      <tbody>
      <?php if ($lista): foreach ($lista as $a): ?>
        <tr>
          <td><?= $a['id'] ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($a['nome']) ?></td>
          <td><?= htmlspecialchars($a['email']) ?></td>
          <td><?= htmlspecialchars($a['nacionalidade']) ?></td>
          <td class="text-end">
            <a href="edit.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
            <a href="delete.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Confirmar exclusão?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="5" class="text-center text-muted py-4">Nenhum autor cadastrado.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php rodape(); ?>

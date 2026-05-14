<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Categoria.php';
$cat   = new Categoria();
$lista = $cat->listarTodas();
cabecalho('Categorias');
exibirFlash();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <span class="text-muted"><?= count($lista) ?> categoria(s) cadastrada(s)</span>
  <a href="create.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Nova Categoria</a>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>#</th><th>Nome</th><th>Descrição</th><th>Cadastro</th><th class="text-end">Ações</th></tr></thead>
      <tbody>
      <?php if ($lista): foreach ($lista as $c): ?>
        <tr>
          <td><?= $c['id'] ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($c['nome']) ?></td>
          <td class="text-muted"><?= htmlspecialchars($c['descricao']) ?></td>
          <td><?= date('d/m/Y', strtotime($c['criado_em'])) ?></td>
          <td class="text-end">
            <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
            <a href="delete.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Confirmar exclusão?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma categoria cadastrada.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php rodape(); ?>

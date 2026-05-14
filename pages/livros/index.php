<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Livro.php';
$liv   = new Livro();
$lista = $liv->listarTodos();
cabecalho('Livros');
exibirFlash();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <span class="text-muted"><?= count($lista) ?> livro(s) no acervo</span>
  <a href="create.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Novo Livro</a>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr><th>#</th><th>Título</th><th>Autor</th><th>Categoria</th><th>Ano</th><th>Qtd</th><th class="text-end">Ações</th></tr>
      </thead>
      <tbody>
      <?php if ($lista): foreach ($lista as $l): ?>
        <tr>
          <td><?= $l['id'] ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($l['titulo']) ?></td>
          <td><?= htmlspecialchars($l['autor_nome']) ?></td>
          <td><span class="badge" style="background:#ede9fe;color:#5b21b6"><?= htmlspecialchars($l['categoria_nome']) ?></span></td>
          <td><?= $l['ano_publicacao'] ?></td>
          <td><?= $l['quantidade'] ?></td>
          <td class="text-end">
            <a href="edit.php?id=<?= $l['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
            <a href="delete.php?id=<?= $l['id'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Confirmar exclusão?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Nenhum livro cadastrado.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php rodape(); ?>

<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Emprestimo.php';

$emp  = new Emprestimo();
$id   = (int)($_GET['id'] ?? 0);
$item = $emp->buscarPorId($id);
if (!$item || $item['devolvido']) redirecionar('/biblioteca/pages/emprestimos/index.php', 'Empréstimo não encontrado ou já devolvido.', 'warning');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $emp->atualizar($id, $_POST['data_devolucao'] ?? '', $_POST['observacao'] ?? '');
        redirecionar('/biblioteca/pages/emprestimos/index.php', 'Empréstimo atualizado com sucesso!');
    } catch (Exception $e) { $erro = $e->getMessage(); }
}
cabecalho('Editar Empréstimo');
?>
<div class="card p-4" style="max-width:600px;">
  <?php if (!empty($erro)) alerta('danger', $erro); ?>
  <div class="mb-4 p-3 rounded" style="background:#f8f9ff;border:1px solid #e0e7ff">
    <div class="small text-muted mb-1">Detalhes do empréstimo</div>
    <strong><?= htmlspecialchars($item['livro_titulo']) ?></strong> → <?= htmlspecialchars($item['usuario_nome']) ?><br>
    <small class="text-muted">Emprestado em: <?= date('d/m/Y', strtotime($item['data_emprestimo'])) ?></small>
  </div>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Nova Data de Devolução *</label>
      <input type="date" name="data_devolucao" class="form-control" required
             value="<?= $_POST['data_devolucao'] ?? $item['data_devolucao'] ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Observação</label>
      <textarea name="observacao" class="form-control" rows="2"><?= htmlspecialchars($_POST['observacao'] ?? $item['observacao']) ?></textarea>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Atualizar</button>
      <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div>
<?php rodape(); ?>

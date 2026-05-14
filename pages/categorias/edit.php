<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Categoria.php';

$cat  = new Categoria();
$id   = (int)($_GET['id'] ?? 0);
$item = $cat->buscarPorId($id);
if (!$item) redirecionar('/biblioteca/pages/categorias/index.php', 'Categoria não encontrada.', 'warning');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $cat->atualizar($id, $_POST['nome'] ?? '', $_POST['descricao'] ?? '');
        redirecionar('/biblioteca/pages/categorias/index.php', 'Categoria atualizada com sucesso!');
    } catch (Exception $e) { $erro = $e->getMessage(); }
}
cabecalho('Editar Categoria');
?>
<div class="card p-4" style="max-width:600px;">
  <?php if (!empty($erro)) alerta('danger', $erro); ?>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Nome *</label>
      <input type="text" name="nome" class="form-control" required maxlength="100"
             value="<?= htmlspecialchars($_POST['nome'] ?? $item['nome']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Descrição</label>
      <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($_POST['descricao'] ?? $item['descricao']) ?></textarea>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Atualizar</button>
      <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div>
<?php rodape(); ?>

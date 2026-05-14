<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Categoria.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        (new Categoria())->criar($_POST['nome'] ?? '', $_POST['descricao'] ?? '');
        redirecionar('/biblioteca/pages/categorias/index.php', 'Categoria cadastrada com sucesso!');
    } catch (Exception $e) { $erro = $e->getMessage(); }
}
cabecalho('Nova Categoria');
?>
<div class="card p-4" style="max-width:600px;">
  <?php if (!empty($erro)) alerta('danger', $erro); ?>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Nome *</label>
      <input type="text" name="nome" class="form-control" required maxlength="100"
             value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Descrição</label>
      <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar</button>
      <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div>
<?php rodape(); ?>

<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Autor.php';

$aut  = new Autor();
$id   = (int)($_GET['id'] ?? 0);
$item = $aut->buscarPorId($id);
if (!$item) redirecionar('/biblioteca/pages/autores/index.php', 'Autor não encontrado.', 'warning');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $aut->atualizar($id, $_POST['nome'] ?? '', $_POST['email'] ?? '', $_POST['nacionalidade'] ?? '');
        redirecionar('/biblioteca/pages/autores/index.php', 'Autor atualizado com sucesso!');
    } catch (Exception $e) { $erro = $e->getMessage(); }
}
cabecalho('Editar Autor');
?>
<div class="card p-4" style="max-width:600px;">
  <?php if (!empty($erro)) alerta('danger', $erro); ?>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Nome *</label>
      <input type="text" name="nome" class="form-control" required maxlength="150"
             value="<?= htmlspecialchars($_POST['nome'] ?? $item['nome']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">E-mail</label>
      <input type="email" name="email" class="form-control" maxlength="150"
             value="<?= htmlspecialchars($_POST['email'] ?? $item['email']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Nacionalidade</label>
      <input type="text" name="nacionalidade" class="form-control" maxlength="100"
             value="<?= htmlspecialchars($_POST['nacionalidade'] ?? $item['nacionalidade']) ?>">
    </div>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Atualizar</button>
      <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div>
<?php rodape(); ?>

<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Livro.php';
require_once __DIR__ . '/../../classes/Categoria.php';
require_once __DIR__ . '/../../classes/Autor.php';

$liv        = new Livro();
$id         = (int)($_GET['id'] ?? 0);
$item       = $liv->buscarPorId($id);
if (!$item) redirecionar('/biblioteca/pages/livros/index.php', 'Livro não encontrado.', 'warning');
$categorias = (new Categoria())->listarTodas();
$autores    = (new Autor())->listarTodos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $liv->atualizar(
            $id,
            $_POST['titulo']            ?? '',
            $_POST['isbn']              ?? '',
            (int)($_POST['ano']        ?? date('Y')),
            (int)($_POST['qtd']        ?? 1),
            (int)($_POST['categoria_id'] ?? 0),
            (int)($_POST['autor_id']     ?? 0)
        );
        redirecionar('/biblioteca/pages/livros/index.php', 'Livro atualizado com sucesso!');
    } catch (Exception $e) { $erro = $e->getMessage(); }
}
cabecalho('Editar Livro');
?>
<div class="card p-4" style="max-width:700px;">
  <?php if (!empty($erro)) alerta('danger', $erro); ?>
  <form method="post">
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label">Título *</label>
        <input type="text" name="titulo" class="form-control" required maxlength="200"
               value="<?= htmlspecialchars($_POST['titulo'] ?? $item['titulo']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">ISBN</label>
        <input type="text" name="isbn" class="form-control" maxlength="20"
               value="<?= htmlspecialchars($_POST['isbn'] ?? $item['isbn']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Ano</label>
        <input type="number" name="ano" class="form-control" min="1000" max="<?= date('Y') ?>"
               value="<?= $_POST['ano'] ?? $item['ano_publicacao'] ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Quantidade *</label>
        <input type="number" name="qtd" class="form-control" min="1" required
               value="<?= $_POST['qtd'] ?? $item['quantidade'] ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Autor *</label>
        <select name="autor_id" class="form-select" required>
          <?php foreach ($autores as $a): ?>
            <option value="<?= $a['id'] ?>"
              <?= (($_POST['autor_id'] ?? $item['autor_id']) == $a['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($a['nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Categoria *</label>
        <select name="categoria_id" class="form-select" required>
          <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['id'] ?>"
              <?= (($_POST['categoria_id'] ?? $item['categoria_id']) == $c['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button type="submit" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Atualizar</button>
      <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div>
<?php rodape(); ?>

<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Emprestimo.php';
require_once __DIR__ . '/../../classes/Livro.php';
require_once __DIR__ . '/../../classes/Usuario.php';

$livros   = (new Livro())->listarTodos();
$usuarios = (new Usuario())->listarTodos();
$prazo    = date('Y-m-d', strtotime('+' . Emprestimo::PRAZO_PADRAO_DIAS . ' days'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        (new Emprestimo())->criar(
            (int)($_POST['livro_id']   ?? 0),
            (int)($_POST['usuario_id'] ?? 0),
            $_POST['data_devolucao']   ?? $prazo,
            $_POST['observacao']       ?? ''
        );
        redirecionar('/biblioteca/pages/emprestimos/index.php', 'Empréstimo registrado com sucesso!');
    } catch (Exception $e) { $erro = $e->getMessage(); }
}
cabecalho('Novo Empréstimo');
?>
<div class="card p-4" style="max-width:650px;">
  <div class="alert alert-info py-2 small mb-4">
    <i class="bi bi-info-circle me-1"></i>
    Prazo padrão: <strong><?= Emprestimo::PRAZO_PADRAO_DIAS ?> dias</strong> &nbsp;|&nbsp;
    Limite por usuário: <strong><?= Usuario::LIMITE_EMPRESTIMOS ?> empréstimos simultâneos</strong>
  </div>
  <?php if (!empty($erro)) alerta('danger', $erro); ?>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Livro *</label>
      <select name="livro_id" class="form-select" required>
        <option value="">Selecione…</option>
        <?php foreach ($livros as $l): ?>
          <option value="<?= $l['id'] ?>" <?= (($_POST['livro_id'] ?? '') == $l['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($l['titulo']) ?> (<?= $l['quantidade'] ?> exemplar(es))
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Usuário *</label>
      <select name="usuario_id" class="form-select" required>
        <option value="">Selecione…</option>
        <?php foreach ($usuarios as $u): ?>
          <option value="<?= $u['id'] ?>" <?= (($_POST['usuario_id'] ?? '') == $u['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['nome']) ?> (<?= ucfirst($u['status']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Data de Devolução *</label>
      <input type="date" name="data_devolucao" class="form-control" required
             min="<?= date('Y-m-d') ?>" value="<?= $_POST['data_devolucao'] ?? $prazo ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Observação</label>
      <textarea name="observacao" class="form-control" rows="2"><?= htmlspecialchars($_POST['observacao'] ?? '') ?></textarea>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Registrar</button>
      <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div>
<?php rodape(); ?>

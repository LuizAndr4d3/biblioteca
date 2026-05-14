<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Usuario.php';

$usu  = new Usuario();
$id   = (int)($_GET['id'] ?? 0);
$item = $usu->buscarPorId($id);
if (!$item) redirecionar('/biblioteca/pages/usuarios/index.php', 'Usuário não encontrado.', 'warning');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $senha = $_POST['senha'] ?? '';
        $conf  = $_POST['confirmar'] ?? '';
        if ($senha && $senha !== $conf) throw new InvalidArgumentException("As senhas não coincidem.");
        $usu->atualizar(
            $id,
            $_POST['nome']     ?? '',
            $_POST['email']    ?? '',
            $_POST['telefone'] ?? '',
            $_POST['status']   ?? 'ativo',
            $_POST['role']     ?? 'usuario',
            $senha
        );
        redirecionar('/biblioteca/pages/usuarios/index.php', 'Usuário atualizado com sucesso!');
    } catch (Exception $e) { $erro = $e->getMessage(); }
}
cabecalho('Editar Usuário');
?>
<div class="card p-4" style="max-width:620px;">
  <?php if (!empty($erro)) alerta('danger', $erro); ?>
  <form method="post">
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label">Nome *</label>
        <input type="text" name="nome" class="form-control" required maxlength="150"
               value="<?= htmlspecialchars($_POST['nome'] ?? $item['nome']) ?>">
      </div>
      <div class="col-md-7">
        <label class="form-label">E-mail *</label>
        <input type="email" name="email" class="form-control" required maxlength="150"
               value="<?= htmlspecialchars($_POST['email'] ?? $item['email']) ?>">
      </div>
      <div class="col-md-5">
        <label class="form-label">Telefone</label>
        <input type="text" name="telefone" class="form-control" maxlength="20"
               value="<?= htmlspecialchars($_POST['telefone'] ?? $item['telefone']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Nova Senha <small class="text-muted fw-normal">(deixe em branco para manter)</small></label>
        <input type="password" name="senha" class="form-control" minlength="6" placeholder="••••••">
      </div>
      <div class="col-md-6">
        <label class="form-label">Confirmar Nova Senha</label>
        <input type="password" name="confirmar" class="form-control" placeholder="••••••">
      </div>
      <div class="col-md-6">
        <label class="form-label">Role *</label>
        <select name="role" class="form-select">
          <option value="usuario" <?= (($_POST['role'] ?? $item['role']) === 'usuario') ? 'selected' : '' ?>>📚 Leitor</option>
          <option value="admin"   <?= (($_POST['role'] ?? $item['role']) === 'admin')   ? 'selected' : '' ?>>🛡️ Administrador</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Status *</label>
        <select name="status" class="form-select">
          <option value="ativo"   <?= (($_POST['status'] ?? $item['status']) === 'ativo')   ? 'selected' : '' ?>>Ativo</option>
          <option value="inativo" <?= (($_POST['status'] ?? $item['status']) === 'inativo') ? 'selected' : '' ?>>Inativo</option>
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

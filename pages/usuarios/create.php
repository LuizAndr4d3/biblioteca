<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $senha = $_POST['senha'] ?? '';
        $conf  = $_POST['confirmar'] ?? '';
        if ($senha !== $conf) throw new InvalidArgumentException("As senhas não coincidem.");
        (new Usuario())->criar(
            $_POST['nome']     ?? '',
            $_POST['email']    ?? '',
            $senha,
            $_POST['telefone'] ?? '',
            $_POST['role']     ?? 'usuario'
        );
        redirecionar('/biblioteca/pages/usuarios/index.php', 'Usuário cadastrado com sucesso!');
    } catch (Exception $e) { $erro = $e->getMessage(); }
}
cabecalho('Novo Usuário');
?>
<div class="card p-4" style="max-width:620px;">
  <?php if (!empty($erro)) alerta('danger', $erro); ?>
  <form method="post">
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label">Nome *</label>
        <input type="text" name="nome" class="form-control" required maxlength="150"
               value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
      </div>
      <div class="col-md-7">
        <label class="form-label">E-mail *</label>
        <input type="email" name="email" class="form-control" required maxlength="150"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="col-md-5">
        <label class="form-label">Telefone</label>
        <input type="text" name="telefone" class="form-control" maxlength="20"
               value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Senha * <small class="text-muted fw-normal">(mín. 6 caracteres)</small></label>
        <input type="password" name="senha" class="form-control" required minlength="6">
      </div>
      <div class="col-md-6">
        <label class="form-label">Confirmar Senha *</label>
        <input type="password" name="confirmar" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Role *</label>
        <select name="role" class="form-select">
          <option value="usuario" <?= (($_POST['role'] ?? 'usuario') === 'usuario') ? 'selected' : '' ?>>📚 Leitor</option>
          <option value="admin"   <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>🛡️ Administrador</option>
        </select>
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar</button>
      <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div>
<?php rodape(); ?>

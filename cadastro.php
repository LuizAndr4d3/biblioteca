<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Usuario.php';
require_once __DIR__ . '/config/auth.php';

iniciarSessao();
if (!empty($_SESSION['usuario_id'])) {
    header('Location: ' . (isAdmin() ? '/biblioteca/index.php' : '/biblioteca/pages/usuario/dashboard.php'));
    exit;
}

$erro    = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha']      ?? '';
    $conf  = $_POST['confirmar']  ?? '';
    $tel   = trim($_POST['telefone'] ?? '');

    if (!$nome || !$email || !$senha || !$conf) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif ($senha !== $conf) {
        $erro = 'As senhas não coincidem.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres.';
    } else {
        try {
            $usu = new Usuario();
            if ($usu->emailExiste($email)) {
                $erro = 'Este e-mail já está cadastrado.';
            } else {
                $usu->criar($nome, $email, $senha, $tel, 'usuario');
                $sucesso = 'Conta criada com sucesso! Você já pode fazer login.';
            }
        } catch (Exception $e) {
            $erro = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Criar Conta — Biblioteca</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  * { box-sizing: border-box; }
  body { margin: 0; min-height: 100vh; display: flex; font-family: 'Segoe UI', sans-serif; background: #f0f2f5; }

  .panel-left {
    width: 40%;
    background: linear-gradient(145deg, #064e3b 0%, #065f46 50%, #059669 100%);
    display: flex; flex-direction: column; justify-content: center; align-items: center;
    padding: 3rem; color: #fff; position: relative; overflow: hidden;
  }
  .panel-left::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  }
  .panel-left .logo-icon { font-size: 4.5rem; margin-bottom: 1.5rem; opacity: .9; }
  .panel-left h1 { font-size: 2rem; font-weight: 800; margin-bottom: .5rem; letter-spacing: -1px; }
  .panel-left p  { font-size: 1rem; opacity: .75; max-width: 300px; text-align: center; line-height: 1.6; }
  .step-item { display: flex; align-items: flex-start; gap: .85rem; margin-top: 1.2rem; font-size: .88rem; opacity: .85; }
  .step-num  { background: rgba(255,255,255,.2); border-radius: 50%; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .8rem; flex-shrink: 0; }

  .panel-right {
    width: 60%; display: flex; align-items: center; justify-content: center; padding: 2.5rem;
  }
  .register-box { width: 100%; max-width: 480px; }
  .register-box h2 { font-size: 1.65rem; font-weight: 700; color: #064e3b; margin-bottom: .25rem; }
  .register-box .subtitle { color: #6b7280; margin-bottom: 1.5rem; font-size: .93rem; }

  .form-control {
    border-radius: 10px !important; border: 1.5px solid #e5e7eb !important;
    padding: .75rem 1rem !important;
    transition: border-color .2s, box-shadow .2s;
  }
  .form-control:focus { border-color: #059669 !important; box-shadow: 0 0 0 3px rgba(5,150,105,.15) !important; }
  .form-label { font-weight: 600; font-size: .85rem; color: #374151; margin-bottom: .3rem; }

  .input-icon { position: relative; }
  .input-icon .bi { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; }

  .btn-cadastrar {
    background: linear-gradient(135deg, #059669, #10b981);
    border: none; border-radius: 10px; padding: .85rem;
    font-size: 1rem; font-weight: 600; color: #fff; width: 100%;
    transition: transform .15s, box-shadow .15s;
  }
  .btn-cadastrar:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(5,150,105,.35); color:#fff; }

  .link-login { text-align: center; font-size: .9rem; color: #6b7280; margin-top: 1rem; }
  .link-login a { color: #059669; font-weight: 600; text-decoration: none; }
  .link-login a:hover { text-decoration: underline; }

  .strength-bar { height: 4px; border-radius: 4px; background: #e5e7eb; margin-top: .3rem; overflow: hidden; }
  .strength-fill { height: 100%; border-radius: 4px; width: 0; transition: width .3s, background .3s; }

  @media (max-width: 768px) {
    .panel-left { display: none; }
    .panel-right { width: 100%; }
  }
</style>
</head>
<body>

<div class="panel-left">
  <div class="logo-icon"><i class="bi bi-person-plus-fill"></i></div>
  <h1>Junte-se!</h1>
  <p>Crie sua conta e tenha acesso ao catálogo completo da biblioteca.</p>
  <div class="mt-3">
    <div class="step-item"><div class="step-num">1</div><span>Preencha seus dados básicos</span></div>
    <div class="step-item"><div class="step-num">2</div><span>Crie uma senha segura</span></div>
    <div class="step-item"><div class="step-num">3</div><span>Acesse o catálogo e solicite empréstimos</span></div>
  </div>
</div>

<div class="panel-right">
  <div class="register-box">
    <h2>Criar sua conta</h2>
    <p class="subtitle">Rápido e gratuito. Campos com * são obrigatórios.</p>

    <?php if ($sucesso): ?>
      <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($sucesso) ?>
        <div class="mt-2"><a href="/biblioteca/login.php" class="alert-link">Ir para o login →</a></div>
      </div>
    <?php endif; ?>

    <?php if ($erro): ?>
      <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <?php if (!$sucesso): ?>
    <form method="post" novalidate>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Nome completo *</label>
          <div class="input-icon">
            <input type="text" name="nome" class="form-control" required maxlength="150"
                   value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" placeholder="Seu nome">
            <i class="bi bi-person"></i>
          </div>
        </div>
        <div class="col-12">
          <label class="form-label">E-mail *</label>
          <div class="input-icon">
            <input type="email" name="email" class="form-control" required maxlength="150"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="seu@email.com">
            <i class="bi bi-envelope"></i>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Telefone</label>
          <div class="input-icon">
            <input type="text" name="telefone" class="form-control" maxlength="20"
                   value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>" placeholder="(11) 91234-5678">
            <i class="bi bi-telephone"></i>
          </div>
        </div>
        <div class="col-md-6"><!-- espaço --></div>
        <div class="col-md-6">
          <label class="form-label">Senha * <small class="text-muted fw-normal">(mín. 6 caracteres)</small></label>
          <div class="input-icon">
            <input type="password" name="senha" id="senha" class="form-control" required
                   placeholder="••••••" autocomplete="new-password">
            <i class="bi bi-lock"></i>
          </div>
          <div class="strength-bar mt-1"><div class="strength-fill" id="strengthFill"></div></div>
          <small id="strengthText" class="text-muted" style="font-size:.75rem"></small>
        </div>
        <div class="col-md-6">
          <label class="form-label">Confirmar senha *</label>
          <div class="input-icon">
            <input type="password" name="confirmar" id="confirmar" class="form-control" required
                   placeholder="••••••" autocomplete="new-password">
            <i class="bi bi-lock-fill"></i>
          </div>
          <small id="matchText" style="font-size:.75rem"></small>
        </div>
      </div>

      <button type="submit" class="btn btn-cadastrar mt-4">
        <i class="bi bi-person-check me-2"></i>Criar minha conta
      </button>
    </form>
    <?php endif; ?>

    <div class="link-login">
      Já tem uma conta? <a href="/biblioteca/login.php">Fazer login</a>
    </div>
  </div>
</div>

<script>
const senhaInput    = document.getElementById('senha');
const confirmarInput= document.getElementById('confirmar');
const fill          = document.getElementById('strengthFill');
const strengthText  = document.getElementById('strengthText');
const matchText     = document.getElementById('matchText');

senhaInput?.addEventListener('input', () => {
  const v = senhaInput.value;
  let score = 0;
  if (v.length >= 6)  score++;
  if (v.length >= 10) score++;
  if (/[A-Z]/.test(v)) score++;
  if (/[0-9]/.test(v)) score++;
  if (/[^a-zA-Z0-9]/.test(v)) score++;
  const pct   = (score / 5) * 100;
  const color = score <= 1 ? '#ef4444' : score <= 3 ? '#f59e0b' : '#10b981';
  const label = score <= 1 ? 'Fraca' : score <= 3 ? 'Média' : 'Forte';
  fill.style.width      = pct + '%';
  fill.style.background = color;
  strengthText.textContent = v ? `Força: ${label}` : '';
  strengthText.style.color = color;
});

confirmarInput?.addEventListener('input', () => {
  if (confirmarInput.value === senhaInput.value) {
    matchText.textContent = '✓ As senhas coincidem';
    matchText.style.color = '#10b981';
  } else {
    matchText.textContent = '✗ As senhas não coincidem';
    matchText.style.color = '#ef4444';
  }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

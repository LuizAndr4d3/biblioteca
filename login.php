<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Usuario.php';
require_once __DIR__ . '/config/auth.php';

iniciarSessao();

// Se já está logado, redireciona
if (!empty($_SESSION['usuario_id'])) {
    header('Location: ' . (isAdmin() ? '/biblioteca/index.php' : '/biblioteca/pages/usuario/dashboard.php'));
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        $usu     = new Usuario();
        $usuario = $usu->login($email, $senha);

        if ($usuario) {
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_role'] = $usuario['role'];

            $destino = $usuario['role'] === 'admin'
                ? '/biblioteca/index.php'
                : '/biblioteca/pages/usuario/dashboard.php';

            header('Location: ' . $destino);
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos, ou conta inativa.';
        }
    } else {
        $erro = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login — Biblioteca</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  * { box-sizing: border-box; }
  body { margin: 0; min-height: 100vh; display: flex; font-family: 'Segoe UI', sans-serif; background: #f0f2f5; }

  /* ── Painel esquerdo ── */
  .panel-left {
    width: 55%;
    background: linear-gradient(145deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
    display: flex; flex-direction: column; justify-content: center; align-items: center;
    padding: 3rem; color: #fff; position: relative; overflow: hidden;
  }
  .panel-left::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  }
  .panel-left .logo-icon {
    font-size: 5rem; margin-bottom: 1.5rem; opacity: .9;
    filter: drop-shadow(0 4px 16px rgba(0,0,0,.3));
  }
  .panel-left h1 { font-size: 2.4rem; font-weight: 800; margin-bottom: .5rem; letter-spacing: -1px; }
  .panel-left p  { font-size: 1.05rem; opacity: .75; max-width: 340px; text-align: center; line-height: 1.6; }
  .feature-item  { display: flex; align-items: center; gap: .75rem; margin-top: 1rem; font-size: .92rem; opacity: .8; }
  .feature-item i { font-size: 1.1rem; color: #a5b4fc; }

  /* ── Painel direito ── */
  .panel-right {
    width: 45%; display: flex; align-items: center; justify-content: center;
    padding: 2.5rem;
  }
  .login-box {
    width: 100%; max-width: 400px;
  }
  .login-box h2 { font-size: 1.75rem; font-weight: 700; color: #1e1b4b; margin-bottom: .25rem; }
  .login-box .subtitle { color: #6b7280; margin-bottom: 2rem; font-size: .95rem; }

  .form-floating label { color: #9ca3af; }
  .form-control {
    border-radius: 10px !important; border: 1.5px solid #e5e7eb !important;
    padding: 1rem !important; height: auto !important;
    transition: border-color .2s, box-shadow .2s;
  }
  .form-control:focus { border-color: #6366f1 !important; box-shadow: 0 0 0 3px rgba(99,102,241,.15) !important; }
  .form-floating { margin-bottom: 1rem; position: relative; }
  .form-floating .bi { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 1.1rem; z-index: 5; }

  .btn-entrar {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    border: none; border-radius: 10px; padding: .85rem;
    font-size: 1rem; font-weight: 600; color: #fff; width: 100%;
    transition: transform .15s, box-shadow .15s;
  }
  .btn-entrar:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(79,70,229,.35); color:#fff; }
  .btn-entrar:active { transform: none; }

  .divider { display: flex; align-items: center; gap: 1rem; margin: 1.5rem 0; color: #d1d5db; font-size: .85rem; }
  .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }

  .link-cadastro { text-align: center; font-size: .9rem; color: #6b7280; }
  .link-cadastro a { color: #4f46e5; font-weight: 600; text-decoration: none; }
  .link-cadastro a:hover { text-decoration: underline; }

  @media (max-width: 768px) {
    .panel-left { display: none; }
    .panel-right { width: 100%; }
  }
</style>
</head>
<body>

<!-- Painel Esquerdo -->
<div class="panel-left">
  <div class="logo-icon"><i class="bi bi-book-half"></i></div>
  <h1>BiblioSys</h1>
  <p>Gerencie seu acervo, controle empréstimos e sirva seus leitores com eficiência.</p>
  <div class="mt-4">
    <div class="feature-item"><i class="bi bi-journals"></i> Catálogo completo de livros</div>
    <div class="feature-item"><i class="bi bi-arrow-left-right"></i> Controle de empréstimos e devoluções</div>
    <div class="feature-item"><i class="bi bi-people"></i> Gestão de usuários e roles</div>
    <div class="feature-item"><i class="bi bi-shield-check"></i> Regras de negócio automatizadas</div>
  </div>
</div>

<!-- Painel Direito -->
<div class="panel-right">
  <div class="login-box">
    <h2>Bem-vindo de volta!</h2>
    <p class="subtitle">Entre com suas credenciais para continuar.</p>

    <?php if ($erro): ?>
      <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="form-floating">
        <input type="email" name="email" id="email" class="form-control"
               placeholder="seu@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autocomplete="email">
        <label for="email">E-mail</label>
        <i class="bi bi-envelope"></i>
      </div>
      <div class="form-floating">
        <input type="password" name="senha" id="senha" class="form-control"
               placeholder="Senha" required autocomplete="current-password">
        <label for="senha">Senha</label>
        <i class="bi bi-lock"></i>
      </div>

      <button type="submit" class="btn btn-entrar mt-2">
        <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
      </button>
    </form>

    <div class="divider">ou</div>

    <div class="link-cadastro">
      Não tem uma conta? <a href="/biblioteca/cadastro.php">Criar conta gratuita</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

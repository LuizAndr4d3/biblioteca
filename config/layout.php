<?php
require_once __DIR__ . '/auth.php';

function cabecalho(string $titulo = 'Biblioteca', bool $requerLogin = true): void {
    if ($requerLogin) verificarLogin();
    $nome   = usuarioLogadoNome();
    $role   = usuarioLogadoRole();
    $inicial= mb_strtoupper(mb_substr($nome, 0, 1));
    $isAdm  = ($role === 'admin');

    // Menus
    $menuAdmin = [
        ['icon'=>'bi-speedometer2',      'label'=>'Dashboard',    'url'=>'/biblioteca/index.php'],
        ['icon'=>'bi-tags',              'label'=>'Categorias',   'url'=>'/biblioteca/pages/categorias/index.php'],
        ['icon'=>'bi-person-lines-fill', 'label'=>'Autores',      'url'=>'/biblioteca/pages/autores/index.php'],
        ['icon'=>'bi-journals',          'label'=>'Livros',       'url'=>'/biblioteca/pages/livros/index.php'],
        ['icon'=>'bi-people',            'label'=>'Usuários',     'url'=>'/biblioteca/pages/usuarios/index.php'],
        ['icon'=>'bi-arrow-left-right',  'label'=>'Empréstimos',  'url'=>'/biblioteca/pages/emprestimos/index.php'],
    ];
    $menuUser = [
        ['icon'=>'bi-house-heart',       'label'=>'Início',       'url'=>'/biblioteca/pages/usuario/dashboard.php'],
        ['icon'=>'bi-search',            'label'=>'Catálogo',     'url'=>'/biblioteca/pages/usuario/catalogo.php'],
        ['icon'=>'bi-arrow-right-circle','label'=>'Solicitar',    'url'=>'/biblioteca/pages/usuario/solicitar.php'],
        ['icon'=>'bi-clock-history',     'label'=>'Meus Livros',  'url'=>'/biblioteca/pages/usuario/meus-emprestimos.php'],
    ];
    $menu = $isAdm ? $menuAdmin : $menuUser;
    $current = $_SERVER['REQUEST_URI'];

    echo '<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>' . htmlspecialchars($titulo) . ' — BiblioSys</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  :root {
    --sidebar-bg: linear-gradient(180deg,#1e1b4b 0%,#312e81 60%,#3730a3 100%);
    --sidebar-w: 240px;
    --accent: #6366f1;
    --accent-light: #e0e7ff;
  }
  body { background:#f0f2f5; font-family:"Segoe UI",sans-serif; margin:0; }

  /* ── Sidebar ── */
  .sidebar {
    position: fixed; top:0; left:0; bottom:0; width:var(--sidebar-w);
    background: var(--sidebar-bg);
    display:flex; flex-direction:column;
    box-shadow: 4px 0 20px rgba(0,0,0,.15);
    z-index:100;
  }
  .sidebar-brand {
    padding:1.4rem 1.25rem 1.1rem;
    border-bottom:1px solid rgba(255,255,255,.08);
    display:flex; align-items:center; gap:.75rem;
  }
  .sidebar-brand .brand-icon {
    width:38px; height:38px; border-radius:10px;
    background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center;
    font-size:1.25rem; color:#fff;
  }
  .sidebar-brand .brand-text { color:#fff; font-size:1.2rem; font-weight:800; letter-spacing:-.5px; }
  .sidebar-brand .brand-sub  { color:rgba(255,255,255,.45); font-size:.68rem; display:block; }

  .sidebar-section { padding:.75rem 1rem .25rem; color:rgba(255,255,255,.3); font-size:.65rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; }

  .sidebar-nav { flex:1; overflow-y:auto; padding:.5rem .75rem; }
  .nav-item a  {
    display:flex; align-items:center; gap:.75rem;
    padding:.65rem .9rem; border-radius:10px; margin:.1rem 0;
    color:rgba(255,255,255,.65); font-size:.88rem; font-weight:500;
    text-decoration:none; transition:all .2s;
  }
  .nav-item a:hover  { background:rgba(255,255,255,.1); color:#fff; }
  .nav-item a.active { background:rgba(255,255,255,.18); color:#fff; box-shadow:0 2px 8px rgba(0,0,0,.15); }
  .nav-item a i      { font-size:1rem; width:18px; text-align:center; }

  .sidebar-footer {
    border-top:1px solid rgba(255,255,255,.08);
    padding:1rem 1.25rem;
  }
  .user-card {
    display:flex; align-items:center; gap:.75rem;
  }
  .user-avatar {
    width:36px; height:36px; border-radius:50%;
    background:linear-gradient(135deg,#818cf8,#a78bfa);
    display:flex; align-items:center; justify-content:center;
    font-weight:700; color:#fff; font-size:.9rem; flex-shrink:0;
  }
  .user-name  { color:#fff; font-size:.82rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .user-role  { color:rgba(255,255,255,.45); font-size:.7rem; }
  .btn-logout {
    background:rgba(255,255,255,.08); border:none; border-radius:8px;
    color:rgba(255,255,255,.6); padding:.3rem .55rem; font-size:.85rem;
    margin-left:auto; transition:.2s; flex-shrink:0;
  }
  .btn-logout:hover { background:rgba(239,68,68,.25); color:#fca5a5; }

  /* ── Main ── */
  .main-content {
    margin-left:var(--sidebar-w); min-height:100vh;
    padding:1.75rem 2rem;
  }
  .page-header {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:1.75rem;
  }
  .page-title   { font-size:1.4rem; font-weight:700; color:#1e1b4b; margin:0; }
  .breadcrumb   { margin:0; font-size:.82rem; }
  .breadcrumb-item a { color:var(--accent); text-decoration:none; }

  /* ── Cards ── */
  .card {
    border:none !important; border-radius:14px !important;
    box-shadow:0 2px 12px rgba(0,0,0,.07) !important;
  }
  .card-header {
    border-radius:14px 14px 0 0 !important;
    border-bottom:1px solid rgba(0,0,0,.06) !important;
    background:#fff; font-weight:600;
  }

  /* ── Stat cards ── */
  .stat-card {
    border-radius:16px; padding:1.4rem 1.5rem;
    display:flex; align-items:center; gap:1.1rem;
    box-shadow:0 4px 16px rgba(0,0,0,.1); color:#fff; border:none;
    transition:transform .2s;
  }
  .stat-card:hover { transform:translateY(-2px); }
  .stat-icon { width:52px; height:52px; border-radius:13px; background:rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-size:1.6rem; }
  .stat-num  { font-size:2rem; font-weight:800; line-height:1; }
  .stat-lbl  { font-size:.8rem; opacity:.85; font-weight:500; }

  .stat-indigo { background:linear-gradient(135deg,#6366f1,#818cf8); }
  .stat-green  { background:linear-gradient(135deg,#059669,#34d399); }
  .stat-amber  { background:linear-gradient(135deg,#d97706,#fbbf24); }
  .stat-red    { background:linear-gradient(135deg,#dc2626,#f87171); }
  .stat-blue   { background:linear-gradient(135deg,#2563eb,#60a5fa); }

  /* ── Tables ── */
  .table { font-size:.88rem; }
  .table thead th { background:#f8f9ff; color:#374151; font-weight:700; font-size:.78rem; text-transform:uppercase; letter-spacing:.5px; border-bottom:2px solid #e5e7eb !important; }
  .table tbody tr:hover { background:#fafafa; }
  .table td, .table th { vertical-align:middle; border-color:#f3f4f6 !important; }

  /* ── Badges ── */
  .badge { border-radius:6px; font-weight:600; font-size:.73rem; }

  /* ── Buttons ── */
  .btn { border-radius:9px; font-weight:600; }
  .btn-sm { font-size:.8rem; }
  .btn-primary { background:linear-gradient(135deg,#6366f1,#818cf8); border:none; }
  .btn-primary:hover { background:linear-gradient(135deg,#4f46e5,#6366f1); }
  .btn-warning { background:linear-gradient(135deg,#f59e0b,#fbbf24); border:none; color:#fff; }
  .btn-warning:hover { color:#fff; }
  .btn-success { background:linear-gradient(135deg,#059669,#10b981); border:none; }

  /* ── Forms ── */
  .form-control, .form-select {
    border-radius:10px !important; border:1.5px solid #e5e7eb !important; font-size:.9rem;
    transition:border-color .2s, box-shadow .2s;
  }
  .form-control:focus, .form-select:focus {
    border-color:#6366f1 !important; box-shadow:0 0 0 3px rgba(99,102,241,.15) !important;
  }
  .form-label { font-weight:600; font-size:.83rem; color:#374151; }

  /* ── Alerts ── */
  .alert { border:none; border-radius:12px; font-size:.88rem; }

  @media (max-width:768px) {
    .sidebar      { display:none; }
    .main-content { margin-left:0; padding:1rem; }
  }
</style>
</head>
<body>

<div class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon"><i class="bi bi-book-half"></i></div>
    <div>
      <div class="brand-text">BiblioSys</div>
      <span class="brand-sub">' . ($isAdm ? 'PAINEL ADMIN' : 'ÁREA DO LEITOR') . '</span>
    </div>
  </div>

  <div class="sidebar-section">' . ($isAdm ? 'Gerenciamento' : 'Menu') . '</div>
  <nav class="sidebar-nav"><ul class="list-unstyled mb-0">';

    foreach ($menu as $item) {
        $active = (strpos($current, basename($item['url'], '.php')) !== false) ? 'active' : '';
        echo '<li class="nav-item"><a href="' . $item['url'] . '" class="' . $active . '">
                <i class="bi ' . $item['icon'] . '"></i> ' . $item['label'] . '
              </a></li>';
    }

    echo '</ul></nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar">' . $inicial . '</div>
      <div style="overflow:hidden;min-width:0">
        <div class="user-name">' . htmlspecialchars($nome) . '</div>
        <div class="user-role">' . ($isAdm ? '🛡️ Admin' : '📚 Leitor') . '</div>
      </div>
      <a href="/biblioteca/logout.php" class="btn-logout" title="Sair"><i class="bi bi-box-arrow-right"></i></a>
    </div>
  </div>
</div>

<div class="main-content">
  <div class="page-header">
    <h1 class="page-title"><i class="bi bi-chevron-right text-muted me-1" style="font-size:.9rem"></i>' . htmlspecialchars($titulo) . '</h1>
  </div>';
}

function rodape(): void {
    echo '</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body></html>';
}

function alerta(string $tipo, string $mensagem): void {
    $icons = ['success'=>'check-circle','danger'=>'exclamation-circle','warning'=>'exclamation-triangle','info'=>'info-circle'];
    $icon  = $icons[$tipo] ?? 'info-circle';
    echo '<div class="alert alert-' . $tipo . ' alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-' . $icon . '"></i>
        <span>' . htmlspecialchars($mensagem) . '</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>';
}

function redirecionar(string $url, string $msg = '', string $tipo = 'success'): void {
    if ($msg) {
        iniciarSessao();
        $_SESSION['flash_msg']  = $msg;
        $_SESSION['flash_tipo'] = $tipo;
    }
    header('Location: ' . $url);
    exit;
}

function exibirFlash(): void {
    iniciarSessao();
    if (!empty($_SESSION['flash_msg'])) {
        alerta($_SESSION['flash_tipo'] ?? 'info', $_SESSION['flash_msg']);
        unset($_SESSION['flash_msg'], $_SESSION['flash_tipo']);
    }
}

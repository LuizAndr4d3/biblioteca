<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/layout.php';
require_once __DIR__ . '/classes/Categoria.php';
require_once __DIR__ . '/classes/Autor.php';
require_once __DIR__ . '/classes/Livro.php';
require_once __DIR__ . '/classes/Usuario.php';
require_once __DIR__ . '/classes/Emprestimo.php';

verificarAdmin();

$cat       = new Categoria();
$aut       = new Autor();
$liv       = new Livro();
$usu       = new Usuario();
$emp       = new Emprestimo();
$atrasados = $emp->listarAtrasados();

cabecalho('Dashboard');
exibirFlash();
?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="stat-card stat-indigo">
      <div class="stat-icon"><i class="bi bi-journals"></i></div>
      <div><div class="stat-num"><?= $liv->total() ?></div><div class="stat-lbl">Livros no Acervo</div></div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card stat-green">
      <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
      <div><div class="stat-num"><?= $usu->total() ?></div><div class="stat-lbl">Usuários Cadastrados</div></div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card stat-amber">
      <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
      <div><div class="stat-num"><?= $emp->totalAtivos() ?></div><div class="stat-lbl">Empréstimos Ativos</div></div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card stat-red">
      <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
      <div><div class="stat-num"><?= count($atrasados) ?></div><div class="stat-lbl">Em Atraso</div></div>
    </div>
  </div>
</div>

<div class="row g-4">

  <!-- Empréstimos em atraso -->
  <div class="col-lg-8">
    <?php if ($atrasados): ?>
    <div class="card">
      <div class="card-header d-flex align-items-center gap-2 py-3">
        <span class="badge bg-danger rounded-circle p-1"><i class="bi bi-exclamation"></i></span>
        <span>Empréstimos em Atraso</span>
        <span class="badge bg-danger ms-auto"><?= count($atrasados) ?></span>
      </div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead><tr><th>Livro</th><th>Leitor</th><th>Prazo</th><th>Atraso</th></tr></thead>
          <tbody>
          <?php foreach ($atrasados as $a): ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($a['livro_titulo']) ?></td>
              <td><?= htmlspecialchars($a['usuario_nome']) ?></td>
              <td><?= date('d/m/Y', strtotime($a['data_devolucao'])) ?></td>
              <td><span class="badge bg-danger"><?= $a['dias_atraso'] ?> dia(s)</span></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php else: ?>
    <div class="card p-4 text-center">
      <div class="text-success fs-1 mb-2"><i class="bi bi-check-circle"></i></div>
      <h6 class="fw-bold text-success">Tudo em dia!</h6>
      <p class="text-muted mb-0 small">Nenhum empréstimo em atraso no momento.</p>
    </div>
    <?php endif; ?>
  </div>

  <!-- Painel lateral -->
  <div class="col-lg-4 d-flex flex-column gap-3">

    <!-- Resumo do acervo -->
    <div class="card">
      <div class="card-header py-3">📊 Resumo do Acervo</div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush rounded-bottom">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Categorias <span class="badge" style="background:#e0e7ff;color:#4338ca"><?= $cat->total() ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Autores <span class="badge" style="background:#dcfce7;color:#166534"><?= $aut->total() ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Títulos <span class="badge" style="background:#ede9fe;color:#5b21b6"><?= $liv->total() ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Leitores <span class="badge" style="background:#fef9c3;color:#854d0e"><?= $usu->total() ?></span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Ações rápidas -->
    <div class="card">
      <div class="card-header py-3">⚡ Ações Rápidas</div>
      <div class="card-body d-grid gap-2">
        <a href="/biblioteca/pages/livros/create.php" class="btn btn-primary btn-sm text-start">
          <i class="bi bi-plus-circle me-2"></i>Cadastrar Livro
        </a>
        <a href="/biblioteca/pages/usuarios/create.php" class="btn btn-sm text-start" style="background:#ecfdf5;color:#059669;border:1.5px solid #d1fae5">
          <i class="bi bi-person-plus me-2"></i>Cadastrar Usuário
        </a>
        <a href="/biblioteca/pages/emprestimos/create.php" class="btn btn-sm text-start" style="background:#fffbeb;color:#d97706;border:1.5px solid #fde68a">
          <i class="bi bi-arrow-right-circle me-2"></i>Novo Empréstimo
        </a>
        <a href="/biblioteca/pages/emprestimos/index.php" class="btn btn-sm text-start" style="background:#f5f3ff;color:#7c3aed;border:1.5px solid #ede9fe">
          <i class="bi bi-list-check me-2"></i>Ver Todos os Empréstimos
        </a>
      </div>
    </div>

  </div>
</div>

<?php rodape(); ?>

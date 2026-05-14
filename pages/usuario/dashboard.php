<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Livro.php';
require_once __DIR__ . '/../../classes/Emprestimo.php';

verificarLogin();

$uid  = usuarioLogadoId();
$liv  = new Livro();
$emp  = new Emprestimo();

// Empréstimos do usuário
$db   = getConexao();
$stmt = $db->prepare(
    "SELECT e.*, l.titulo AS livro_titulo,
            CASE WHEN e.devolvido=0 AND e.data_devolucao < CURRENT_DATE THEN 1 ELSE 0 END AS atrasado
     FROM emprestimos e
     INNER JOIN livros l ON l.id = e.livro_id
     WHERE e.usuario_id = :uid
     ORDER BY e.devolvido ASC, e.data_devolucao ASC
     LIMIT 5"
);
$stmt->execute([':uid' => $uid]);
$meusEmp = $stmt->fetchAll();

$stmt2 = $db->prepare("SELECT COUNT(*) FROM emprestimos WHERE usuario_id=:uid AND devolvido=0");
$stmt2->execute([':uid' => $uid]);
$ativos = (int)$stmt2->fetchColumn();

$stmt3 = $db->prepare("SELECT COUNT(*) FROM emprestimos WHERE usuario_id=:uid AND devolvido=0 AND data_devolucao < CURRENT_DATE");
$stmt3->execute([':uid' => $uid]);
$atrasados = (int)$stmt3->fetchColumn();

cabecalho('Início');
exibirFlash();
?>

<!-- Cards do usuário -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-4">
    <div class="stat-card stat-indigo">
      <div class="stat-icon"><i class="bi bi-journals"></i></div>
      <div><div class="stat-num"><?= $liv->total() ?></div><div class="stat-lbl">Livros Disponíveis</div></div>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="stat-card stat-green">
      <div class="stat-icon"><i class="bi bi-bookmark-check"></i></div>
      <div><div class="stat-num"><?= $ativos ?></div><div class="stat-lbl">Empréstimos Ativos</div></div>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="stat-card <?= $atrasados > 0 ? 'stat-red' : 'stat-blue' ?>">
      <div class="stat-icon"><i class="bi bi-clock<?= $atrasados > 0 ? '-history' : '' ?>"></i></div>
      <div><div class="stat-num"><?= $atrasados ?></div><div class="stat-lbl">Em Atraso</div></div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Meus empréstimos recentes -->
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between py-3">
        <span><i class="bi bi-clock-history me-2 text-primary"></i>Meus Empréstimos Recentes</span>
        <a href="/biblioteca/pages/usuario/meus-emprestimos.php" class="btn btn-sm btn-primary">Ver todos</a>
      </div>
      <?php if ($meusEmp): ?>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead><tr><th>Livro</th><th>Devolução</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach ($meusEmp as $e): ?>
            <tr class="<?= (!$e['devolvido'] && $e['atrasado']) ? 'table-danger' : '' ?>">
              <td class="fw-semibold"><?= htmlspecialchars($e['livro_titulo']) ?></td>
              <td><?= date('d/m/Y', strtotime($e['data_devolucao'])) ?></td>
              <td>
                <?php if ($e['devolvido']): ?>
                  <span class="badge bg-success">Devolvido</span>
                <?php elseif ($e['atrasado']): ?>
                  <span class="badge bg-danger">Em Atraso</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">Em Aberto</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="card-body text-center text-muted py-4">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        Você ainda não tem empréstimos.
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Ações -->
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header py-3">⚡ O que deseja fazer?</div>
      <div class="card-body d-grid gap-2">
        <a href="/biblioteca/pages/usuario/catalogo.php" class="btn btn-primary">
          <i class="bi bi-search me-2"></i>Explorar Catálogo
        </a>
        <a href="/biblioteca/pages/usuario/solicitar.php" class="btn btn-sm text-start" style="background:#fffbeb;color:#d97706;border:1.5px solid #fde68a">
          <i class="bi bi-arrow-right-circle me-2"></i>Solicitar Empréstimo
        </a>
        <a href="/biblioteca/pages/usuario/meus-emprestimos.php" class="btn btn-sm text-start" style="background:#f5f3ff;color:#7c3aed;border:1.5px solid #ede9fe">
          <i class="bi bi-list-check me-2"></i>Meus Empréstimos
        </a>
      </div>
    </div>

    <?php if ($atrasados > 0): ?>
    <div class="alert alert-danger mt-3">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>Atenção!</strong> Você tem <?= $atrasados ?> empréstimo(s) em atraso. Devolva o quanto antes para evitar suspensão.
    </div>
    <?php endif; ?>
  </div>
</div>

<?php rodape(); ?>

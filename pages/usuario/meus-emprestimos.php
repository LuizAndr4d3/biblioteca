<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/layout.php';

verificarLogin();

$uid = usuarioLogadoId();
$db  = getConexao();

$stmt = $db->prepare(
    "SELECT e.*, l.titulo AS livro_titulo, a.nome AS autor_nome,
            CASE WHEN e.devolvido=0 AND e.data_devolucao < CURRENT_DATE THEN 1 ELSE 0 END AS atrasado,
            CASE WHEN e.devolvido=0 AND e.data_devolucao < CURRENT_DATE
                 THEN DATEDIFF(CURRENT_DATE, e.data_devolucao) ELSE 0 END AS dias_atraso
     FROM emprestimos e
     INNER JOIN livros l ON l.id = e.livro_id
     INNER JOIN autores a ON a.id = l.autor_id
     WHERE e.usuario_id = :uid
     ORDER BY e.devolvido ASC, e.data_devolucao ASC"
);
$stmt->execute([':uid' => $uid]);
$lista = $stmt->fetchAll();

$ativos    = array_filter($lista, fn($e) => !$e['devolvido']);
$atrasados = array_filter($lista, fn($e) => !$e['devolvido'] && $e['atrasado']);
$hist      = array_filter($lista, fn($e) =>  $e['devolvido']);

cabecalho('Meus Empréstimos');
exibirFlash();
?>

<!-- Resumo pessoal -->
<div class="row g-3 mb-4">
  <div class="col-4">
    <div class="stat-card stat-indigo">
      <div class="stat-icon"><i class="bi bi-bookmark"></i></div>
      <div><div class="stat-num"><?= count($ativos) ?></div><div class="stat-lbl">Ativos</div></div>
    </div>
  </div>
  <div class="col-4">
    <div class="stat-card <?= count($atrasados) > 0 ? 'stat-red' : 'stat-green' ?>">
      <div class="stat-icon"><i class="bi bi-clock"></i></div>
      <div><div class="stat-num"><?= count($atrasados) ?></div><div class="stat-lbl">Em Atraso</div></div>
    </div>
  </div>
  <div class="col-4">
    <div class="stat-card stat-blue">
      <div class="stat-icon"><i class="bi bi-check2-all"></i></div>
      <div><div class="stat-num"><?= count($hist) ?></div><div class="stat-lbl">Devolvidos</div></div>
    </div>
  </div>
</div>

<?php if (count($atrasados) > 0): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
  <i class="bi bi-exclamation-triangle-fill fs-5"></i>
  <div>Você tem <strong><?= count($atrasados) ?></strong> empréstimo(s) em atraso! Por favor, devolva o mais rápido possível para evitar restrições na sua conta.</div>
</div>
<?php endif; ?>

<!-- Lista de empréstimos -->
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <span><i class="bi bi-list-check me-2 text-primary"></i>Todos os Empréstimos</span>
    <a href="solicitar.php" class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i>Novo</a>
  </div>

  <?php if ($lista): ?>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr><th>Livro</th><th>Autor</th><th>Emprestado em</th><th>Devolver até</th><th>Status</th></tr>
      </thead>
      <tbody>
      <?php foreach ($lista as $e): ?>
        <tr class="<?= (!$e['devolvido'] && $e['atrasado']) ? 'table-danger' : '' ?>">
          <td class="fw-semibold"><?= htmlspecialchars($e['livro_titulo']) ?></td>
          <td class="text-muted"><?= htmlspecialchars($e['autor_nome']) ?></td>
          <td><?= date('d/m/Y', strtotime($e['data_emprestimo'])) ?></td>
          <td><?= date('d/m/Y', strtotime($e['data_devolucao'])) ?></td>
          <td>
            <?php if ($e['devolvido']): ?>
              <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Devolvido</span>
            <?php elseif ($e['atrasado']): ?>
              <span class="badge bg-danger"><i class="bi bi-clock me-1"></i><?= $e['dias_atraso'] ?> dia(s) atrasado</span>
            <?php else: ?>
              <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Em Aberto</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="card-body text-center text-muted py-5">
    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
    <p class="mb-3">Você ainda não fez nenhum empréstimo.</p>
    <a href="solicitar.php" class="btn btn-primary"><i class="bi bi-arrow-right-circle me-2"></i>Solicitar agora</a>
  </div>
  <?php endif; ?>
</div>

<?php rodape(); ?>

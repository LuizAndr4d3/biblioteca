<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Emprestimo.php';
require_once __DIR__ . '/../../classes/Livro.php';
require_once __DIR__ . '/../../classes/Usuario.php';

verificarLogin();

$uid    = usuarioLogadoId();
$usu    = new Usuario();
$liv    = new Livro();
$prazo  = date('Y-m-d', strtotime('+' . Emprestimo::PRAZO_PADRAO_DIAS . ' days'));

// Pré-selecionar livro via GET
$livroPreSel = (int)($_GET['livro_id'] ?? 0);

// Buscar livros disponíveis
$db  = getConexao();
$sql = "SELECT l.*, a.nome AS autor_nome,
               (l.quantidade - COALESCE(e.emprestados,0)) AS disponivel
        FROM livros l
        INNER JOIN autores a ON a.id = l.autor_id
        LEFT JOIN (
            SELECT livro_id, COUNT(*) AS emprestados
            FROM emprestimos WHERE devolvido=0 GROUP BY livro_id
        ) e ON e.livro_id = l.id
        HAVING disponivel > 0
        ORDER BY l.titulo";
$livros = $db->query($sql)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!$usu->podeEmprestar($uid)) {
            throw new RuntimeException("Você atingiu o limite de " . Usuario::LIMITE_EMPRESTIMOS . " empréstimos simultâneos ou sua conta está inativa.");
        }
        (new Emprestimo())->criar(
            (int)($_POST['livro_id']   ?? 0),
            $uid,
            $_POST['data_devolucao']   ?? $prazo,
            $_POST['observacao']       ?? ''
        );
        redirecionar('/biblioteca/pages/usuario/meus-emprestimos.php', 'Empréstimo solicitado com sucesso! Retire o livro na biblioteca.');
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}

cabecalho('Solicitar Empréstimo');
?>

<div class="row justify-content-center">
  <div class="col-lg-7">

    <!-- Regras -->
    <div class="card mb-4" style="border-left:4px solid #6366f1 !important;border-radius:12px !important">
      <div class="card-body py-3">
        <h6 class="fw-bold text-primary mb-2"><i class="bi bi-info-circle me-2"></i>Regras de Empréstimo</h6>
        <div class="row g-2 small text-muted">
          <div class="col-md-4"><i class="bi bi-calendar-check me-1 text-success"></i>Prazo: <?= Emprestimo::PRAZO_PADRAO_DIAS ?> dias</div>
          <div class="col-md-4"><i class="bi bi-stack me-1 text-warning"></i>Limite: <?= Usuario::LIMITE_EMPRESTIMOS ?> livros</div>
          <div class="col-md-4"><i class="bi bi-person-check me-1 text-primary"></i>Conta deve estar ativa</div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header py-3 fw-semibold"><i class="bi bi-arrow-right-circle me-2 text-primary"></i>Nova Solicitação</div>
      <div class="card-body p-4">
        <?php if (!empty($erro)) alerta('danger', $erro); ?>

        <?php if (!$livros): ?>
          <div class="text-center text-muted py-4">
            <i class="bi bi-emoji-frown fs-1 d-block mb-2"></i>
            Nenhum livro disponível no momento. Volte mais tarde!
          </div>
        <?php else: ?>
        <form method="post">
          <div class="mb-4">
            <label class="form-label">Escolha o livro *</label>
            <select name="livro_id" class="form-select" required>
              <option value="">Selecione um título…</option>
              <?php foreach ($livros as $l): ?>
                <option value="<?= $l['id'] ?>" <?= (($_POST['livro_id'] ?? $livroPreSel) == $l['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($l['titulo']) ?> — <?= htmlspecialchars($l['autor_nome']) ?>
                  (<?= $l['disponivel'] ?> disponível)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-4">
            <label class="form-label">Data de devolução *</label>
            <input type="date" name="data_devolucao" class="form-control" required
                   min="<?= date('Y-m-d') ?>"
                   value="<?= $_POST['data_devolucao'] ?? $prazo ?>">
            <div class="form-text">Prazo padrão sugerido: <?= date('d/m/Y', strtotime($prazo)) ?></div>
          </div>
          <div class="mb-4">
            <label class="form-label">Observação <span class="text-muted fw-normal">(opcional)</span></label>
            <textarea name="observacao" class="form-control" rows="2" placeholder="Algum recado…"><?= htmlspecialchars($_POST['observacao'] ?? '') ?></textarea>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
              <i class="bi bi-check-lg me-2"></i>Confirmar Solicitação
            </button>
            <a href="catalogo.php" class="btn btn-outline-secondary">Voltar ao Catálogo</a>
          </div>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php rodape(); ?>

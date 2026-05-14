<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Livro.php';
require_once __DIR__ . '/../../classes/Categoria.php';

verificarLogin();

$liv    = new Livro();
$cat    = new Categoria();
$cats   = $cat->listarTodas();

// Filtro de categoria
$catFiltro = (int)($_GET['categoria'] ?? 0);
$busca     = trim($_GET['busca'] ?? '');

$db   = getConexao();
$sql  = "SELECT l.*, a.nome AS autor_nome, c.nome AS categoria_nome,
                (l.quantidade - COALESCE(e.emprestados,0)) AS disponivel
         FROM livros l
         INNER JOIN autores    a ON a.id = l.autor_id
         INNER JOIN categorias c ON c.id = l.categoria_id
         LEFT JOIN (
             SELECT livro_id, COUNT(*) AS emprestados
             FROM emprestimos WHERE devolvido=0 GROUP BY livro_id
         ) e ON e.livro_id = l.id
         WHERE 1=1";
$params = [];

if ($catFiltro) {
    $sql .= " AND l.categoria_id = :cat";
    $params[':cat'] = $catFiltro;
}
if ($busca) {
    $sql .= " AND (l.titulo LIKE :busca OR a.nome LIKE :busca)";
    $params[':busca'] = "%$busca%";
}
$sql .= " ORDER BY l.titulo";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$livros = $stmt->fetchAll();

cabecalho('Catálogo de Livros');
?>

<!-- Filtros -->
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="get" class="row g-2 align-items-center">
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
          <input type="text" name="busca" class="form-control border-start-0" placeholder="Buscar por título ou autor…"
                 value="<?= htmlspecialchars($busca) ?>" style="border-radius:0 10px 10px 0 !important">
        </div>
      </div>
      <div class="col-md-4">
        <select name="categoria" class="form-select">
          <option value="">Todas as categorias</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $catFiltro == $c['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-funnel me-1"></i>Filtrar</button>
        <a href="catalogo.php" class="btn btn-outline-secondary ms-1">Limpar</a>
      </div>
    </form>
  </div>
</div>

<p class="text-muted small mb-3"><?= count($livros) ?> livro(s) encontrado(s)</p>

<!-- Grid de livros -->
<div class="row g-3">
<?php if ($livros): foreach ($livros as $l): ?>
  <div class="col-sm-6 col-md-4 col-xl-3">
    <div class="card h-100" style="transition:.2s;cursor:default" onmouseenter="this.style.transform='translateY(-3px)'" onmouseleave="this.style.transform=''">
      <!-- Capa fictícia com cor baseada na categoria -->
      <div style="height:130px;border-radius:14px 14px 0 0;display:flex;align-items:center;justify-content:center;
                  background:linear-gradient(135deg,<?= ['#6366f1','#059669','#d97706','#dc2626','#2563eb','#7c3aed'][($l['categoria_id']-1) % 6] ?>,
                  <?= ['#818cf8','#34d399','#fbbf24','#f87171','#60a5fa','#a78bfa'][($l['categoria_id']-1) % 6] ?>)">
        <i class="bi bi-book-half text-white" style="font-size:3rem;opacity:.7"></i>
      </div>
      <div class="card-body d-flex flex-column p-3">
        <h6 class="fw-bold mb-1" style="font-size:.88rem;line-height:1.3"><?= htmlspecialchars($l['titulo']) ?></h6>
        <p class="text-muted mb-1" style="font-size:.78rem"><?= htmlspecialchars($l['autor_nome']) ?></p>
        <span class="badge mb-2" style="background:#ede9fe;color:#5b21b6;width:fit-content;font-size:.7rem">
          <?= htmlspecialchars($l['categoria_nome']) ?>
        </span>
        <div class="mt-auto d-flex align-items-center justify-content-between">
          <?php if ($l['disponivel'] > 0): ?>
            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i><?= $l['disponivel'] ?> disp.</span>
            <a href="/biblioteca/pages/usuario/solicitar.php?livro_id=<?= $l['id'] ?>"
               class="btn btn-sm btn-primary" style="font-size:.75rem">Pegar</a>
          <?php else: ?>
            <span class="badge bg-secondary">Indisponível</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; else: ?>
  <div class="col-12">
    <div class="card p-5 text-center text-muted">
      <i class="bi bi-search fs-1 d-block mb-2"></i>
      Nenhum livro encontrado com os filtros selecionados.
    </div>
  </div>
<?php endif; ?>
</div>

<?php rodape(); ?>

<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Emprestimo.php';
$id = (int)($_GET['id'] ?? 0);
try {
    (new Emprestimo())->deletar($id);
    redirecionar('/biblioteca/pages/emprestimos/index.php', 'Empréstimo excluído com sucesso!');
} catch (Exception $e) {
    redirecionar('/biblioteca/pages/emprestimos/index.php', $e->getMessage(), 'danger');
}

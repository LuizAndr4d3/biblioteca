<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Emprestimo.php';
$id = (int)($_GET['id'] ?? 0);
try {
    (new Emprestimo())->registrarDevolucao($id);
    redirecionar('/biblioteca/pages/emprestimos/index.php', 'Devolução registrada com sucesso!');
} catch (Exception $e) {
    redirecionar('/biblioteca/pages/emprestimos/index.php', $e->getMessage(), 'danger');
}

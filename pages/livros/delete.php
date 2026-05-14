<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Livro.php';
$id = (int)($_GET['id'] ?? 0);
try {
    (new Livro())->deletar($id);
    redirecionar('/biblioteca/pages/livros/index.php', 'Livro excluído com sucesso!');
} catch (Exception $e) {
    redirecionar('/biblioteca/pages/livros/index.php', $e->getMessage(), 'danger');
}

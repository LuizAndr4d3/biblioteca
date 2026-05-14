<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Categoria.php';
$id = (int)($_GET['id'] ?? 0);
try {
    (new Categoria())->deletar($id);
    redirecionar('/biblioteca/pages/categorias/index.php', 'Categoria excluída com sucesso!');
} catch (Exception $e) {
    redirecionar('/biblioteca/pages/categorias/index.php', $e->getMessage(), 'danger');
}

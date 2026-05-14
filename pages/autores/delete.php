<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Autor.php';
$id = (int)($_GET['id'] ?? 0);
try {
    (new Autor())->deletar($id);
    redirecionar('/biblioteca/pages/autores/index.php', 'Autor excluído com sucesso!');
} catch (Exception $e) {
    redirecionar('/biblioteca/pages/autores/index.php', $e->getMessage(), 'danger');
}

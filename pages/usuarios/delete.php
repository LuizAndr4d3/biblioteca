<?php
require_once __DIR__ . '/../../config/auth.php';
verificarAdmin();
require_once __DIR__ . '/../../config/layout.php';
require_once __DIR__ . '/../../classes/Usuario.php';
$id = (int)($_GET['id'] ?? 0);
try {
    (new Usuario())->deletar($id);
    redirecionar('/biblioteca/pages/usuarios/index.php', 'Usuário excluído com sucesso!');
} catch (Exception $e) {
    redirecionar('/biblioteca/pages/usuarios/index.php', $e->getMessage(), 'danger');
}

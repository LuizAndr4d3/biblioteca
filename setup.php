<?php
/**
 * SETUP INICIAL — Cria os usuários padrão com senhas criptografadas.
 * Execute UMA VEZ pelo navegador: http://localhost/biblioteca/setup.php
 * APAGUE este arquivo após executar!
 */
require_once __DIR__ . '/config/database.php';

$db = getConexao();

$usuarios = [
    [
        'nome'     => 'Administrador',
        'email'    => 'admin@biblioteca.com',
        'senha'    => password_hash('admin123', PASSWORD_DEFAULT),
        'role'     => 'admin',
        'telefone' => '',
        'status'   => 'ativo',
    ],
    [
        'nome'     => 'João Silva',
        'email'    => 'joao@email.com',
        'senha'    => password_hash('user123', PASSWORD_DEFAULT),
        'role'     => 'usuario',
        'telefone' => '(11) 91234-5678',
        'status'   => 'ativo',
    ],
    [
        'nome'     => 'Maria Costa',
        'email'    => 'maria@email.com',
        'senha'    => password_hash('user123', PASSWORD_DEFAULT),
        'role'     => 'usuario',
        'telefone' => '(21) 98765-4321',
        'status'   => 'ativo',
    ],
];

$sql = "INSERT INTO usuarios (nome, email, senha, role, telefone, status)
        VALUES (:nome, :email, :senha, :role, :telefone, :status)
        ON DUPLICATE KEY UPDATE senha = VALUES(senha), role = VALUES(role)";

$stmt = $db->prepare($sql);
$criados = 0;
foreach ($usuarios as $u) {
    $stmt->execute($u);
    $criados++;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Setup — Biblioteca</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
<div class="card shadow p-5 text-center" style="max-width:480px;width:100%">
    <div class="display-1 text-success mb-3">✅</div>
    <h4 class="fw-bold mb-3">Setup concluído!</h4>
    <p class="text-muted"><?= $criados ?> usuário(s) criado(s) com sucesso.</p>
    <hr>
    <div class="text-start small">
        <p class="mb-1"><strong>Admin:</strong></p>
        <p class="text-muted">E-mail: <code>admin@biblioteca.com</code> | Senha: <code>admin123</code></p>
        <p class="mb-1"><strong>Usuário comum:</strong></p>
        <p class="text-muted mb-0">E-mail: <code>joao@email.com</code> | Senha: <code>user123</code></p>
    </div>
    <hr>
    <div class="alert alert-danger py-2 small">
        ⚠️ <strong>Apague este arquivo após o setup!</strong><br>
        <code>delete: biblioteca/setup.php</code>
    </div>
    <a href="/biblioteca/login.php" class="btn btn-primary mt-2">Ir para o Login →</a>
</div>
</body>
</html>

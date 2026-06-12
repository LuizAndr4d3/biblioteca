<?php
/**
 * SETUP — Utilitário de reset de senhas.
 *
 * Use apenas se precisar recriar os usuários padrão
 * (ex.: alterou as senhas e quer voltar ao estado inicial).
 *
 * Os usuários padrão já são criados pelo database.sql.
 *
 * ⚠️  APAGUE ESTE ARQUIVO após usar em produção!
 */
require_once __DIR__ . '/config/database.php';

$db = getConexao();

$usuarios = [
    ['nome'=>'Administrador', 'email'=>'admin@biblioteca.com', 'senha'=>password_hash('admin123', PASSWORD_DEFAULT), 'role'=>'admin',   'telefone'=>''],
    ['nome'=>'João Silva',    'email'=>'joao@email.com',        'senha'=>password_hash('user123',  PASSWORD_DEFAULT), 'role'=>'usuario', 'telefone'=>'(11) 91234-5678'],
    ['nome'=>'Maria Costa',   'email'=>'maria@email.com',       'senha'=>password_hash('user123',  PASSWORD_DEFAULT), 'role'=>'usuario', 'telefone'=>'(21) 98765-4321'],
];

$sql  = "INSERT INTO usuarios (nome, email, senha, role, telefone, status)
         VALUES (:nome, :email, :senha, :role, :telefone, 'ativo')
         ON DUPLICATE KEY UPDATE senha = VALUES(senha), role = VALUES(role), status = 'ativo'";
$stmt = $db->prepare($sql);
foreach ($usuarios as $u) $stmt->execute($u);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Setup — BiblioSys</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
<div class="card shadow p-5 text-center" style="max-width:480px;width:100%">
    <div class="display-1 text-success mb-3">✅</div>
    <h4 class="fw-bold mb-3">Usuários recriados com sucesso!</h4>
    <hr>
    <div class="text-start small">
        <p class="mb-1"><strong>Admin:</strong></p>
        <p class="text-muted">E-mail: <code>admin@biblioteca.com</code> | Senha: <code>admin123</code></p>
        <p class="mb-1 mt-2"><strong>Leitores:</strong></p>
        <p class="text-muted mb-0">
            <code>joao@email.com</code> / <code>user123</code><br>
            <code>maria@email.com</code> / <code>user123</code>
        </p>
    </div>
    <div class="alert alert-danger py-2 small mt-3 mb-0">
        ⚠️ <strong>Apague este arquivo após o uso!</strong>
    </div>
    <a href="/biblioteca/login.php" class="btn btn-primary mt-3">Ir para o Login →</a>
</div>
</body>
</html>

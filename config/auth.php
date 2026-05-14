<?php
/**
 * Funções de autenticação e controle de acesso por role.
 */

function iniciarSessao(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/** Redireciona para login se não estiver autenticado. */
function verificarLogin(): void {
    iniciarSessao();
    if (empty($_SESSION['usuario_id'])) {
        header('Location: /biblioteca/login.php');
        exit;
    }
}

/** Redireciona para área do usuário se não for admin. */
function verificarAdmin(): void {
    verificarLogin();
    if (($_SESSION['usuario_role'] ?? '') !== 'admin') {
        header('Location: /biblioteca/pages/usuario/dashboard.php');
        exit;
    }
}

/** Retorna true se o usuário logado for admin. */
function isAdmin(): bool {
    iniciarSessao();
    return ($_SESSION['usuario_role'] ?? '') === 'admin';
}

/** Retorna o ID do usuário logado ou null. */
function usuarioLogadoId(): ?int {
    iniciarSessao();
    return isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;
}

/** Retorna o nome do usuário logado. */
function usuarioLogadoNome(): string {
    iniciarSessao();
    return $_SESSION['usuario_nome'] ?? 'Usuário';
}

/** Retorna a role do usuário logado. */
function usuarioLogadoRole(): string {
    iniciarSessao();
    return $_SESSION['usuario_role'] ?? 'usuario';
}

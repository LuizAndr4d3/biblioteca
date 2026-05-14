<?php
// ============================================================
// Configuração da Conexão com o Banco de Dados
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'biblioteca_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getConexao(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $opcoes = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcoes);
        } catch (PDOException $e) {
            die('<div style="color:red;font-family:sans-serif;padding:20px;">
                <h2>Erro de Conexão com o Banco de Dados</h2>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Verifique as configurações em <code>config/database.php</code></p>
            </div>');
        }
    }
    return $pdo;
}

<?php
// Configuração de cookies de sessão (antes de session_start)
ini_set('session.cookie_secure', 1); // Apenas em conexões HTTPS
ini_set('session.cookie_httponly', 1); // Impedir acesso via JavaScript
ini_set('session.use_only_cookies', 1); // Apenas cookies para sessão

// Inicia a sessão
session_start();

// Cabeçalhos de segurança
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\';');

// Desabilita a exibição de erros em produção
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error_log'); // Define o caminho para o arquivo de log de erros

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'fiap');

// Utilizando PDO para segurança contra SQL Injection
function getDBConnection() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
        $conn = new PDO($dsn, DB_USER, DB_PASS);
        
        // Definir modo de erro para exceções
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $conn;
    } catch (PDOException $e) {
        // Loga o erro sem exibi-lo ao usuário
        error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
        die("Erro na conexão ao banco de dados.");
    }
}
?>

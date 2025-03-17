<?php
session_start();

// Proteção contra CSRF
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

function verifyCsrfToken($token) {
    return $token === $_SESSION['csrf_token'];
}

function isUserLoggedIn() {
    // Verificar se as variáveis de sessão existem
    if (isset($_SESSION['usuario']) && isset($_SESSION['permissao'])) {
        return true;
    }
    return false;
}

// Regenerar ID da sessão para evitar ataque de fixação
session_regenerate_id(true);
?>

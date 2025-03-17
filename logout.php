<?php
// Inicia a sessão
session_start();

// Regenera o ID da sessão antes de destruir
session_regenerate_id(true);

// Destrói a sessão de forma segura
$_SESSION = array(); // Limpa todas as variáveis de sessão
session_unset(); // Destrói todas as variáveis da sessão
session_destroy(); // Destrói a sessão

// Remove o cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000, 
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Redireciona para a página inicial de login
header("Location: index.php");
exit();
?>

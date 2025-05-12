<?php

include 'includes/config.php'; // Arquivo de configuração do banco

// Gerar o token CSRF se não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Gera um token CSRF seguro
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    // Verificar se o token CSRF é válido
    if ($csrf_token !== $_SESSION['csrf_token']) {
        die("Token CSRF inválido.");
    }

    if (empty($usuario) || empty($senha)) {
        $_SESSION['erro_login'] = "Por favor, preencha todos os campos.";
        header("Location: index.php");
        exit();
    }

    // Conexão com o banco de dados
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar se a conexão foi bem-sucedida
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    // Consulta para buscar o usuário no banco de dados
    $sql = "SELECT id, senha, permissao FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);


    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verifica se a senha está correta
        if (trim($senha) === trim($user['senha'])) {
            // Senha correta, iniciar a sessão
            $_SESSION['usuario'] = $usuario; 
            $_SESSION['permissao'] = $user['permissao'];
            $_SESSION['user_id'] = $user['id'];  // Guardar o ID do usuário na sessão
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['erro_login'] = "Senha inválida!";
            echo "Senha inválida!";
        }
    } else {
        $_SESSION['erro_login'] = "Usuário não encontrado!";
        echo "Usuário não encontrado!";
    }

    // Fecha a conexão com o banco
    $stmt->close();
    $conn->close();

    header("Location: index.php"); // Redireciona de volta para a tela de login
    exit();
}
?>

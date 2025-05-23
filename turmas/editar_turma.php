<?php
include '../includes/auth.php';
include '../header.php';
include '../includes/session.php';  // Incluindo o arquivo de sessão para ter acesso às funções
include '../includes/db.php';

// Verifica se o usuário está logado e se tem permissão de admin
if (!isUserLoggedIn() || $_SESSION['permissao'] != 'admin') {
    header("Location: ../index.php"); // Redireciona para a validação de login
    exit();
}

// Verifica se a turma foi especificada
if (!isset($_GET['id'])) {
    echo "ID da turma não especificado!";
    exit();
}

$idTurma = $_GET['id'];

// Busca a turma a ser editada
$result = $conn->query("SELECT * FROM turmas WHERE id = $idTurma");
if ($result->num_rows == 0) {
    echo "Turma não encontrada!";
    exit();
}

$turma = $result->fetch_assoc();

// Atualiza os dados da turma
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $tipo = $_POST['tipo'];

    // Atualiza a turma no banco de dados
    $updateSql = "UPDATE turmas SET nome = ?, descricao = ?, tipo = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param('sssi', $nome, $descricao, $tipo, $idTurma);
    if ($stmt->execute()) {
        header("Location: gerenciar_turmas.php");
        exit();
    } else {
        echo "Erro ao atualizar turma!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Turma</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/main-content.css">
    <link rel="stylesheet" href="../assets/css/styles_turmas.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-header">
                <svg class="components-menu-svg" viewBox="0 0 576.206 157.976" fill="#ed145b">
                    <title>Logotipo FIAP</title>
                    <path d="M164.733 3.17h11.35v152.805h-11.35z"></path>
                    <path fill="none" d="M164.733 3.17h11.35v152.805h-11.35z"></path>
                    <path d="M44.387 76.298h55.408v10.478H44.387z"></path>
                    <path fill="none" d="M44.387 76.298h55.408v10.478H44.387z"></path>
                    <path d="M2 3.17v152.806h11.35v-69.2h.015V76.297h-.014v-62.65h117.696V3.17"></path>
                    <path fill="none" d="M2 3.17v152.806h11.35v-69.2h.015V76.297h-.014v-62.65h117.696V3.17"></path>
                    <path d="M517.45 2.58h-90.446v152.806h11.35V98.41h.083V87.935h-.082V13.06h78.223c27.505 0 46.277 12.66 46.277 36.89v.438c0 22.7-19.21 37.546-47.588 37.546H479.47V98.41h35.142c31.87 0 59.595-16.59 59.595-48.678v-.436c0-29.47-23.14-46.715-56.758-46.715"></path>
                    <path fill="none" d="M517.45 2.58h-90.446v152.806h11.35V98.41h.083V87.935h-.082V13.06h78.223c27.505 0 46.277 12.66 46.277 36.89v.438c0 22.7-19.21 37.546-47.588 37.546H479.47V98.41h35.142c31.87 0 59.595-16.59 59.595-48.678v-.436c0-29.47-23.14-46.715-56.758-46.715"></path>
                    <path d="M360.968 87.935L307.978 2h-10.915l-94.387 153.897h11.788l87.84-141.453 45.786 73.49"></path>
                    <path fill="none" d="M360.968 87.935L307.978 2h-10.915l-94.387 153.897h11.788l87.84-141.453 45.786 73.49"></path>
                    <path d="M378.403 116.21h-12.697l24.727 39.686h12.44"></path>
                    <path fill="none" d="M378.403 116.21h-12.697l24.727 39.686h12.44"></path>
                </svg>
            </div>
            <ul class="sidebar-nav">
                <li><a href="gerenciar_turmas.php">Voltar</a></li>
            </ul>
        </div>

        <div class="main-content">
            <header>
                <h1>Editar Turma</h1>
            </header>

            <form method="POST" class="form-turma">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" value="<?= htmlspecialchars($turma['nome']) ?>" required><br><br>
                
                <label for="descricao">Descrição:</label>
                <textarea name="descricao" required><?= htmlspecialchars($turma['descricao']) ?></textarea><br><br>
                
                <label for="tipo">Tipo:</label>
                <input type="text" name="tipo" value="<?= htmlspecialchars($turma['tipo']) ?>" required><br><br>
                
                <button type="submit" class="interna_turma">Atualizar</button>
            </form>
        </div>
    </div>
</body>
</html>

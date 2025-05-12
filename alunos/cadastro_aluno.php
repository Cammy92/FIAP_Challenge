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

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Aluno</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/main-content.css">
    <link rel="stylesheet" href="../assets/css/styles_alunos.css">
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
                <li><a href="gerenciar_alunos.php">Voltar</a></li>
            </ul>
        </div>

        <div class="main-content">
            <header>
                <h1>Cadastrar Novo Aluno</h1>
            </header>

            <form method="POST" class="interna_aluno">
                <label for="nome">Nome do Aluno: </label>
                <input type="text" name="nome" required placeholder="Nome do aluno" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" /><br><br>

                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" name="data_nascimento" required><br><br>

                <label for="usuario">Usuário (nickname):</label>
                <input type="text" name="usuario" required placeholder="ex: ca_ramos" value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>"><br><br>


                <button type="submit" class="interna_aluno">Cadastrar</button>
            </form>

            <?php
                // Declara a data atual antes das verificações
                $data_atual = date('Y-m-d'); // Data atual no formato YYYY-MM-DD
            
                // Verifica se o formulário foi enviado
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $nome = $_POST['nome'];
                    $data_nascimento = $_POST['data_nascimento'];
                    $usuario = $_POST['usuario'];

                    if (empty($nome) || empty($data_nascimento) || empty($usuario)) {
                        echo "<p class='erro'>Por favor, preencha todos os campos.</p>";

                    // Verifica se a data de nascimento é menor que a data atual
                    } elseif ($data_nascimento >= $data_atual) {
                        echo "<p class='erro'>A data de nascimento deve ser inferior à data atual.</p>";
                    } elseif (strlen($nome) < 3) {  // Verifica se o nome tem pelo menos 3 caracteres
                        echo "<p class='erro'>O nome do aluno deve ter pelo menos 3 caracteres.</p>";
                    }  else {
                        // Inserir o aluno no banco de dados
                        $stmt = $conn->prepare("INSERT INTO alunos (nome, data_nascimento, usuario) VALUES (?, ?, ?)");
                        $stmt->bind_param('sss', $nome, $data_nascimento, $usuario);
                        $stmt->execute();

                        $sucesso = "Aluno cadastrado com sucesso!";
                        $_SESSION['mensagem_sucesso'] = $sucesso;
                        // Redireciona para a página de gerenciamento de alunos após o cadastro
                        header("Location: gerenciar_alunos.php");
                        exit();
                    }
                }
            ?>

        </div>
    </div>
</body>
</html>

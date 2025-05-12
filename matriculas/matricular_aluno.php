<?php
include '../includes/auth.php';
include '../header.php';
include '../includes/session.php';  // Sessão de controle de login
include '../includes/db.php';        // Conexão com o banco

// Verifica se o usuário está logado e se tem permissão de admin
if (!isUserLoggedIn() || $_SESSION['permissao'] != 'admin') {
    header("Location: ../index.php"); // Redireciona para a validação de login
    exit();
}

// Busca todas as turmas
$sqlTurmas = "SELECT id, nome FROM turmas ORDER BY nome ASC";
$turmas = $conn->query($sqlTurmas);

// Busca todos os alunos
$sqlAlunos = "SELECT id, nome FROM alunos";
$alunos = $conn->query($sqlAlunos);

// Processa cadastro de matrícula
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_aluno = $_POST['aluno'];
    $id_turma = $_POST['turma'];

    // Verifica se o aluno já está matriculado na turma
    $sqlCheck = "SELECT COUNT(*) as total FROM matriculas WHERE aluno_id = ? AND turma_id = ?";
    $stmt = $conn->prepare($sqlCheck);
    $stmt->bind_param("ii", $id_aluno, $id_turma);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['total'] == 0) {
        // Realiza a matrícula
        $sqlInsert = "INSERT INTO matriculas (aluno_id, turma_id, data_matricula) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sqlInsert);
        $stmt->bind_param("ii", $id_aluno, $id_turma);
        if ($stmt->execute()) {

            $sucesso = "Matrícula realizada com sucesso!";
            $_SESSION['mensagem_sucesso'] = $sucesso;
            // Redireciona para evitar reenvio de formulário
            header("Location: gerenciar_matriculas.php");
            exit;
        } else {
            $erro = "Erro ao cadastrar a matrícula.";
        }
    } else {
        $erro = "<p class='erro'>O aluno já está matriculado nesta turma.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Matrícula</title>
    <link rel="stylesheet" href="../assets/css/styles_matriculas.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/main-content.css">
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
                <li><a href="gerenciar_matriculas.php">Voltar</a></li>
            </ul>
        </div>

        <div class="main-content">
            <header>
                <h1>Cadastro de Matrícula</h1>
            </header>

            <?php if ($erro): ?>
                <p style="color: red;"><?= $erro ?></p>
            <?php elseif ($sucesso): ?>
                <p style="color: green;"><?= $sucesso ?></p>
            <?php endif; ?>

            <form method="POST" class="interna_matricula">
                <label for="turma">Selecione a Turma:</label>
                <select name="turma" id="turma" required>
                    <?php while ($turma = $turmas->fetch_assoc()) { ?>
                        <option value="<?= $turma['id'] ?>" 
                            <?= isset($_POST['turma']) && $_POST['turma'] == $turma['id'] ? 'selected' : '' ?>>
                            <?= $turma['nome'] ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="aluno">Selecione o Aluno:</label>
                <select name="aluno" id="aluno" required>
                    <?php while ($aluno = $alunos->fetch_assoc()) { ?>
                        <option value="<?= $aluno['id'] ?>" 
                            <?= isset($_POST['aluno']) && $_POST['aluno'] == $aluno['id'] ? 'selected' : '' ?>>
                            <?= $aluno['nome'] ?>
                        </option>
                    <?php } ?>
                </select>

                <button type="submit" class="interna_matricula">Cadastrar Matrícula</button>
            </form>
        </div>
    </div>
</body>
</html>

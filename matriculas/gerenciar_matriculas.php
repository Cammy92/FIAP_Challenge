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

if(isset($_SESSION['mensagem_sucesso'])){
    $sucesso = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']);
}

// Buscar turmas
$turmas = [];
$sqlTurmas = "SELECT id, nome FROM turmas ORDER BY nome ASC";
if ($result = $conn->query($sqlTurmas)) {
    while ($row = $result->fetch_assoc()) {
        $turmas[] = $row;
    }
}

// Exclusão de matrícula
if (isset($_GET['excluir'])) {
    $matricula_id = $_GET['excluir'];
    $stmt = $conn->prepare("DELETE FROM matriculas WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $matricula_id);
        if ($stmt->execute()) {
            $sucesso = "Matrícula excluída com sucesso!";
        } else {
            $erro = "Erro ao excluir matrícula.";
        }
        $stmt->close();
    } else {
        $erro = "Erro ao preparar exclusão.";
    }
}

// Filtros
$whereClauses = [];
$params = [];
$tipos = '';

// Recebe dados do formulário de busca
$nome_turma = $_GET['nome_turma'] ?? '';
$nome_aluno = $_GET['nome_aluno'] ?? '';
$data_matricula = $_GET['data_matricula'] ?? '';

if (isset($_GET['buscar'])) {
    if (!empty($nome_turma)) {
        $whereClauses[] = "turmas.id = ?";
        $params[] = $nome_turma;
        $tipos .= 'i';
    }
    if (!empty($nome_aluno)) {
        $whereClauses[] = "alunos.nome LIKE ?";
        $params[] = '%' . $nome_aluno . '%';
        $tipos .= 's';
    }
    if (!empty($data_matricula)) {
        $whereClauses[] = "DATE(matriculas.data_matricula) = ?";
        $params[] = $data_matricula;
        $tipos .= 's';
    }
}

// Paginação
$matriculasPorPagina = 5;
$paginaAtual = max(1, (int)($_GET['pagina'] ?? 1));
$inicio = ($paginaAtual - 1) * $matriculasPorPagina;

// Consulta principal
$sqlMatriculas = "
    SELECT matriculas.id AS matricula_id, 
           alunos.id AS aluno_id, 
           alunos.nome AS aluno_nome, 
           matriculas.data_matricula, 
           turmas.nome AS turma_nome 
    FROM matriculas
    INNER JOIN alunos ON alunos.id = matriculas.aluno_id
    INNER JOIN turmas ON matriculas.turma_id = turmas.id";

// Aplica filtros
if ($whereClauses) {
    $sqlMatriculas .= ' WHERE ' . implode(' AND ', $whereClauses);
}

$sqlMatriculas .= " ORDER BY alunos.nome ASC, turmas.nome ASC";
$sqlMatriculas .= " LIMIT ?, ?";

// Acrescenta paginação nos parâmetros
$params[] = $inicio;
$params[] = $matriculasPorPagina;
$tipos .= 'ii';

// Prepara e executa
$stmt = $conn->prepare($sqlMatriculas);
if ($stmt) {
    $stmt->bind_param($tipos, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $matriculas = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $erro = "Erro na consulta de matrículas.";
}

// Contagem total para paginação
$countSql = "SELECT COUNT(*) as total FROM matriculas";
$countResult = $conn->query($countSql);
$totalMatriculas = ($countResult) ? $countResult->fetch_assoc()['total'] : 0;
$totalPaginas = ceil($totalMatriculas / $matriculasPorPagina);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Matrículas</title>
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
                <li><a href="../dashboard.php">Voltar</a></li>
            </ul>
        </div>

        <div class="main-content">
            <header>
                <h1>Gerenciar Matrículas</h1>
            </header>

            <h3><a href="matricular_aluno.php">Cadastrar Matrícula</a></h3>

            <form method="GET">
                <label for="nome_turma">Nome da Turma:</label>
                <select name="nome_turma" id="nome_turma">
                    <option value="">Selecione uma turma</option>
                    <?php foreach ($turmas as $turma) { ?>
                        <option value="<?= $turma['id'] ?>" <?= isset($_GET['nome_turma']) && $_GET['nome_turma'] == $turma['id'] ? 'selected' : '' ?>><?= $turma['nome'] ?></option>
                    <?php } ?>
                </select>
                <label for="nome_aluno">Nome do Aluno:</label>
                <input type="text" name="nome_aluno" value="<?= isset($_GET['nome_aluno']) ? $_GET['nome_aluno'] : '' ?>">
                <label for="data_matricula">Data da Matrícula:</label>
                <input type="date" name="data_matricula" value="<?= isset($_GET['data_matricula']) ? $_GET['data_matricula'] : '' ?>">
                <button type="submit" name="buscar">Buscar</button>
                <button type="button" onclick="window.location.href='gerenciar_matriculas.php'">Limpar Busca</button>
            </form>

            <?php if (isset($erro)): ?>
                <p style="color: red;"><?= $erro ?></p>
            <?php endif; ?>
            <?php if (isset($sucesso)): ?>
                <p style="color: green;"><?= $sucesso ?></p>
            <?php endif; ?>
            
            <?php if (count($matriculas) > 0) { ?>

            <table>
                <thead>
                    <tr>
                        <th>Nome do Aluno</th>
                        <th>Nome da Turma</th>
                        <th>Data da Matrícula</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matriculas as $matricula): ?>
                        <tr>
                            <td><?= htmlspecialchars($matricula['aluno_nome']) ?></td>
                            <td><?= htmlspecialchars($matricula['turma_nome']) ?></td>
                            <td><?= htmlspecialchars($matricula['data_matricula']) ?></td>
                            <td>
                                <a href="?excluir=<?= $matricula['matricula_id'] ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                                
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalMatriculas > 5): ?>
                <div class="pagination">
                    <?php 
                    $queryParams = $_GET;
                    unset($queryParams['pagina']);
                    
                    $queryString = http_build_query($queryParams);
                    ?>
                    
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <a href="?<?= $queryString ?>&pagina=<?= $i ?>" class="<?= ($i == $paginaAtual) ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <?php } else { ?>
                <p>Nenhuma matrícula encontrada.</p>
            <?php } ?>

        </div>
    </div>
</body>
</html>
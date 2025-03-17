<?php
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

// Busca todas as turmas
$sqlTurmas = "SELECT id, nome FROM turmas ORDER BY nome ASC";
$turmasResult = $conn->query($sqlTurmas);
$turmas = [];
while ($turma = $turmasResult->fetch_assoc()) {
    $turmas[] = $turma;
}

// Verifica se existe um ID de matrícula para exclusão
if (isset($_GET['excluir'])) {
    $matricula_id = $_GET['excluir'];

    // Exclui a matrícula do banco de dados
    $sqlExcluir = "DELETE FROM matriculas WHERE id = ?";
    $stmt = $conn->prepare($sqlExcluir);
    
    if ($stmt) {
        $stmt->bind_param("i", $matricula_id);  // Vincula o ID da matrícula
        if ($stmt->execute()) {
            $sucesso = "Matrícula excluída com sucesso!";
        } else {
            $erro = "Erro ao excluir a matrícula.";
        }
        $stmt->close();
    } else {
        $erro = "Erro na preparação da consulta.";
    }
}


// Define filtros de busca
$whereClauses = [];
$params = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['buscar'])) {
    $nome_turma = isset($_GET['nome_turma']) ? $_GET['nome_turma'] : '';
    $nome_aluno = isset($_GET['nome_aluno']) ? $_GET['nome_aluno'] : '';
    $data_matricula = isset($_GET['data_matricula']) ? $_GET['data_matricula'] : '';

    // Filtros de busca
    if (!empty($nome_turma)) {
        $whereClauses[] = "turmas.id = ?";
        $params[] = $nome_turma;
    }

    if (!empty($nome_aluno)) {
        $whereClauses[] = "alunos.nome LIKE ?";
        $params[] = "%" . $nome_aluno . "%";
    }

    if (!empty($data_matricula)) {
        $whereClauses[] = "DATE(matriculas.data_matricula) = ?";
        $params[] = $data_matricula;
    }
}

// Define a quantidade de matrículas por página
$matriculasPorPagina = 5;

// Determinar a página atual
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaAtual = ($paginaAtual > 0) ? $paginaAtual : 1;  // Se a página for menor que 1, definir como 1

// Calcular o limite de matrículas a serem exibidas na consulta
$inicio = ($paginaAtual - 1) * $matriculasPorPagina;

// Monta a consulta SQL com base nos filtros
$sqlMatriculas = "
    SELECT matriculas.id AS matricula_id, 
           alunos.id AS aluno_id, 
           alunos.nome AS aluno_nome, 
           matriculas.data_matricula, 
           turmas.nome AS turma_nome 
    FROM matriculas
    INNER JOIN alunos ON alunos.id = matriculas.aluno_id
    INNER JOIN turmas ON matriculas.turma_id = turmas.id";

// Adiciona os filtros à consulta, se existirem
if (count($whereClauses) > 0) {
    $sqlMatriculas .= " WHERE " . implode(" AND ", $whereClauses);
}

// Ordena pelo nome do aluno e depois pela turma
$sqlMatriculas .= " ORDER BY alunos.nome ASC, turmas.nome ASC";

// Conta o total de registros para calcular o número de páginas
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM matriculas");
$rowTotal = $totalResult->fetch_assoc();
$totalMatriculas = $rowTotal['total'];

// Calcula o número total de páginas
$totalPaginas = ceil($totalMatriculas / $matriculasPorPagina);

// Adiciona LIMIT à consulta para exibir a página atual
$sqlMatriculas .= " LIMIT $inicio, $matriculasPorPagina";

// Executa a consulta
$stmt = $conn->prepare($sqlMatriculas);
if (count($params) > 0) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}

$stmt->execute();
$matriculasResult = $stmt->get_result();
$matriculas = $matriculasResult->fetch_all(MYSQLI_ASSOC);

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
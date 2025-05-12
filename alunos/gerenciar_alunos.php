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

// Verifica se há mensagem de sucesso
if(isset($_SESSION['mensagem_sucesso'])){
    $sucesso = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']);
}

// Verifica se há busca
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

// Verifica se existe um ID de aluno para exclusão
if (isset($_GET['excluir'])) {
    $aluno_id = $_GET['excluir'];

    // Exclui o aluno do banco de dados
    $conn->query("DELETE FROM alunos WHERE id = $aluno_id");
    $sucesso = "Aluno excluído com sucesso!";
}

// Verifica se existe um ID de aluno para editar
if (isset($_GET['editar'])) {
    $aluno_id = $_GET['editar'];

    // Busca os dados do aluno
    $result = $conn->query("SELECT * FROM alunos WHERE id = $aluno_id");
    $aluno = $result->fetch_assoc();

    // Atualiza os dados do aluno
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'];
        $data_nascimento = $_POST['data_nascimento'];

        // Atualiza o aluno no banco de dados
        $updateSql = "UPDATE alunos SET nome = ?, data_nascimento = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param('ssi', $nome, $data_nascimento, $aluno_id);
        if ($stmt->execute()) {
            header("Location: gerenciar_alunos.php");
            exit();
        } else {
            $erro = "Erro ao atualizar aluno!";
        }
    }
}

// Define a quantidade de alunos por página
$alunosPorPagina = 5;

// Determinar a página atual
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaAtual = ($paginaAtual > 0) ? $paginaAtual : 1;  // Se a página for menor que 1, definir como 1

// Calcular o limite de alunos a serem exibidos na consulta
$inicio = ($paginaAtual - 1) * $alunosPorPagina;

// Contar o total de alunos (com filtro de busca)
if ($busca != '') {
    $resultadoTotal = $conn->query("SELECT COUNT(*) as total FROM alunos WHERE nome LIKE '%$busca%'");
} else {
    $resultadoTotal = $conn->query("SELECT COUNT(*) as total FROM alunos");
}

$rowTotal = $resultadoTotal->fetch_assoc();
$totalAlunos = $rowTotal['total'];

// Calcular o número total de páginas
$totalPaginas = ceil($totalAlunos / $alunosPorPagina);

// Verificar o limite da consulta (com filtro de busca)
if ($busca != '') {
    $result = $conn->query("SELECT * FROM alunos WHERE nome LIKE '%$busca%' ORDER BY nome ASC LIMIT $inicio, $alunosPorPagina");
} else {
    $result = $conn->query("SELECT * FROM alunos ORDER BY nome ASC LIMIT $inicio, $alunosPorPagina");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Alunos</title>
    <link rel="stylesheet" href="../assets/css/styles_alunos.css">
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
                <h1>Gerenciar Alunos</h1>
            </header>

            <a href="cadastro_aluno.php">Cadastrar Novo Aluno</a><br><br>

            <form method="GET">
                <input type="text" name="busca" placeholder="Buscar por nome" value="<?= htmlspecialchars($busca) ?>">
                <button type="submit">Buscar</button>
                <button type="button" id="limparBusca" onclick="window.location.href = 'gerenciar_alunos.php';">Limpar Busca</button>
            </form>

            <br>

            <?php if (isset($erro)): ?>
                <p style="color: red;"><?= $erro ?></p>
            <?php endif; ?>
            <?php if (isset($sucesso)): ?>
                <p style="color: green;"><?= $sucesso ?></p>
            <?php endif; ?>
            
            <?php if ($result->num_rows > 0) { ?>
                <table border="1">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Usuário</th>
                        <th>Data de Nascimento</th>
                        <th>Ações</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['nome']) ?></td>
                        <td><?= htmlspecialchars($row['usuario'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($row['data_nascimento']))) ?></td>
                        <td>
                            <a href="editar_aluno.php?id=<?= $row['id'] ?>">Editar</a> | 
                            <a href="?excluir=<?= $row['id'] ?>" onclick="return confirm('Tem certeza de que deseja excluir este aluno?')">Excluir</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            <?php } else { ?>
                <p>Nenhum aluno encontrado.</p>
            <?php } ?>

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

        </div>
    </div>
</body>
</html>

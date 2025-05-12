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

if(isset($_SESSION['mensagem_sucesso'])){
    $sucesso = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']);
}

// Verifica se há busca
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

// Verifica se existe um ID de turma para exclusão
if (isset($_GET['excluir'])) {
    $turma_id = $_GET['excluir'];

    // Verifica se a turma tem alunos matriculados
    $resultado = $conn->query("SELECT COUNT(*) AS total FROM matriculas WHERE turma_id = $turma_id");
    $row = $resultado->fetch_assoc();
    if ($row['total'] > 0) {
        $erro = "Não é possível excluir a turma, pois há alunos matriculados nela.";
    } else {
        // Exclui a turma
        $conn->query("DELETE FROM turmas WHERE id = $turma_id");
        $sucesso = "Turma excluída com sucesso!";
    }
}

// Define a quantidade de turmas por página
$turmasPorPagina = 5;

// Determina a página atual
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaAtual = ($paginaAtual > 0) ? $paginaAtual : 1;  // Se a página for menor que 1, definir como 1

// Calcular o limite de turmas a serem exibidas na consulta
$inicio = ($paginaAtual - 1) * $turmasPorPagina;

// Contar o total de turmas (com filtro de busca)
if ($busca != '') {
    $resultadoTotal = $conn->query("SELECT COUNT(*) as total FROM turmas WHERE nome LIKE '%$busca%'");
} else {
    $resultadoTotal = $conn->query("SELECT COUNT(*) as total FROM turmas");
}

$rowTotal = $resultadoTotal->fetch_assoc();
$totalTurmas = $rowTotal['total'];

// Calcular o número total de páginas
$totalPaginas = ceil($totalTurmas / $turmasPorPagina);

// Verificar o limite da consulta (com filtro de busca)
if ($busca != '') {
    $result = $conn->query("SELECT * FROM turmas WHERE nome LIKE '%$busca%' ORDER BY nome ASC LIMIT $inicio, $turmasPorPagina");
} else {
    $result = $conn->query("SELECT * FROM turmas ORDER BY nome ASC LIMIT $inicio, $turmasPorPagina");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Turmas</title>
    <link rel="stylesheet" href="../assets/css/styles_turmas.css">
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
                <h1>Gerenciar Turmas</h1>
            </header>

            <a href="cadastro_turma.php">Cadastrar Nova Turma</a><br><br>

            <form method="GET">
                <input type="text" name="busca" placeholder="Buscar por nome" value="<?= htmlspecialchars($busca) ?>">
                <button type="submit">Buscar</button>
                <button type="button" id="limparBusca" onclick="window.location.href = 'gerenciar_turmas.php';">Limpar Busca</button>
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
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                    <?php while ($turma = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($turma['nome']) ?></td>
                        <td><?= htmlspecialchars($turma['descricao']) ?></td>
                        <td><?= htmlspecialchars($turma['tipo']) ?></td>
                        <td>
                            <a href="editar_turma.php?id=<?= $turma['id'] ?>">Editar</a> | 
                            <a href="?excluir=<?= $turma['id'] ?>" onclick="return confirm('Tem certeza de que deseja excluir esta turma?')">Excluir</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            <?php } else { ?>
                <p>Nenhuma turma encontrada.</p>
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

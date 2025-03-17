<?php
// Incluindo a conexão com o banco de dados
include 'includes/db.php';

// Consultas SQL para pegar os totais
$sqlAlunos = "SELECT COUNT(*) as total_alunos FROM alunos";
$sqlTurmas = "SELECT COUNT(*) as total_turmas FROM turmas";
$sqlMatriculas = "SELECT COUNT(*) as total_matriculas FROM matriculas";

// Executando as consultas
$resultAlunos = $conn->query($sqlAlunos);
$resultTurmas = $conn->query($sqlTurmas);
$resultMatriculas = $conn->query($sqlMatriculas);

// Pegando os resultados
$totalAlunos = $resultAlunos->fetch_assoc()['total_alunos'];
$totalTurmas = $resultTurmas->fetch_assoc()['total_turmas'];
$totalMatriculas = $resultMatriculas->fetch_assoc()['total_matriculas'];

// Verifica se o usuário está logado e se há permissão de admin
include 'includes/session.php';  // Incluindo o arquivo de sessão
if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Ação de logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle - Dashboard</title>
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/styles_dashboard.css">
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
                <li><a href="alunos/gerenciar_alunos.php">Alunos</a></li>
                <li><a href="turmas/gerenciar_turmas.php">Turmas</a></li>
                <li><a href="matriculas/gerenciar_matriculas.php">Matrículas</a></li>
                <li><a href="?logout=true">Sair</a></li>
            </ul>
        </div>

        <div class="main-content">
            <header>
                <h1>Visão Geral</h1>
            </header>
            <div class="dashboard-overview">
                <div class="box">
                    <h3>Total de Alunos</h3>
                    <p><?= $totalAlunos ?></p>
                </div>
                <div class="box">
                    <h3>Total de Turmas</h3>
                    <p><?= $totalTurmas ?></p>
                </div>
                <div class="box">
                    <h3>Total de Matrículas</h3>
                    <p><?= $totalMatriculas ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

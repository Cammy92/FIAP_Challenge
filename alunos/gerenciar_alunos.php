<?php
include '../includes/auth.php';
include '../header.php';
include '../includes/session.php';
include '../includes/db.php';

if (!isUserLoggedIn() || $_SESSION['permissao'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$sucesso = '';
$erro = '';
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// Excluir aluno (somente via POST por segurança)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $aluno_id = intval($_POST['excluir_id']);

    $stmt = $conn->prepare("DELETE FROM alunos WHERE id = ?");
    $stmt->bind_param('i', $aluno_id);
    if ($stmt->execute()) {
        $sucesso = "Aluno excluído com sucesso!";
    } else {
        $erro = "Erro ao excluir aluno!";
    }
}

// Paginação
$alunosPorPagina = 5;
$paginaAtual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$inicio = ($paginaAtual - 1) * $alunosPorPagina;

if ($busca != '') {
    $busca_sql = "%{$busca}%";
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM alunos WHERE nome LIKE ?");
    $stmtTotal->bind_param("s", $busca_sql);
    $stmtTotal->execute();
    $resultTotal = $stmtTotal->get_result();
    $rowTotal = $resultTotal->fetch_assoc();
    $totalAlunos = $rowTotal['total'];

    $stmt = $conn->prepare("SELECT * FROM alunos WHERE nome LIKE ? ORDER BY nome ASC LIMIT ?, ?");
    $stmt->bind_param("sii", $busca_sql, $inicio, $alunosPorPagina);
} else {
    $resultTotal = $conn->query("SELECT COUNT(*) as total FROM alunos");
    $rowTotal = $resultTotal->fetch_assoc();
    $totalAlunos = $rowTotal['total'];

    $stmt = $conn->prepare("SELECT * FROM alunos ORDER BY nome ASC LIMIT ?, ?");
    $stmt->bind_param("ii", $inicio, $alunosPorPagina);
}
$stmt->execute();
$result = $stmt->get_result();
$totalPaginas = ceil($totalAlunos / $alunosPorPagina);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
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
        <header><h1>Gerenciar Alunos</h1></header>

        <a href="cadastro_aluno.php">Cadastrar Novo Aluno</a><br><br>

        <form method="GET">
            <input type="text" name="busca" placeholder="Buscar por nome" value="<?= htmlspecialchars($busca) ?>">
            <button type="submit">Buscar</button>
            <button type="button" onclick="window.location.href='gerenciar_alunos.php';">Limpar Busca</button>
        </form>

        <?php if ($erro): ?>
            <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
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
                            <a href="editar_aluno.php?id=<?= urlencode($row['id']) ?>">Editar</a> | 
                            <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este aluno?');">
                                <input type="hidden" name="excluir_id" value="<?= htmlspecialchars($row['id']) ?>">
                                <button type="submit" style="background:none;border:none;color:red;cursor:pointer;">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <!-- Paginação -->
            <div class="paginacao">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?pagina=<?= $i ?>&busca=<?= urlencode($busca) ?>" <?= $i === $paginaAtual ? 'style="font-weight:bold;"' : '' ?>>
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <p>Nenhum aluno encontrado.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

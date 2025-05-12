<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Gerar o token CSRF se não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Gera um token CSRF seguro
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIAP - Portal Secretaria</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <svg class="components-menu-svg" viewBox="0 0 576.206 157.976" fill="#ed145b">
                <title>Logotipo FIAP</title>
                <path d="M164.733 3.17h11.35v152.805h-11.35z"></path>
                <path d="M44.387 76.298h55.408v10.478H44.387z"></path>
                <path d="M2 3.17v152.806h11.35v-69.2h.015V76.297h-.014v-62.65h117.696V3.17"></path>
                <path d="M517.45 2.58h-90.446v152.806h11.35V98.41h.083V87.935h-.082V13.06h78.223c27.505 0 46.277 12.66 46.277 36.89v.438c0 22.7-19.21 37.546-47.588 37.546H479.47V98.41h35.142c31.87 0 59.595-16.59 59.595-48.678v-.436c0-29.47-23.14-46.715-56.758-46.715"></path>
                <path d="M360.968 87.935L307.978 2h-10.915l-94.387 153.897h11.788l87.84-141.453 45.786 73.49"></path>
                <path d="M378.403 116.21h-12.697l24.727 39.686h12.44"></path>
            </svg>
            <form action="login.php" method="POST">
                <div class="textbox">
                    <input type="text" placeholder="Usuário" name="usuario" required autocomplete="off">
                </div>
                <div class="textbox">
                    <input type="password" placeholder="Senha" name="senha" required autocomplete="off">
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="submit" value="Conectar" class="btn">
            </form>

            <?php
            if (isset($_SESSION['erro_login'])) {
                echo "<p class='erro'>" . htmlspecialchars($_SESSION['erro_login'], ENT_QUOTES, 'UTF-8') . "</p>";
                unset($_SESSION['erro_login']);
            }
            ?>
        </div>
    </div>
    <canvas id="canvas"></canvas>
    <script src="assets/js/script.js"></script>
</body>
</html>

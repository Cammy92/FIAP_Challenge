<?php
// Conexão segura com o banco de dados
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'fiap';

$conn = new mysqli($host, $user, $pass, $db);

// Verifica de erro na conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Configuração de charset para evitar problemas de codificação
$conn->set_charset('utf8');
?>

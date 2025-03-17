<?php
// Função para limpar dados de entrada (evitar XSS)
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

?>

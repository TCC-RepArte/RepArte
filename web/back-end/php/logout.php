<?php
session_start();

// Limpar todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Limpar cookies de sessão se existirem
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Redirecionar para a página de login
header("Location: ../../html/login1.php");
exit();
?> 
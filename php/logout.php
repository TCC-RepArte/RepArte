<?php
session_start();

// Remove token de "lembre de mim" se existir
if (isset($_SESSION['id'])) {
    require_once 'config.php';
    require_once 'remember_me.php';

    global $con;
    removerTokenDoBanco($con, $_SESSION['id']);
    limparTokenLembreMim();
}

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
header("Location: ../login1.php");
exit();
?>
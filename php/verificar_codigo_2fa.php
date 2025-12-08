<?php
session_start();
require 'config.php';
require '2fa.php';
require 'remember_me.php';

// Verificar se tem dados pendentes
if (!isset($_SESSION['2fa_pending_user'])) {
    header("Location: ../login1.php");
    exit();
}

if (!isset($_POST['codigo'])) {
    $_SESSION['erros'] = ['Código não fornecido!'];
    header("Location: ../verificar_2fa.php");
    exit();
}

$codigo = $_POST['codigo'];
$id_usuario = $_SESSION['2fa_pending_user'];
$email = $_SESSION['2fa_pending_email'];
$usuario = $_SESSION['2fa_pending_usuario'];
$lembrar = $_SESSION['2fa_lembrar'] ?? false;

global $con;

// Verifica o código
if (verificarCodigo2FA($con, $id_usuario, $codigo)) {
    // Código correto, cria a sessão
    $_SESSION['usuario'] = $usuario;
    $_SESSION['email'] = $email;
    $_SESSION['id'] = $id_usuario;

    // Se marcou "lembre de mim", salva o token
    if ($lembrar) {
        salvarTokenLembreMim($con, $id_usuario);
    }

    // Limpa dados temporários
    unset($_SESSION['2fa_pending_user']);
    unset($_SESSION['2fa_pending_email']);
    unset($_SESSION['2fa_pending_usuario']);
    unset($_SESSION['2fa_lembrar']);

    $_SESSION['val'] = ['Login realizado com sucesso!'];
    header('Location: ../telainicial.php');
} else {
    $_SESSION['erros'] = ['Código inválido ou expirado!'];
    header("Location: ../verificar_2fa.php");
}

$con->close();
?>
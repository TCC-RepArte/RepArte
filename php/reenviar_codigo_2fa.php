<?php
session_start();
require 'config.php';
require '2fa.php';

// Verificar se tem dados pendentes
if (!isset($_SESSION['2fa_pending_user'])) {
    header("Location: ../login1.php");
    exit();
}

$id_usuario = $_SESSION['2fa_pending_user'];
$email = $_SESSION['2fa_pending_email'];
$usuario = $_SESSION['2fa_pending_usuario'];

global $con;

// Gera novo código
$codigo = salvarCodigo2FA($con, $id_usuario);

if ($codigo && enviarCodigo2FAEmail($email, $codigo, $usuario)) {
    $_SESSION['val'] = ['Novo código enviado para seu email!'];
} else {
    $_SESSION['erros'] = ['Erro ao reenviar código. Tente novamente.'];
}

header("Location: ../verificar_2fa.php");
exit();
?>
<?php
session_start();

// Limpa dados temporários do 2FA
unset($_SESSION['2fa_pending_user']);
unset($_SESSION['2fa_pending_email']);
unset($_SESSION['2fa_pending_usuario']);
unset($_SESSION['2fa_lembrar']);

$_SESSION['val'] = ['Verificação 2FA cancelada.'];
header("Location: ../login1.php");
exit();
?>
<?php
// Arquivo para iniciar login com Google
session_start();
require 'google_config.php';

// Redirecionar para página de autenticação do Google
$authUrl = getGoogleAuthUrl();
header('Location: ' . $authUrl);
exit();
?>


<?php
// Arquivo para enviar código de verificação por SMS (simulado)
session_start();
require 'config.php';

unset($_SESSION['erros_sms']);
unset($_SESSION['val_sms']);

global $con;

if ($con->connect_error) {
    $_SESSION['erros_sms'] = ['Não foi possível conectar ao banco'];
    header('Location: ../login_sms.php');
    exit();
}

if(isset($_POST['telefone'])){
    $telefone = $con->real_escape_string($_POST['telefone']);
    
    // Validar formato do telefone (apenas números)
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    
    if(strlen($telefone) < 10 || strlen($telefone) > 11) {
        $_SESSION['erros_sms'] = ['Telefone inválido. Use formato: (00) 00000-0000'];
        header('Location: ../login_sms.php');
        exit();
    }
    
    // Gerar código de 6 dígitos
    $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Definir expiração (5 minutos)
    $data_criacao = date('Y-m-d H:i:s');
    $data_expiracao = date('Y-m-d H:i:s', strtotime('+5 minutes'));
    
    // Invalidar códigos antigos do mesmo telefone
    $stmt = $con->prepare("UPDATE codigos_sms SET usado = 1 WHERE telefone = ? AND usado = 0");
    $stmt->bind_param("s", $telefone);
    $stmt->execute();
    
    // Inserir novo código
    $stmt = $con->prepare("INSERT INTO codigos_sms (telefone, codigo, data_criacao, data_expiracao) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $telefone, $codigo, $data_criacao, $data_expiracao);
    
    if($stmt->execute()) {
        // Armazenar telefone na sessão
        $_SESSION['telefone_sms'] = $telefone;
        $_SESSION['codigo_enviado'] = $codigo; // Para mostrar na tela (ambiente local)
        $_SESSION['val_sms'] = ['Código enviado com sucesso!'];
        
        header('Location: ../verificar_sms.php');
        exit();
    } else {
        $_SESSION['erros_sms'] = ['Erro ao enviar código'];
        header('Location: ../login_sms.php');
        exit();
    }
    
    $stmt->close();
} else {
    $_SESSION['erros_sms'] = ['Telefone não informado'];
    header('Location: ../login_sms.php');
    exit();
}

$con->close();
?>


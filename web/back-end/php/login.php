<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
require 'config.php';

// Verificar se já está logado e foi uma requisição AJAX
if(isset($_SESSION['id'])){
    $_SESSION['val'] = ['Usuario já está logado!'];
    header('Location: ../../html/telainicial.php');
    exit();
}

// Conexão com o banco
global $con;

// Verificar conexão
if ($con->connect_error) {
    $_SESSION['erros'] = ['Não foi possível realizar conexão com o banco'];
    exit();
}

if(isset($_POST['usuario']) && isset($_POST['senha'])){
    // Limpar os dados recebidos
    $usuario = $con->real_escape_string($_POST['usuario']);
    $senha = $_POST['senha']; 

    // Verificar se é email ou nome de usuário
    if(filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
        $stmt = $con->prepare("SELECT id, usuario, email, senha FROM login WHERE email = ?");
    } else {
        $usuario = substr_replace($usuario, '@', 0, 0);
        $stmt = $con->prepare("SELECT id, usuario, email, senha FROM login WHERE usuario = ?");
    }

    if (!$stmt) {
        $_SESSION['erros'] = ['Não foi possível verificar os dados do login!'];
        header('Location: ../../html/login1.php');
        exit();
    }

    $stmt->bind_param("s", $usuario);
    
    if (!$stmt->execute()) {
        $_SESSION['erros'] = ['Houve um erro no processo de verificação do e-mail/senha'];
        header('Location: ../../html/login1.php');
        exit();
    }

    $resultado = $stmt->get_result();

    if($resultado->num_rows > 0){
        $usuario = $resultado->fetch_assoc();
        
        // Verificar a senha
        if(password_verify($senha, $usuario['senha'])) {
            // Armazenar informações na sessão
            $_SESSION['usuario'] = $usuario['usuario'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['id'] = $usuario['id'];

            $_SESSION['val'] = ['Logado com sucesso!'];
            header('Location: ../../html/telainicial.php');

        } else {
            $_SESSION['erros'] = ['Senha incorreta!'];
            header('Location: ../../html/login1.php');
            exit();
        }
                
    } else {

        $_SESSION['erros'] = ['Usuario não encontrado!'];
        header('Location: ../../html/login1.php');
        exit();
        
        }
    
    $stmt->close();

} else {

    $_SESSION['erros'] = ['Dados estão incompletos!'];
    header('Location: ../../html/login1.php');
    exit();
    
}

$con->close();
?>
<?php
// Arquivo para atualizar senha após validação do token
session_start();
require 'config.php';
require 'validar_token.php';

// Limpar mensagens antigas específicas de atualização de senha
unset($_SESSION['erros_nova_senha']);
unset($_SESSION['val_nova_senha']);

global $con;

// Verificar conexão
if ($con->connect_error) {
    $_SESSION['erros_nova_senha'] = ['Não foi possível realizar conexão com o banco'];
    header('Location: ../novasenha.php');
    exit();
}

// Verificar se todos os dados foram enviados
if(isset($_POST['token']) && isset($_POST['senha']) && isset($_POST['confirmar_senha'])){
    
    $token = $_POST['token'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Validar se as senhas coincidem
    if($senha !== $confirmar_senha) {
        $_SESSION['erros_nova_senha'] = ['As senhas não coincidem'];
        header('Location: ../novasenha.php?token=' . urlencode($token));
        exit();
    }
    
    // Validar tamanho mínimo da senha
    if(strlen($senha) < 6) {
        $_SESSION['erros_nova_senha'] = ['A senha deve ter no mínimo 6 caracteres'];
        header('Location: ../novasenha.php?token=' . urlencode($token));
        exit();
    }
    
    // Validar o token
    $validacao = validarToken($token);
    
    if(!$validacao['valido']) {
        $_SESSION['erros_recuperacao'] = [$validacao['erro']];
        header('Location: ../emailesqueceu.php');
        exit();
    }
    
    // Token válido, atualizar a senha
    $id_usuario = $validacao['id_usuario'];
    $id_token = $validacao['id_token'];
    
    // Criptografar a nova senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Atualizar senha no banco
    $stmt = $con->prepare("UPDATE login SET senha = ? WHERE id = ?");
    $stmt->bind_param("ss", $senha_hash, $id_usuario);
    
    if($stmt->execute()) {
        // Marcar token como usado
        $stmt_token = $con->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE id = ?");
        $stmt_token->bind_param("i", $id_token);
        $stmt_token->execute();
        $stmt_token->close();
        
        $_SESSION['val_atualizar'] = ['Senha atualizada com sucesso!'];
        header('Location: ../atualizarsenha.html');
        exit();
    } else {
        $_SESSION['erros_nova_senha'] = ['Erro ao atualizar senha'];
        header('Location: ../novasenha.php?token=' . urlencode($token));
        exit();
    }
    
    $stmt->close();
    
} else {
    $_SESSION['erros_recuperacao'] = ['Dados incompletos'];
    header('Location: ../emailesqueceu.php');
    exit();
}

$con->close();
?>


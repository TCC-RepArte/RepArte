<?php
// Atualizar senha após verificação de código
session_start();
require_once 'config.php';

global $con;

// Verificar se veio do fluxo correto
if (!isset($_SESSION['codigo_senha_validado']) || !isset($_SESSION['email_nova_senha']) || !isset($_SESSION['id_recuperacao'])) {
    $_SESSION['erros_recuperacao'] = ['Acesso inválido'];
    header('Location: ../emailesqueceu.php');
    exit();
}

// Verificar conexão
if ($con->connect_error) {
    $_SESSION['erros_nova_senha'] = ['Erro de conexão com banco'];
    header('Location: ../nova_senha_codigo.php');
    exit();
}

// Verificar dados do formulário
if (!isset($_POST['senha']) || !isset($_POST['confirmar_senha'])) {
    $_SESSION['erros_nova_senha'] = ['Dados incompletos'];
    header('Location: ../nova_senha_codigo.php');
    exit();
}

$senha = $_POST['senha'];
$confirmar_senha = $_POST['confirmar_senha'];
$email = $_SESSION['email_nova_senha'];
$id_recuperacao = $_SESSION['id_recuperacao'];

// Validar senhas
if ($senha !== $confirmar_senha) {
    $_SESSION['erros_nova_senha'] = ['As senhas não coincidem'];
    header('Location: ../nova_senha_codigo.php');
    exit();
}

if (strlen($senha) < 6) {
    $_SESSION['erros_nova_senha'] = ['A senha deve ter no mínimo 6 caracteres'];
    header('Location: ../nova_senha_codigo.php');
    exit();
}

// Buscar usuário
$stmt = $con->prepare("SELECT id FROM login WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $_SESSION['erros_nova_senha'] = ['Usuário não encontrado'];
    header('Location: ../nova_senha_codigo.php');
    exit();
}

$usuario = $resultado->fetch_assoc();
$stmt->close();

// Criptografar nova senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Atualizar senha no banco
$stmt = $con->prepare("UPDATE login SET senha = ? WHERE id = ?");
$stmt->bind_param("ss", $senha_hash, $usuario['id']);

if ($stmt->execute()) {
    // Marcar código como usado
    $stmt_usado = $con->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE id = ?");
    $stmt_usado->bind_param("i", $id_recuperacao);
    $stmt_usado->execute();
    $stmt_usado->close();

    // Limpar sessão
    unset($_SESSION['codigo_senha_validado']);
    unset($_SESSION['email_nova_senha']);
    unset($_SESSION['id_recuperacao']);
    unset($_SESSION['email_recuperacao']);
    unset($_SESSION['codigo_recuperacao']);

    // Redirecionar para página de sucesso
    $_SESSION['val_atualizar'] = ['Senha atualizada com sucesso!'];
    header('Location: ../atualizarsenha.html');
    exit();
} else {
    $_SESSION['erros_nova_senha'] = ['Erro ao atualizar senha'];
    header('Location: ../nova_senha_codigo.php');
    exit();
}

$stmt->close();
$con->close();
?>
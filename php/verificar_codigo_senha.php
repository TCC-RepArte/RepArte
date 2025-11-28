<?php
// Verificar código de recuperação de senha
session_start();
require_once 'config.php';

global $con;

// Verificar conexão
if ($con->connect_error) {
    $_SESSION['erros_senha'] = ['Não foi possível conectar ao banco de dados'];
    header('Location: ../verificar_codigo_senha.php');
    exit();
}

// Verificar dados
if (!isset($_POST['email']) || !isset($_POST['codigo'])) {
    $_SESSION['erros_senha'] = ['Dados incompletos'];
    header('Location: ../verificar_codigo_senha.php');
    exit();
}

$email = $con->real_escape_string(trim($_POST['email']));
$codigo = $con->real_escape_string(trim($_POST['codigo']));

// Buscar usuário pelo email
$stmt = $con->prepare("SELECT id FROM login WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $_SESSION['erros_senha'] = ['Email não encontrado'];
    header('Location: ../verificar_codigo_senha.php');
    exit();
}

$usuario = $resultado->fetch_assoc();
$stmt->close();

// Buscar código no banco (token armazena o código agora)
$stmt = $con->prepare("SELECT id, token, data_expiracao FROM recuperacao_senha WHERE id_usuario = ? AND usado = 0 ORDER BY data_criacao DESC LIMIT 1");
$stmt->bind_param("s", $usuario['id']);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $_SESSION['erros_senha'] = ['Código não encontrado ou já utilizado'];
    header('Location: ../verificar_codigo_senha.php');
    exit();
}

$registro = $resultado->fetch_assoc();
$stmt->close();

// Verificar expiração
if (strtotime($registro['data_expiracao']) < time()) {
    $_SESSION['erros_senha'] = ['Código expirado. Solicite um novo código.'];
    header('Location: ../emailesqueceu.php');
    exit();
}

// Verificar se código está correto
if ($registro['token'] !== $codigo) {
    $_SESSION['erros_senha'] = ['Código incorreto. Tente novamente.'];
    header('Location: ../verificar_codigo_senha.php');
    exit();
}

// Código correto! Salvar na sessão e redirecionar para nova senha
$_SESSION['codigo_senha_validado'] = true;
$_SESSION['email_nova_senha'] = $email;
$_SESSION['id_recuperacao'] = $registro['id'];

header('Location: ../nova_senha_codigo.php');
exit();

$con->close();
?>
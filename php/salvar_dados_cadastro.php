<?php
// Arquivo para salvar dados do cadastro na sessão
session_start();

header('Content-Type: application/json');

// Verificar se todos os dados foram enviados
if (!isset($_POST['usuario']) || !isset($_POST['email']) || !isset($_POST['senha']) || !isset($_POST['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Dados incompletos'
    ]);
    exit();
}

require_once 'config.php';

global $con;

if ($con->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro de conexão com banco de dados'
    ]);
    exit();
}

$usuario = $con->real_escape_string(trim($_POST['usuario']));
$email = $con->real_escape_string(trim($_POST['email']));

// Verificar se email já existe
$stmt = $con->prepare("SELECT id FROM login WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Este email já está cadastrado!'
    ]);
    $stmt->close();
    exit();
}
$stmt->close();

// Verificar se usuário já existe
$stmt = $con->prepare("SELECT id FROM login WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Este nome de usuário já está em uso!'
    ]);
    $stmt->close();
    exit();
}
$stmt->close();

// Salvar dados na sessão
$_SESSION['dados_cadastro'] = [
    'usuario' => $_POST['usuario'],
    'email' => $_POST['email'],
    'senha' => $_POST['senha'],
    'id' => $_POST['id']
];

echo json_encode([
    'success' => true,
    'message' => 'Dados salvos com sucesso'
]);
?>
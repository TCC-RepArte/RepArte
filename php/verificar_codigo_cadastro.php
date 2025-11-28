<?php
// Arquivo para verificar código de cadastro
session_start();
require_once 'config.php';

header('Content-Type: application/json');

global $con;

// Verificar conexão
if ($con->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Não foi possível conectar ao banco de dados'
    ]);
    exit();
}

// Verificar se os dados foram enviados
if (!isset($_POST['email']) || !isset($_POST['codigo'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Dados incompletos'
    ]);
    exit();
}

$email = $con->real_escape_string(trim($_POST['email']));
$codigo = $con->real_escape_string(trim($_POST['codigo']));

// Buscar código no banco
$stmt = $con->prepare("SELECT id, codigo, data_expiracao, tentativas FROM codigos_verificacao WHERE email = ? AND usado = 0 ORDER BY data_criacao DESC LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Código não encontrado. Solicite um novo código.'
    ]);
    $stmt->close();
    exit();
}

$registro = $resultado->fetch_assoc();
$stmt->close();

// Verificar se código expirou (comparação direta de strings no formato MySQL)
$agora = date('Y-m-d H:i:s');
if ($registro['data_expiracao'] < $agora) {
    echo json_encode([
        'success' => false,
        'message' => 'Código expirado. Solicite um novo código.',
        'expirado' => true
    ]);
    exit();
}

// Verificar número de tentativas (máximo 5)
if ($registro['tentativas'] >= 5) {
    echo json_encode([
        'success' => false,
        'message' => 'Número máximo de tentativas excedido. Solicite um novo código.',
        'bloqueado' => true
    ]);
    exit();
}

// Verificar se código está correto
if ($registro['codigo'] !== $codigo) {
    // Incrementar tentativas
    $novasTentativas = $registro['tentativas'] + 1;
    $stmt = $con->prepare("UPDATE codigos_verificacao SET tentativas = ? WHERE id = ?");
    $stmt->bind_param("ii", $novasTentativas, $registro['id']);
    $stmt->execute();
    $stmt->close();

    $tentativasRestantes = 5 - $novasTentativas;

    echo json_encode([
        'success' => false,
        'message' => "Código incorreto. Você tem {$tentativasRestantes} tentativa(s) restante(s).",
        'tentativas_restantes' => $tentativasRestantes
    ]);
    exit();
}

// Código correto! Marcar como usado
$stmt = $con->prepare("UPDATE codigos_verificacao SET usado = 1 WHERE id = ?");
$stmt->bind_param("i", $registro['id']);
$stmt->execute();
$stmt->close();

// Salvar na sessão que o email foi verificado
$_SESSION['email_verificado'] = $email;
$_SESSION['codigo_verificado'] = true;

echo json_encode([
    'success' => true,
    'message' => 'Código verificado com sucesso!'
]);

$con->close();
?>
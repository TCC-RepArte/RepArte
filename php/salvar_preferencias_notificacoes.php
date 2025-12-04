<?php
// Salvar preferências de notificações
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Verificar se usuário está logado
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado']);
    exit;
}

$id_usuario = $_SESSION['id'];
$input = json_decode(file_get_contents('php://input'), true);

$notif_comentarios = isset($input['notif_comentarios']) ? 1 : 0;
$notif_reacoes = isset($input['notif_reacoes']) ? 1 : 0;
$notif_respostas = isset($input['notif_respostas']) ? 1 : 0;

// Verificar se já existe registro
$sqlCheck = "SELECT id FROM preferencias_notificacoes WHERE id_usuario = ?";
$stmtCheck = $con->prepare($sqlCheck);
$stmtCheck->bind_param("s", $id_usuario);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    // Atualizar
    $sql = "UPDATE preferencias_notificacoes SET notif_comentarios = ?, notif_reacoes = ?, notif_respostas = ? WHERE id_usuario = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iiis", $notif_comentarios, $notif_reacoes, $notif_respostas, $id_usuario);
} else {
    // Inserir
    $sql = "INSERT INTO preferencias_notificacoes (id_usuario, notif_comentarios, notif_reacoes, notif_respostas) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("siii", $id_usuario, $notif_comentarios, $notif_reacoes, $notif_respostas);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Preferências salvas com sucesso']);
} else {
    echo json_encode(['success' => false, 'error' => 'Erro ao salvar preferências']);
}
?>
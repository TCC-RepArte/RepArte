<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

require_once 'config.php';

// Recebe o status do VLibras (ativo ou inativo)
$data = json_decode(file_get_contents('php://input'), true);
$vlibras_ativo = isset($data['vlibras_ativo']) ? (int) $data['vlibras_ativo'] : 1;
$id_usuario = $_SESSION['id'];

// Verifica se já existe configuração para o usuário
$sqlCheck = "SELECT id FROM configuracoes_usuario WHERE id_usuario = ?";
$stmtCheck = $con->prepare($sqlCheck);
$stmtCheck->bind_param("s", $id_usuario);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    // Atualiza a configuração existente
    $sql = "UPDATE configuracoes_usuario SET vlibras_ativo = ? WHERE id_usuario = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("is", $vlibras_ativo, $id_usuario);
} else {
    // Cria nova configuração
    $sql = "INSERT INTO configuracoes_usuario (id_usuario, vlibras_ativo) VALUES (?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $id_usuario, $vlibras_ativo);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Configuração atualizada com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar configuração']);
}

$stmt->close();
$stmtCheck->close();
$con->close();
?>
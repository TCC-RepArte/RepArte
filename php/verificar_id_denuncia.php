<?php
require_once 'config.php';

header('Content-Type: application/json');

// Recebe o JSON enviado pelo criarID.js
$data = json_decode(file_get_contents('php://input'), true);

// O criarID.js envia { idCriado: '...' }
if (!isset($data['idCriado'])) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

$id = $data['idCriado'];

// Verifica se o ID já existe na tabela denuncias
$stmt = $con->prepare("SELECT id FROM denuncias WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // ID já existe (duplicado)
    echo json_encode(['success' => true, 'duplicate' => true]);
} else {
    // ID disponível (não duplicado)
    // Retorna o próprio ID para o JS usar
    echo json_encode(['success' => true, 'duplicate' => false, 'id' => $id]);
}

$stmt->close();
$con->close();
?>
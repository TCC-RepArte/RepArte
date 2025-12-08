<?php
session_start();
require 'config.php';
require '2fa.php';

header('Content-Type: application/json');

// Verificar se usuário está logado
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

$id_usuario = $_SESSION['id'];
$data = json_decode(file_get_contents('php://input'), true);
$ativar = isset($data['ativar']) && $data['ativar'];

global $con;

if ($ativar) {
    // Ativar 2FA
    if (ativar2FA($con, $id_usuario)) {
        echo json_encode(['success' => true, 'message' => '2FA ativado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao ativar 2FA']);
    }
} else {
    // Desativar 2FA
    if (desativar2FA($con, $id_usuario)) {
        echo json_encode(['success' => true, 'message' => '2FA desativado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao desativar 2FA']);
    }
}

$con->close();
?>
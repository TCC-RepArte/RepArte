<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não logado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

$id_denunciante = $_SESSION['id'];
$tipo_denuncia = $data['tipo'];
$id_item_denunciado = $data['id_item']; 
$motivo = $data['motivo'];

// Função simples para gerar ID se não vier do front (fallback)
function gerarIdDenuncia($con)
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $len = 15;
    $unique = false;
    $id = '';

    while (!$unique) {
        $id = '';
        for ($i = 0; $i < $len; $i++) {
            $id .= $chars[rand(0, strlen($chars) - 1)];
        }

        // Verifica se já existe
        $stmt = $con->prepare("SELECT id FROM denuncias WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            $unique = true;
        }
        $stmt->close();
    }
    return $id;
}

// Se o JS não mandou um ID, gera aqui
$id_denuncia = isset($data['id_denuncia']) ? $data['id_denuncia'] : gerarIdDenuncia($con);

if (empty($motivo) || empty($id_item_denunciado) || empty($tipo_denuncia)) {
    echo json_encode(['success' => false, 'error' => 'Todos os campos são obrigatórios']);
    exit;
}

// Query atualizada para incluir o ID e tratar tudo como string
$sql = "INSERT INTO denuncias (id, id_denunciante, tipo_denuncia, id_item_denunciado, motivo, data_denuncia) VALUES (?, ?, ?, ?, ?, NOW())";

if ($stmt = $con->prepare($sql)) {

    $stmt->bind_param("sssss", $id_denuncia, $id_denunciante, $tipo_denuncia, $id_item_denunciado, $motivo);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Denúncia enviada com sucesso', 'id' => $id_denuncia]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao salvar: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Erro SQL: ' . $con->error]);
}

$con->close();
?>
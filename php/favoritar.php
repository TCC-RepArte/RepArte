<?php
// Inicia a sessão para pegar o ID do usuário logado
session_start();
require_once 'config.php';

// Define que a resposta será em formato JSON (para o JavaScript ler)
header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

// Pega os dados enviados pelo JavaScript (ID do post)
$data = json_decode(file_get_contents('php://input'), true);
$id_post = $data['id_post'] ?? null;
$id_usuario = $_SESSION['id'];

if (!$id_post) {
    echo json_encode(['success' => false, 'message' => 'ID do post inválido']);
    exit;
}

// Verifica se já existe esse favorito no banco
$sql_check = "SELECT id FROM favoritos WHERE id_usuario = ? AND id_post = ?";
$stmt = $con->prepare($sql_check);
$stmt->bind_param("ss", $id_usuario, $id_post);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Se já existe, REMOVE (desfavoritar)
    $sql_del = "DELETE FROM favoritos WHERE id_usuario = ? AND id_post = ?";
    $stmt_del = $con->prepare($sql_del);
    $stmt_del->bind_param("ss", $id_usuario, $id_post);
    $stmt_del->execute();
    echo json_encode(['success' => true, 'status' => 'removido']);
} else {
    // Se não existe, ADICIONA (favoritar)
    $sql_add = "INSERT INTO favoritos (id_usuario, id_post) VALUES (?, ?)";
    $stmt_add = $con->prepare($sql_add);
    $stmt_add->bind_param("ss", $id_usuario, $id_post);
    $stmt_add->execute();
    echo json_encode(['success' => true, 'status' => 'favoritado']);
}
?>
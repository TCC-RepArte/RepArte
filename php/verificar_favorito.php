<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

// Se não tiver logado, não pode ser favorito
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => true, 'favorito' => false]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id_post = $data['id_post'] ?? null;
$id_usuario = $_SESSION['id'];

if (!$id_post) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}

// Verifica no banco
$sql = "SELECT id FROM favoritos WHERE id_usuario = ? AND id_post = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ss", $id_usuario, $id_post);
$stmt->execute();
$result = $stmt->get_result();

// Se achou linha, é verdadeiro. Se não, falso.
$favorito = ($result->num_rows > 0);

echo json_encode(['success' => true, 'favorito' => $favorito]);
?>
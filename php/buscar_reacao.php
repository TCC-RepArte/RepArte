<?php
// Arquivo para buscar reação existente do usuário para uma postagem
error_reporting(0);
ini_set('display_errors', 0);

require 'config.php';
header('Content-Type: application/json; charset=utf-8');
session_start();

// Recebe os dados enviados via POST em formato JSON
$input = json_decode(file_get_contents("php://input"), true);

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado']);
    exit;
}

$id_usuario = $_SESSION['id'];

// Verifica se o ID da postagem foi enviado
if (isset($input['id'])) {
    $id_conteudo = $input['id'];
    
    // Verificar se a conexão existe
    if (!$con) {
        echo json_encode(['success' => false, 'error' => 'Erro de conexão com banco de dados']);
        exit;
    }
    
    // Busca reação existente do usuário para esta postagem
    $stmt = $con->prepare('SELECT tipo FROM reacoes WHERE id_usuario = ? AND id_conteudo = ? AND tipo_conteudo = "postagem"');
    $stmt->bind_param('ss', $id_usuario, $id_conteudo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $reacao = $result->fetch_assoc();
        echo json_encode(['success' => true, 'reacao' => $reacao]);
    } else {
        echo json_encode(['success' => true, 'reacao' => null]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID da postagem não fornecido']);
}
?>

<?php
// Arquivo para contar reações de uma postagem
error_reporting(0);
ini_set('display_errors', 0);

require 'config.php';
header('Content-Type: application/json; charset=utf-8');

// Recebe os dados enviados via POST em formato JSON
$input = json_decode(file_get_contents("php://input"), true);

// Verifica se o ID da postagem foi enviado
if (isset($input['id'])) {
    $id_conteudo = $input['id'];
    
    // Verificar se a conexão existe
    if (!$con) {
        echo json_encode(['success' => false, 'error' => 'Erro de conexão com banco de dados']);
        exit;
    }
    
    // Conta curtidas
    $stmt = $con->prepare('SELECT COUNT(*) as total FROM reacoes WHERE id_conteudo = ? AND tipo_conteudo = "postagem" AND tipo = "like"');
    $stmt->bind_param('s', $id_conteudo);
    $stmt->execute();
    $result = $stmt->get_result();
    $likes = $result->fetch_assoc()['total'];
    
    // Conta descurtidas
    $stmt = $con->prepare('SELECT COUNT(*) as total FROM reacoes WHERE id_conteudo = ? AND tipo_conteudo = "postagem" AND tipo = "dislike"');
    $stmt->bind_param('s', $id_conteudo);
    $stmt->execute();
    $result = $stmt->get_result();
    $dislikes = $result->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'likes' => $likes,
        'dislikes' => $dislikes
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'ID da postagem não fornecido']);
}
?>

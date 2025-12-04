<?php
// Busca as 3 hashtags mais comentadas em uma obra específica
header('Content-Type: application/json');
require_once 'config.php';

// Recebe o ID da obra
$id_obra = $_GET['id_obra'] ?? '';

if (empty($id_obra)) {
    echo json_encode(['erro' => 'ID da obra não fornecido']);
    exit;
}

try {
    // Busca as 3 hashtags mais usadas nas postagens desta obra
    $sql = "SELECT h.nome, COUNT(*) as total
            FROM hashtags h
            INNER JOIN post_hashtags ph ON h.id = ph.hashtag_id
            INNER JOIN postagens p ON ph.post_id = p.id
            WHERE p.id_obra = ?
            GROUP BY h.id, h.nome
            ORDER BY total DESC
            LIMIT 3";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $id_obra);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $hashtags = [];
    while ($row = $result->fetch_assoc()) {
        $hashtags[] = $row;
    }
    
    echo json_encode([
        'sucesso' => true,
        'hashtags' => $hashtags,
        'total' => count($hashtags)
    ]);
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode([
        'erro' => 'Erro ao buscar hashtags: ' . $e->getMessage()
    ]);
}
?>
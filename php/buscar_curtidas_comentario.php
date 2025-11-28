<?php
// Arquivo para buscar curtidas dos comentários
require_once 'config.php';

// Configuração de cabeçalhos CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

// Responder imediatamente para requisições OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Verifica se é uma requisição GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode([
            'success' => false,
            'message' => 'Método não permitido'
        ]);
        exit;
    }
    
    $comentarioId = $_GET['comentario_id'] ?? '';
    
    if (empty($comentarioId)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID do comentário não fornecido'
        ]);
        exit;
    }
    
    // Busca o número de curtidas
    $sql = "SELECT COUNT(*) as total_likes FROM reacoes WHERE id_conteudo = ? AND tipo_conteudo = 'comentario' AND tipo = 'like'";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $comentarioId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $totalLikes = $row['total_likes'];
    
    // Verifica se o usuário atual curtiu (se estiver logado)
    $userLiked = false;
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['id'])) {
        $userId = $_SESSION['id'];
        $sqlUser = "SELECT id FROM reacoes WHERE id_usuario = ? AND id_conteudo = ? AND tipo_conteudo = 'comentario' AND tipo = 'like'";
        $stmtUser = $con->prepare($sqlUser);
        $stmtUser->bind_param("ss", $userId, $comentarioId);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();
        
        if ($resultUser->fetch_assoc()) {
            $userLiked = true;
        }
    }
    
    echo json_encode([
        'success' => true,
        'total_likes' => $totalLikes,
        'user_liked' => $userLiked
    ]);
    
} catch (Exception $e) {
    error_log("Erro ao buscar curtidas: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>

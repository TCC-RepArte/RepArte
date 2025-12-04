<?php
// Arquivo para curtir/descurtir comentários
require_once 'config.php';

// Configuração de cabeçalhos CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

// Responder imediatamente para requisições OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Verifica se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Método não permitido'
        ]);
        exit;
    }
    
    // Lê os dados JSON da requisição
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Valida os dados recebidos
    if (!isset($input['comentario_id']) || empty($input['comentario_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'ID do comentário não fornecido'
        ]);
        exit;
    }
    
    // Verificar se o usuário está logado
    session_start();
    if (!isset($_SESSION['id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Usuário não está logado'
        ]);
        exit;
    }
    
    $userId = $_SESSION['id'];
    $comentarioId = $input['comentario_id'];
    
    // Verifica se o usuário já curtiu este comentário
    $sqlCheck = "SELECT id, tipo FROM reacoes WHERE id_usuario = ? AND id_conteudo = ? AND tipo_conteudo = 'comentario'";
    $stmtCheck = $con->prepare($sqlCheck);
    $stmtCheck->bind_param("ss", $userId, $comentarioId);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    
    if ($row = $resultCheck->fetch_assoc()) {
        // Usuário já reagiu - remove a reação
        $sqlDelete = "DELETE FROM reacoes WHERE id = ?";
        $stmtDelete = $con->prepare($sqlDelete);
        $stmtDelete->bind_param("s", $row['id']);
        $stmtDelete->execute();
        
        echo json_encode([
            'success' => true,
            'action' => 'removed',
            'message' => 'Curtida removida'
        ]);
    } else {
        // Usuário não reagiu - adiciona curtida
        $reacaoId = uniqid('react_', true);
        
        $sqlInsert = "INSERT INTO reacoes (id, id_usuario, tipo_conteudo, id_conteudo, tipo, data) VALUES (?, ?, 'comentario', ?, 'like', NOW())";
        $stmtInsert = $con->prepare($sqlInsert);
        $stmtInsert->bind_param("sss", $reacaoId, $userId, $comentarioId);
        $stmtInsert->execute();

                
        // Criar notificação para o dono do comentário
        require_once 'criar_notificacao.php';
        
        $sqlDono = "SELECT id_usuario FROM comentarios WHERE id = ?";
        $stmtDono = $con->prepare($sqlDono);
        $stmtDono->bind_param("s", $comentarioId);
        $stmtDono->execute();
        $resultDono = $stmtDono->get_result();
        
        if ($rowDono = $resultDono->fetch_assoc()) {
            $idDono = $rowDono['id_usuario'];
            if ($idDono != $userId) {
                criarNotificacao($idDono, $userId, 'reacao', $comentarioId, 'curtiu seu comentário');
            }
        }
        
        echo json_encode([
            'success' => true,
            'action' => 'added',
            'message' => 'Comentário curtido'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Erro ao curtir comentário: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>

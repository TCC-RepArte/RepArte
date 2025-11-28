<?php
// Arquivo para criar um novo comentário
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
    if (!isset($input['id_postagem']) || empty($input['id_postagem'])) {
        echo json_encode([
            'success' => false,
            'message' => 'ID da postagem não fornecido'
        ]);
        exit;
    }
    
    if (!isset($input['texto']) || empty(trim($input['texto']))) {
        echo json_encode([
            'success' => false,
            'message' => 'Texto do comentário não fornecido'
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
    
    $postId = $input['id_postagem'];
    $texto = trim($input['texto']);
    $comentarioPaiId = isset($input['comentario_pai_id']) ? $input['comentario_pai_id'] : null;
    
    // Log para debug
    error_log("Tentando criar comentário - PostID: $postId, Texto: $texto, UserID: $userId, PaiID: $comentarioPaiId");
    
    // Gera um ID único para o comentário
    $commentId = uniqid('comment_', true);
    
    // Log do ID gerado
    error_log("ID do comentário gerado: $commentId");
    
    // Determina o nível do comentário
    $nivel = 0;
    if ($comentarioPaiId) {
        // Busca o nível do comentário pai
        $sqlNivel = "SELECT nivel FROM comentarios WHERE id = ?";
        $stmtNivel = $con->prepare($sqlNivel);
        $stmtNivel->bind_param("s", $comentarioPaiId);
        $stmtNivel->execute();
        $resultNivel = $stmtNivel->get_result();
        if ($rowNivel = $resultNivel->fetch_assoc()) {
            $nivel = $rowNivel['nivel'] + 1;
        }
    }
    
    // Insere o comentário no banco de dados
    $sql = "INSERT INTO comentarios (id, id_conteudo, id_usuario, tipo_conteudo, texto, data, comentario_pai_id, nivel) 
            VALUES (?, ?, ?, 'postagem', ?, NOW(), ?, ?)";
    
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro ao preparar statement: " . $con->error);
    }
    
    // Bind parameters - 6 parâmetros: id, id_conteudo, id_usuario, texto, comentario_pai_id, nivel
    $stmt->bind_param("sssssi", $commentId, $postId, $userId, $texto, $comentarioPaiId, $nivel);
    $result = $stmt->execute();
    
    if ($result) {
        error_log("Comentário criado com sucesso: $commentId");
        echo json_encode([
            'success' => true,
            'message' => 'Comentário criado com sucesso',
            'comment_id' => $commentId
        ]);
    } else {
        error_log("Erro ao executar statement: " . $stmt->error);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao criar comentário: ' . $stmt->error
        ]);
    }
    
} catch (Exception $e) {
    // Log do erro para depuração
    error_log("Erro ao criar comentário: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>

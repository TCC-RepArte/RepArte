<?php
// Arquivo para buscar comentários de uma postagem específica
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

// Função para retornar erro
function returnError($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Função para retornar sucesso
function returnSuccess($data) {
    echo json_encode(['success' => true, 'comentarios' => $data]);
    exit;
}

// Verifica se o ID da postagem foi fornecido
if (!isset($_GET['id_postagem']) || empty($_GET['id_postagem'])) {
    returnError('ID da postagem não fornecido');
}

$postId = $_GET['id_postagem'];

// Verificar conexão com banco
if ($con->connect_error) {
    returnError('Erro de conexão com banco de dados');
}

// Query para buscar comentários hierárquicos com dados do perfil
$sql = "SELECT 
            c.id,
            c.texto,
            c.data,
            c.comentario_pai_id,
            c.nivel,
            COALESCE(pf.nomexi, u.usuario) as usuario,
            CASE 
                WHEN pf.caminho IS NOT NULL AND pf.caminho != '' THEN pf.caminho
                ELSE CONCAT('https://ui-avatars.com/api/?name=', REPLACE(COALESCE(pf.nomexi, u.usuario, 'User'), ' ', '+'), '&background=ff6600&color=fff&size=50')
            END as foto_usuario
        FROM comentarios c
        INNER JOIN login u ON c.id_usuario = u.id
        LEFT JOIN perfil pf ON c.id_usuario = pf.id
        WHERE c.id_conteudo = ? 
        AND c.tipo_conteudo = 'postagem'
        ORDER BY 
            CASE WHEN c.comentario_pai_id IS NULL THEN c.id ELSE c.comentario_pai_id END,
            c.comentario_pai_id IS NULL DESC,
            c.data ASC";

$stmt = $con->prepare($sql);
if (!$stmt) {
    returnError('Erro ao preparar consulta: ' . $con->error);
}

$stmt->bind_param("s", $postId);
$stmt->execute();
$result = $stmt->get_result();

$comentarios = [];
while ($row = $result->fetch_assoc()) {
    $comentarios[] = $row;
}

returnSuccess($comentarios);
?>

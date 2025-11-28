<?php
// Teste específico para o sistema de geração de ID
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Responder imediatamente para requisições OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    require_once 'config.php';

    // Verificar conexão
    if ($con->connect_error) {
        throw new Exception("Erro de conexão: " . $con->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON inválido: ' . json_last_error_msg());
        }

        if (isset($data['idCriado'])) {
            $id = $data['idCriado'];

            // Verificar se o ID já existe
            $stmt = $con->prepare("SELECT id FROM login WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Erro ao preparar consulta: " . $con->error);
            }

            $stmt->bind_param("s", $id);
            if (!$stmt->execute()) {
                throw new Exception("Erro ao executar consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID já existe',
                    'duplicate' => true
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'ID disponível',
                    'id' => $id
                ]);
            }
            $stmt->close();
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'ID não recebido'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Método não permitido'
        ]);
    }

} catch (Exception $e) {
    error_log("Erro no teste de ID: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro: ' . $e->getMessage()
    ]);
}
?>
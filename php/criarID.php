<?php
/*
 * Arquivo para gerar IDs únicos para usuários
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Incluir configuração do banco de dados
require_once 'config.php';

try {
    // Verificar se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    // Obter dados JSON do corpo da requisição
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['idCriado']) || empty($data['idCriado'])) {
        throw new Exception('ID não fornecido');
    }

    $idCriado = $data['idCriado'];

    // Verificar se o ID já existe no banco de dados
    // Verificar na tabela 'login' (usuários)
    $stmt = $con->prepare("SELECT id FROM login WHERE id = ?");
    $stmt->bind_param("s", $idCriado);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ID duplicado
        echo json_encode([
            'success' => false,
            'duplicate' => true,
            'message' => 'ID já existe no banco de dados'
        ]);
    } else {
        // ID único
        echo json_encode([
            'success' => true,
            'duplicate' => false,
            'id' => $idCriado,
            'message' => 'ID válido e único'
        ]);
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

<?php
// Arquivo para processar reações (curtidas/descurtidas) nas postagens
error_reporting(0); // Desabilitar exibição de erros na saída
ini_set('display_errors', 0); // Não mostrar erros na página

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

// Verifica se os dados necessários foram enviados
if (isset($input['id'])) {
    $id_conteudo = $input['id'];
    $like = isset($input['like']) ? $input['like'] : false;
    $dislike = isset($input['dislike']) ? $input['dislike'] : false;
    
    // Verificar se a conexão existe
    if (!$con) {
        error_log("Erro: Conexão com banco não estabelecida");
        echo json_encode(['success' => false, 'error' => 'Erro de conexão com banco de dados']);
        exit;
    }
    
    // Verifica se já existe uma reação deste usuário para esta postagem
    try {
        $stmt = $con->prepare('SELECT tipo FROM reacoes WHERE id_usuario = ? AND id_conteudo = ? AND tipo_conteudo = "postagem"');
        if (!$stmt) {
            throw new Exception("Erro na preparação da query: " . $con->error);
        }
        $stmt->bind_param('ss', $id_usuario, $id_conteudo);
        $stmt->execute();
        $result = $stmt->get_result();
    } catch (Exception $e) {
        error_log("Erro SQL: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Erro interno do servidor']);
        exit;
    }
    
    // Se não existe reação e o usuário está removendo (estado null)
    if ($result->num_rows == 0 && !$like && !$dislike) {
        echo json_encode(['success' => true, 'action' => 'nenhuma_acao_necessaria']);
        exit;
    }
    
    // Se já existe uma reação
    if ($result->num_rows > 0) {
        $reacao = $result->fetch_assoc();
        
        // Se o usuário está removendo a reação (estado null)
        if (!$like && !$dislike) {
            $stmt = $con->prepare('DELETE FROM reacoes WHERE id_usuario = ? AND id_conteudo = ? AND tipo_conteudo = "postagem"');
            $stmt->bind_param('ss', $id_usuario, $id_conteudo);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'action' => 'reacao_removida']);
            exit;
        }
        
        // Se o usuário está alterando o tipo de reação
        $novo_tipo = $like ? 'like' : 'dislike';
        
        if ($reacao['tipo'] != $novo_tipo) {
            $stmt = $con->prepare('UPDATE reacoes SET tipo = ?, data = NOW() WHERE id_usuario = ? AND id_conteudo = ? AND tipo_conteudo = "postagem"');
            $stmt->bind_param('sss', $novo_tipo, $id_usuario, $id_conteudo);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'action' => 'reacao_atualizada']);
            exit;
        } else {
            echo json_encode(['success' => true, 'action' => 'reacao_mantida']);
            exit;
        }
    } else {
        // Se não existe reação e o usuário está adicionando uma
        $tipo = $like ? 'like' : 'dislike';
        
        // Gerar ID único para a reação
        $id_reacao = uniqid();
        $stmt = $con->prepare('INSERT INTO reacoes (id_usuario, tipo_conteudo, id_conteudo, id, tipo, data) VALUES (?, "postagem", ?, ?, ?, NOW())');
        $stmt->bind_param('ssss', $id_usuario, $id_conteudo, $id_reacao, $tipo);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'action' => 'reacao_adicionada']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao adicionar reação: ' . $con->error]);
        }
        exit;
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
    exit;
}
?>
<?php
// API para gerenciar notificações
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Verificar se usuário está logado
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado']);
    exit;
}

$id_usuario = $_SESSION['id'];
$acao = $_GET['acao'] ?? $_POST['acao'] ?? '';

switch ($acao) {
    case 'buscar':
        // Buscar últimas 10 notificações
        $sql = "SELECT n.*, 
                       COALESCE(p.nomexi, l.usuario) as nome_origem,
                       COALESCE(p.caminho, CONCAT('https://ui-avatars.com/api/?name=', REPLACE(COALESCE(p.nomexi, l.usuario, 'User'), ' ', '+'), '&background=ff6600&color=fff&size=50')) as foto_origem
                FROM notificacoes n
                LEFT JOIN login l ON n.id_usuario_origem = l.id
                LEFT JOIN perfil p ON n.id_usuario_origem = p.id
                WHERE n.id_usuario_destino = ?
                ORDER BY n.data_criacao DESC
                LIMIT 10";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $notificacoes = [];
        while ($row = $result->fetch_assoc()) {
            $notificacoes[] = $row;
        }

        echo json_encode(['success' => true, 'notificacoes' => $notificacoes]);
        break;

    case 'contar_nao_lidas':
        // Contar notificações não lidas
        $sql = "SELECT COUNT(*) as total FROM notificacoes WHERE id_usuario_destino = ? AND lida = 0";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        echo json_encode(['success' => true, 'total' => $row['total']]);
        break;

    case 'marcar_lida':
        // Marcar uma notificação como lida
        $input = json_decode(file_get_contents('php://input'), true);
        $id_notif = $input['id'] ?? '';

        if (empty($id_notif)) {
            echo json_encode(['success' => false, 'error' => 'ID não fornecido']);
            exit;
        }

        $sql = "UPDATE notificacoes SET lida = 1 WHERE id = ? AND id_usuario_destino = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ss", $id_notif, $id_usuario);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao atualizar']);
        }
        break;

    case 'marcar_todas_lidas':
        // Marcar todas como lidas
        $sql = "UPDATE notificacoes SET lida = 1 WHERE id_usuario_destino = ? AND lida = 0";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $id_usuario);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao atualizar']);
        }
        break;

    case 'buscar_preferencias':
        // Buscar preferências do usuário
        $sql = "SELECT * FROM preferencias_notificacoes WHERE id_usuario = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $preferencias = $result->fetch_assoc();
            echo json_encode(['success' => true, 'preferencias' => $preferencias]);
        } else {
            echo json_encode(['success' => false, 'preferencias' => null]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Ação inválida']);
}
?>
<?php
// Função para criar notificações
require_once 'config.php';

function criarNotificacao($id_usuario_destino, $id_usuario_origem, $tipo, $id_conteudo, $mensagem) {
    global $con;
    
    // Não criar notificação se for para si mesmo
    if ($id_usuario_destino == $id_usuario_origem) {
        return false;
    }
    
    // Verificar preferências do usuário
    $sqlPref = "SELECT * FROM preferencias_notificacoes WHERE id_usuario = ?";
    $stmtPref = $con->prepare($sqlPref);
    $stmtPref->bind_param("s", $id_usuario_destino);
    $stmtPref->execute();
    $resultPref = $stmtPref->get_result();
    
    // Se não tem preferências, criar com padrão (tudo ativo)
    if ($resultPref->num_rows == 0) {
        $sqlInsertPref = "INSERT INTO preferencias_notificacoes (id_usuario) VALUES (?)";
        $stmtInsertPref = $con->prepare($sqlInsertPref);
        $stmtInsertPref->bind_param("s", $id_usuario_destino);
        $stmtInsertPref->execute();
        $preferencias = ['notif_comentarios' => 1, 'notif_reacoes' => 1, 'notif_respostas' => 1];
    } else {
        $preferencias = $resultPref->fetch_assoc();
    }
    
    // Verificar se o tipo de notificação está ativo
    $ativo = false;
    if ($tipo == 'comentario' && $preferencias['notif_comentarios']) $ativo = true;
    if ($tipo == 'reacao' && $preferencias['notif_reacoes']) $ativo = true;
    if ($tipo == 'resposta' && $preferencias['notif_respostas']) $ativo = true;
    
    if (!$ativo) {
        return false;
    }
    
    // Buscar nome do usuário origem
    $sqlUser = "SELECT COALESCE(p.nomexi, l.usuario) as nome FROM login l LEFT JOIN perfil p ON l.id = p.id WHERE l.id = ?";
    $stmtUser = $con->prepare($sqlUser);
    $stmtUser->bind_param("s", $id_usuario_origem);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    $nomeOrigem = "Alguém";
    if ($rowUser = $resultUser->fetch_assoc()) {
        $nomeOrigem = $rowUser['nome'];
    }
    
    // Criar mensagem completa
    $mensagemCompleta = $nomeOrigem . " " . $mensagem;
    
    // Criar notificação
    $idNotif = uniqid('notif_', true);
    $sqlNotif = "INSERT INTO notificacoes (id, id_usuario_destino, id_usuario_origem, tipo, id_conteudo, mensagem, data_criacao) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmtNotif = $con->prepare($sqlNotif);
    $stmtNotif->bind_param("ssssss", $idNotif, $id_usuario_destino, $id_usuario_origem, $tipo, $id_conteudo, $mensagemCompleta);
    
    return $stmtNotif->execute();
}
?>
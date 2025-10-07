<?php
include 'login.php';
session_start ();

global $con;

 function buscaUsuario(){

    global $con;
    // Utilizando da sessão do id para puxar outros dados do perfil
    $id_usuario = $_SESSION['id'];

    if(!isset($_SESSION['id'])){
        header("Location: ../../web/html/login1.php");;
        exit;
    }
     $id_usuario = $_SESSION['id'];

    // Buscar dados do usuário
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}

// Função para criar notificação
function criarNotificacao($mensagem){
    global $con;
    $id_usuario = $_SESSION['id'];

    $sql = "INSERT INTO notificacoes (user_id, mensagem, is_read) VALUES (?, ?, false)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("is", $id_usuario, $mensagem);
    $stmt->execute();
}

// Função para buscar notificações do usuário
function buscarNotificacoes(){
    global $con;
    $id_usuario = $_SESSION['id'];

    $sql = "SELECT * FROM notificacoes WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

// Função para marcar notificação como lida
function marcarComoLida($id_notificacao){
    global $con;

    $sql = "UPDATE notificacoes SET is_read = true WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_notificacao);
    $stmt->execute();
}
?>
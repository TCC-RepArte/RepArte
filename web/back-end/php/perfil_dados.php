<?php

include 'config.php';
session_start();

// Chamando o BD
global $con;

// Função que puxa dados do usuario logado
 function buscaUsuario(){

    global $con;
    // Utilizando da sessão do id para puxar outros dados do perfil
    $id_usuario = $_SESSION['id'];

    if(!isset($_SESSION['id'])){
        header("Location: ../../web/html/login1.php");;
        exit;
    }

    // Fazendo a seleção dos dados
    $stmt = $con->prepare("SELECT * from perfil where id = ?");
    $stmt->bind_param("s", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    // Tranformando o resultado em uma array, o row
    if ($result && $row = $result->fetch_assoc()) {
        return $row;
    } else {
        echo "Erro ao consultar usuário ou usuário não encontrado.";
    }

    
}

?>
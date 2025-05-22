<?php

require_once 'signup.php';
$con = new mysqli("localhost", "root", '', "reparte");
global $id;

if(isset ($_FILES['foto']) && isset($_POST['nome']) && isset($_POST['desc'])){

    global $id;

    //Recebendo dados e atribuindo a variaveis
    $nome = $_POST['nome'];
    $foto = $_FILES['foto'];
    $pasta = '../../imagens/';
    $descricao = $_POST['desc'];



    if($foto['error']){
        die('Falha ao enviar imagem');
    }

    //Verificando a extensão das imagens
    $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
    if($extensao != 'jpg' && $extensao != 'png' && $extensao != 'jpeg'){

        die("Formato invalido para a imagem");
        return;

    }
 
    // Gerando um novo nome para a foto
    $novo_nome = uniqid();
    $nome_def = $novo_nome . '.' . $extensao ;
    $caminho_completo = $pasta . $nome_def;

    //definindo dat apra horário de Brasília
    date_default_timezone_set('America/Sao_Paulo');

    //atribuindo data a uma variável
    $date = date('Y-m-d H:i:s');

    //Movendo foto para a pasta
    $insercao = move_uploaded_file($foto["tmp_name"], $caminho_completo);

    $stmt = $con->prepare("INSERT INTO `perfil`(`foto`, `path`, `data`, `nomexi`, `desc`, `id`) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nome_def, $caminho_completo, $date, $nome, $descricao, $id);
    $stmt->execute();

}
<?php

require_once '../../../confidencial/php/conexao.php';

if(isset ($_FILES['foto']) && isset($_POST['nome'])){

    //Recebendo dados e atribuindo a variaveis
    $nome = $_POST['nome'];
    $foto = $_FILES['foto'];
    $pasta = '../../../confidencial/teste/';



    if($foto['error']){
        die('Falha ao enviar imagem');
    }

    //Verificando a extensão das imagens
    $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

    if($extensao != 'jpg' && $extensao != 'png' && $extensao != 'jpeg'){

        die("Formato invalido para a imagem");

    }

    // Gerando um novo nome para a foto
    $nome_da_foto = $foto['name'];
    $novo_nome = uniqid();
    $nome_def = $novo_nome . '.' . $extensao;

    //Movendo foto para a pasta
    $insercao = move_uploaded_file($foto["tmp_name"], $pasta . $nome_def);

    //adaptando nome para padrão do reparte
    $nomexi = ;

    //Verificando existência de nome

}
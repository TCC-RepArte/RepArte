<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'signup.php';
$con = new mysqli("localhost", "root", '', "reparte");

if(isset($_FILES['envft']) && isset($_POST['nomeexi']) && isset($_POST['desc'])){


    //Recebendo dados e atribuindo a variaveis
    if(isset($_FILES['envft']) && isset($_POST['nomeexi']) && isset($_POST['desc'])) {
        
        // Recebendo dados e atribuindo a variaveis

        $nome = $_POST['nomeexi'];
        $foto = $_FILES['envft'];
        $descricao = $_POST['desc'];
        $pasta = '../../imagens/';
        
        $id = $_SESSION['id'] ?? null;

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

        //Inserindo na tabela
        $stmt = $con->prepare("INSERT INTO perfil (foto, caminho, data_perf, nomexi, descri, id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nome_def, $caminho_completo, $date, $nome, $descricao, $id);
        $stmt->execute();

    

    } else{

        echo "faltaram dADOS";
        var_dump($id);

    }
}
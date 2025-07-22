<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
$con = new mysqli("localhost", "root", '', "reparte");

// Verificar se o usuário está logado
if(!isset($_SESSION['id'])) {
    header("Location: ../../html/login1.php");
    exit();
}

if(isset($_FILES['envft']) && isset($_POST['nomeexi']) && isset($_POST['desc'])){
    // Recebendo dados e atribuindo a variaveis
    $nome = $_POST['nomeexi'];
    $foto = $_FILES['envft'];
    $descricao = $_POST['desc'];
    $pasta = '../../imagens/';
    $endereco = '/RepArte/web/imagens/';
    
    $id = $_SESSION['id'] ?? null;

    if(!$id) {
        $_SESSION['erro_perfil'] = "ID de usuário não encontrado.";
        header("Location: ../../html/perfil.php");
        exit();
    }

    if($foto['error']){
        $_SESSION['erro_perfil'] = "Falha ao enviar imagem: " . $foto['error'];
        header("Location: ../../html/perfil.php");
        exit();
    }

    //Verificando a extensão das imagens
    $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
    if($extensao != 'jpg' && $extensao != 'png' && $extensao != 'jpeg'){
        $_SESSION['erro_perfil'] = "Formato inválido para a imagem. Use apenas JPG, JPEG ou PNG.";
        header("Location: ../../html/perfil.php");
        exit();
    }

    // Gerando um novo nome para a foto
    $novo_nome = uniqid();
    $nome_def = $novo_nome . '.' . $extensao;
    $caminho_inserir = $pasta . $nome_def;
    $caminho_completo= $endereco . $nome_def;

    // Definindo data para horário de Brasília
    date_default_timezone_set('America/Sao_Paulo');

    // Atribuindo data a uma variável
    $date = date('Y-m-d H:i:s');

    // Movendo foto para a pasta
    $insercao = move_uploaded_file($foto["tmp_name"], $caminho_inserir);
    
    if(!$insercao) {
        $_SESSION['erro_perfil'] = "Falha ao salvar a imagem no servidor.";
        header("Location: ../../html/perfil.php");
        exit();
    }

    //Inserindo na tabela
    $stmt = $con->prepare("INSERT INTO perfil (foto, caminho, data_perf, nomexi, descri, id) VALUES (?, ?, ?, ?, ?, ?)");
    
    if(!$stmt) {
        $_SESSION['erro_perfil'] = "Erro ao preparar inserção: " . $con->error;
        header("Location: ../../html/perfil.php");
        exit();
    }
    
    $stmt->bind_param("ssssss", $nome_def, $caminho_completo, $date, $nome, $descricao, $id);
    
    if($stmt->execute()) {
        // Sucesso
        $_SESSION['sucesso_perfil'] = "Perfil criado com sucesso!";
        header("Location: ../../html/telainicial.php");
        exit();
    } else {
        $_SESSION['erro_perfil'] = "Erro ao inserir dados: " . $stmt->error;
        header("Location: ../../html/perfil.php");
        exit();
    }


} else {
    $_SESSION['erro_perfil'] = "Todos os campos são obrigatórios.";
    header("Location: ../../html/perfil.php");
    exit();
}



?>
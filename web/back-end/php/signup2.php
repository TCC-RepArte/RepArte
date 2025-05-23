<?php

require_once 'signup.php';
$con = new mysqli("localhost", "root", '', "reparte");
global $id;

// Verifica se o processo de cadastro está em andamento
if (!isset($_SESSION['signup_in_progress']) || !isset($_SESSION['id'])) {
    header("Location: ../../web/html/cadastro.php");
    exit;
}

if(isset($_FILES['envft']) && isset($_POST['nomeexi']) && isset($_POST['desc'])) {
    // Recebendo dados e atribuindo a variaveis
    $nome = $_POST['nomeexi'];
    $foto = $_FILES['envft'];
    $descricao = $_POST['desc'];
    $pasta = '../../imagens/';
    
    $id = $_SESSION['id'];

    if($foto['error']) {
        die('Falha ao enviar imagem');
    }

    // Verificando a extensão das imagens
    $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
    if($extensao != 'jpg' && $extensao != 'png' && $extensao != 'jpeg'){

        die("Formato invalido para a imagem");
        return;
    }

    // Gerando um novo nome para a foto
    $novo_nome = uniqid();
    $nome_def = $novo_nome . '.' . $extensao;
    $caminho_completo = $pasta . $nome_def;

    // Definindo data para horário de Brasília
    date_default_timezone_set('America/Sao_Paulo');
    $date = date('Y-m-d H:i:s');

    // Iniciando transação
    $con->begin_transaction();

    try {
        // Movendo foto para a pasta
        if (!move_uploaded_file($foto["tmp_name"], $caminho_completo)) {
            throw new Exception("Erro ao mover a imagem");
        }

        // Obtendo dados da tabela temporária
        $stmt = $con->prepare("SELECT usuario, email, senha FROM temp_signup WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $temp_data = $result->fetch_assoc();
        $stmt->close();

        if (!$temp_data) {
            throw new Exception("Dados temporários não encontrados");
        }

        // Inserindo dados na tabela login
        $stmt = $con->prepare("INSERT INTO login (usuario, email, senha, id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $temp_data['usuario'], $temp_data['email'], $temp_data['senha'], $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Erro ao salvar dados do usuário");
        }
        $stmt->close();

        // Inserindo dados na tabela perfil
        $stmt = $con->prepare("INSERT INTO perfil (foto, caminho, data_perf, nomexi, descri, id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nome_def, $caminho_completo, $date, $nome, $descricao, $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Erro ao salvar perfil");
        }
        $stmt->close();

        // Marcando o registro temporário como completo
        $stmt = $con->prepare("UPDATE temp_signup SET completed = 1 WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->close();

        // Commit da transação
        $con->commit();

        // Limpando a sessão
        unset($_SESSION['signup_in_progress']);
        
        // Redirecionando para a página de login
        header("Location: ../../web/html/login1.php");
        exit;

    } catch (Exception $e) {
        // Rollback em caso de erro
        $con->rollback();
        
        // Removendo a imagem se ela foi enviada
        if (file_exists($caminho_completo)) {
            unlink($caminho_completo);
        }
        
        die("Erro no processo de cadastro: " . $e->getMessage());
    }
} else {
    echo "Dados incompletos";
}
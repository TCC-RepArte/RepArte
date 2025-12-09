<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
require_once 'config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../login1.php");
    exit();
}

// Recebendo dados do formulário
$nome = $_POST['nomeexi'] ?? '';
$descricao = $_POST['desc'] ?? '';
$id = $_SESSION['id'];

// Verificar se o perfil já existe
$stmt_check = $con->prepare("SELECT id FROM perfil WHERE id = ?");
$stmt_check->bind_param("s", $id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$perfil_existe = $result_check->num_rows > 0;

// Processar upload de foto se houver
$caminho_completo = null;
$nome_def = null;

if (isset($_FILES['envft']) && $_FILES['envft']['error'] == 0) {
    $foto = $_FILES['envft'];
    $pasta = '../images/';
    $endereco = 'images/';

    // Verificando a extensão das imagens
    $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
    if ($extensao != 'jpg' && $extensao != 'png' && $extensao != 'jpeg' && $extensao != 'gif' && $extensao != 'webp') {
        $_SESSION['erro_perfil'] = "Formato inválido para a imagem. Use apenas JPG, JPEG, PNG, GIF ou WEBP.";
        header("Location: ../editar_perfil.php");
        exit();
    }

    // Gerando um novo nome para a foto
    $novo_nome = uniqid();
    $nome_def = $novo_nome . '.' . $extensao;
    $caminho_inserir = $pasta . $nome_def;
    $caminho_completo = $endereco . $nome_def;

    // Movendo foto para a pasta
    $insercao = move_uploaded_file($foto["tmp_name"], $caminho_inserir);

    if (!$insercao) {
        $_SESSION['erro_perfil'] = "Falha ao salvar a imagem no servidor.";
        header("Location: ../editar_perfil.php");
        exit();
    }

    // Deletar foto antiga se existir
    if ($perfil_existe) {
        $stmt_foto = $con->prepare("SELECT caminho FROM perfil WHERE id = ?");
        $stmt_foto->bind_param("s", $id);
        $stmt_foto->execute();
        $result_foto = $stmt_foto->get_result();
        if ($row_foto = $result_foto->fetch_assoc()) {
            $foto_antiga = '../' . $row_foto['caminho'];
            if (file_exists($foto_antiga)) {
                unlink($foto_antiga);
            }
        }
    }
}

// Definindo data para horário de Brasília
date_default_timezone_set('America/Sao_Paulo');
$date = date('Y-m-d H:i:s');

if ($perfil_existe) {
    // Atualizar perfil existente
    if ($caminho_completo) {
        // Atualizar com nova foto
        $stmt = $con->prepare("UPDATE perfil SET foto = ?, caminho = ?, nomexi = ?, descri = ?, data_perf = ? WHERE id = ?");
        $stmt->bind_param("ssssss", $nome_def, $caminho_completo, $nome, $descricao, $date, $id);
    } else {
        // Atualizar sem foto
        $stmt = $con->prepare("UPDATE perfil SET nomexi = ?, descri = ?, data_perf = ? WHERE id = ?");
        $stmt->bind_param("ssss", $nome, $descricao, $date, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['sucesso_perfil'] = "Perfil atualizado com sucesso!";
        header("Location: ../meu_perfil.php");
        exit();
    } else {
        $_SESSION['erro_perfil'] = "Erro ao atualizar dados: " . $stmt->error;
        header("Location: ../editar_perfil.php");
        exit();
    }
} else {
    // Criar novo perfil
    if (!$caminho_completo) {
        $_SESSION['erro_perfil'] = "Por favor, selecione uma foto de perfil.";
        header("Location: ../editar_perfil.php");
        exit();
    }

    $stmt = $con->prepare("INSERT INTO perfil (foto, caminho, data_perf, nomexi, descri, id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nome_def, $caminho_completo, $date, $nome, $descricao, $id);

    if ($stmt->execute()) {
        $_SESSION['sucesso_perfil'] = "Perfil criado com sucesso!";
        header("Location: ../meu_perfil.php");
        exit();
    } else {
        $_SESSION['erro_perfil'] = "Erro ao inserir dados: " . $stmt->error;
        header("Location: ../editar_perfil.php");
        exit();
    }
}

?>
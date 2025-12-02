<?php
session_start();
require_once 'config.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../login1.php");
    exit;
}

// Verifica se foi passado um ID na URL
if (isset($_GET['id'])) {
    $id_post = $_GET['id'];
    $id_usuario = $_SESSION['id'];

    // 1. Verifica se o post existe e se pertence ao usuário logado (Segurança)
    // Isso impede que alguém delete o post de outro mudando o ID na URL
    $sql_check = "SELECT id FROM postagens WHERE id = ? AND id_usuario = ?";
    $stmt_check = $con->prepare($sql_check);
    $stmt_check->bind_param("ss", $id_post, $id_usuario);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // 2. O post é meu, então posso deletar

        // Deleta o post
        $sql_delete = "DELETE FROM postagens WHERE id = ?";
        $stmt_delete = $con->prepare($sql_delete);
        $stmt_delete->bind_param("s", $id_post);

        if ($stmt_delete->execute()) {
            // Sucesso: Volta para a tela inicial
            header("Location: ../telainicial.php");
            exit;
        } else {
            // Erro no SQL
            echo "Erro ao deletar: " . $con->error;
        }
    } else {
        // Tentou deletar post de outro ou post não existe
        echo "Você não tem permissão para deletar este post.";
    }
} else {
    // Se não tiver ID, volta pra home
    header("Location: ../telainicial.php");
    exit;
}
?>
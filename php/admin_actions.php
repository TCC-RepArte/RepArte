<?php
session_start();
require 'config.php';

// Segurança Dupla: Verifica novamente se é o admin antes de fazer qualquer coisa
if (!isset($_SESSION['id']) || $_SESSION['id'] !== 'rFRCxqU-Yze') {
    die("Acesso negado. Você não é administrador.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Ação: Deletar Item (Manual ou via Denúncia)
    if ($action === 'delete_manual') {
        $id_item = $_POST['id_item'];
        $tipo = $_POST['tipo_item'];
        $id_denuncia = $_POST['id_denuncia'] ?? null;

        $table = '';
        if ($tipo === 'postagem') $table = 'postagens';
        elseif ($tipo === 'comentario') $table = 'comentarios';
        elseif ($tipo === 'usuario') $table = 'login'; // Deletar do login apaga perfil em cascata

        if ($table) {
            // Prepara delete seguro
            $stmt = $con->prepare("DELETE FROM $table WHERE id = ?");
            $stmt->bind_param("s", $id_item);
            
            if ($stmt->execute()) {
                // Se tiver ID de denúncia atrelado, apaga ela também pois o problema foi resolvido
                if ($id_denuncia) {
                    $con->query("DELETE FROM denuncias WHERE id = '$id_denuncia'");
                }
                echo "<script>alert('Item deletado com sucesso!'); window.location.href='../admin.php';</script>";
            } else {
                echo "<script>alert('Erro ao deletar: " . $con->error . "'); window.location.href='../admin.php';</script>";
            }
        } else {
            echo "<script>alert('Tipo de item inválido.'); window.location.href='../admin.php';</script>";
        }
    } 
    // Ação: Ignorar Denúncia (Apaga apenas o registro da denúncia)
    elseif ($action === 'dismiss_report') {
        $id_denuncia = $_POST['id_denuncia'];
        $con->query("DELETE FROM denuncias WHERE id = '$id_denuncia'");
        header("Location: ../admin.php");
    }
}
?>
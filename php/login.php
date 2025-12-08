<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
require 'config.php';
require 'remember_me.php';
require '2fa.php';

// Verificar se já está logado
if (isset($_SESSION['id'])) {
    $_SESSION['val'] = ['Usuario já está logado!'];
    header('Location: ../telainicial.php');
    exit();
}

// Conexão com o banco
global $con;

// Verificar conexão
if ($con->connect_error) {
    $_SESSION['erros'] = ['Não foi possível realizar conexão com o banco'];
    exit();
}

if (isset($_POST['usuario']) && isset($_POST['senha'])) {
    // Limpar os dados recebidos
    $usuario = $con->real_escape_string($_POST['usuario']);
    $senha = $_POST['senha'];
    $lembrar = isset($_POST['lembrar']);

    // Verificar se é email ou nome de usuário
    if (filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
        // Se for email, busca pelo email
        $stmt = $con->prepare("SELECT id, usuario, email, senha, 2fa_ativo FROM login WHERE email = ?");
    } else {
        // Se não for email, busca pelo nome de usuário (com ou sem @)
        $stmt = $con->prepare("SELECT id, usuario, email, senha, 2fa_ativo FROM login WHERE usuario = ? OR usuario = ?");
        $usuarioComArroba = '@' . $usuario;
    }

    if (!$stmt) {
        $_SESSION['erros'] = ['Não foi possível verificar os dados do login!'];
        header('Location: ../login1.php');
        exit();
    }

    // Bind dos parâmetros dependendo do tipo de login
    if (filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
        $stmt->bind_param("s", $usuario);
    } else {
        $stmt->bind_param("ss", $usuario, $usuarioComArroba);
    }

    if (!$stmt->execute()) {
        $_SESSION['erros'] = ['Houve um erro no processo de verificação do e-mail/senha'];
        header('Location: ../login1.php');
        exit();
    }

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario_data = $resultado->fetch_assoc();

        // Verificar a senha
        if (password_verify($senha, $usuario_data['senha'])) {

            // Verificar se tem 2FA ativo
            if ($usuario_data['2fa_ativo'] == 1) {
                // Gera e envia código 2FA
                $codigo = salvarCodigo2FA($con, $usuario_data['id']);

                if ($codigo && enviarCodigo2FAEmail($usuario_data['email'], $codigo, $usuario_data['usuario'])) {
                    // Armazena dados temporários na sessão
                    $_SESSION['2fa_pending_user'] = $usuario_data['id'];
                    $_SESSION['2fa_pending_email'] = $usuario_data['email'];
                    $_SESSION['2fa_pending_usuario'] = $usuario_data['usuario'];
                    $_SESSION['2fa_lembrar'] = $lembrar;

                    // Redireciona para página de verificação 2FA
                    header('Location: ../verificar_2fa.php');
                    exit();
                } else {
                    $_SESSION['erros'] = ['Erro ao enviar código de verificação. Tente novamente.'];
                    header('Location: ../login1.php');
                    exit();
                }
            } else {
                // Login normal sem 2FA
                $_SESSION['usuario'] = $usuario_data['usuario'];
                $_SESSION['email'] = $usuario_data['email'];
                $_SESSION['id'] = $usuario_data['id'];

                // Se marcou "lembre de mim", salva o token
                if ($lembrar) {
                    salvarTokenLembreMim($con, $usuario_data['id']);
                }

                $_SESSION['val'] = ['Logado com sucesso!'];
                header('Location: ../telainicial.php');
            }

        } else {
            $_SESSION['erros'] = ['Senha incorreta!'];
            header('Location: ../login1.php');
            exit();
        }

    } else {

        $_SESSION['erros'] = ['Usuario não encontrado!'];
        header('Location: ../login1.php');
        exit();

    }

    $stmt->close();

} else {

    $_SESSION['erros'] = ['Dados estão incompletos!'];
    header('Location: ../login1.php');
    exit();

}

$con->close();
?>
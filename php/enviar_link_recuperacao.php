<?php
//Enviar código de recuperação de senha usando PHPMailer
session_start();
require_once 'config.php';
require_once 'limpar_dados_temporarios.php';

unset($_SESSION['erros_recuperacao']);
unset($_SESSION['val_recuperacao']);

global $con;

if ($con->connect_error) {
    $_SESSION['erros_recuperacao'] = ['Não foi possível realizar conexão com o banco'];
    header('Location: ../emailesqueceu.php');
    exit();
}

if (isset($_POST['usuario_email'])) {
    $usuario_email = $con->real_escape_string($_POST['usuario_email']);

    // Verificar se é email ou usuário
    if (filter_var($usuario_email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $con->prepare("SELECT id, email, usuario FROM login WHERE email = ?");
    } else {
        $usuario_email = substr_replace($usuario_email, '@', 0, 0);
        $stmt = $con->prepare("SELECT id, email, usuario FROM login WHERE usuario = ?");
    }

    if (!$stmt) {
        $_SESSION['erros_recuperacao'] = ['Erro ao processar solicitação'];
        header('Location: ../emailesqueceu.php');
        exit();
    }

    $stmt->bind_param("s", $usuario_email);

    if (!$stmt->execute()) {
        $_SESSION['erros_recuperacao'] = ['Erro ao verificar usuário'];
        header('Location: ../emailesqueceu.php');
        exit();
    }

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        // Verificar se já existe código válido
        $stmt_check = $con->prepare("SELECT id, token FROM recuperacao_senha WHERE id_usuario = ? AND usado = 0 AND data_expiracao > NOW() ORDER BY data_criacao DESC LIMIT 1");
        $stmt_check->bind_param("s", $usuario['id']);
        $stmt_check->execute();
        $resultado_check = $stmt_check->get_result();

        if ($resultado_check->num_rows > 0) {
            // Código já existe
            $codigo_existente = $resultado_check->fetch_assoc();
            $_SESSION['val_recuperacao'] = ['Um código já foi enviado. Verifique seu email.'];
            $_SESSION['email_recuperacao'] = $usuario['email'];
            $_SESSION['codigo_recuperacao'] = $codigo_existente['token'];
            header('Location: ../verificar_codigo_senha.php');
            exit();
        }
        $stmt_check->close();

        // Gerar código
        $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Definir expiração (1 hora)
        $data_criacao = date('Y-m-d H:i:s');
        $data_expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Inserir no banco (token armazena o código)
        $stmt_codigo = $con->prepare("INSERT INTO recuperacao_senha (id_usuario, token, data_criacao, data_expiracao, usado) VALUES (?, ?, ?, ?, 0)");
        $stmt_codigo->bind_param("ssss", $usuario['id'], $codigo, $data_criacao, $data_expiracao);

        if ($stmt_codigo->execute()) {
            // Tentar enviar email com PHPMailer
            $emailEnviado = false;

            try {
                require_once 'email_phpmailer.php';

                $assunto = "Recuperacao de Senha - RepArte";
                $mensagem = templateRecuperacaoSenha($codigo, $usuario['usuario']);

                $emailEnviado = enviarEmailPHPMailer($usuario['email'], $assunto, $mensagem, true);

            } catch (Exception $e) {
                error_log("Erro ao enviar email de recuperação: " . $e->getMessage());
            }

            // Salvar na sessão
            $_SESSION['email_recuperacao'] = $usuario['email'];
            $_SESSION['codigo_recuperacao'] = $codigo;
            $_SESSION['val_recuperacao'] = [$emailEnviado ? 'Código enviado para seu email!' : 'Código gerado (verifique a página)'];

            header('Location: ../verificar_codigo_senha.php');
            exit();
        } else {
            $_SESSION['erros_recuperacao'] = ['Erro ao gerar código de recuperação'];
            header('Location: ../emailesqueceu.php');
            exit();
        }

        $stmt_codigo->close();

    } else {
        $_SESSION['erros_recuperacao'] = ['Usuário ou e-mail não encontrado'];
        header('Location: ../emailesqueceu.php');
        exit();
    }

    $stmt->close();

} else {
    $_SESSION['erros_recuperacao'] = ['Dados incompletos'];
    header('Location: ../emailesqueceu.php');
    exit();
}

$con->close();
?>
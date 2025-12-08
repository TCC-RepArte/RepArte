<?php
// Funções para 2FA (Autenticação de Dois Fatores)

// Gera um código de 6 dígitos
function gerarCodigo2FA()
{
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Salva o código 2FA no banco
function salvarCodigo2FA($con, $id_usuario)
{
    $codigo = gerarCodigo2FA();
    $data_criacao = date('Y-m-d H:i:s');
    $data_expiracao = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    // Invalida códigos anteriores
    $sql_invalidar = "UPDATE codigos_2fa SET usado = 1 WHERE id_usuario = ? AND usado = 0";
    $stmt_invalidar = $con->prepare($sql_invalidar);
    $stmt_invalidar->bind_param("s", $id_usuario);
    $stmt_invalidar->execute();

    // Insere novo código
    $sql = "INSERT INTO codigos_2fa (id_usuario, codigo, data_criacao, data_expiracao) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssss", $id_usuario, $codigo, $data_criacao, $data_expiracao);

    if ($stmt->execute()) {
        return $codigo;
    }

    return false;
}

// Verifica o código 2FA
function verificarCodigo2FA($con, $id_usuario, $codigo)
{
    $sql = "SELECT id, tentativas FROM codigos_2fa 
            WHERE id_usuario = ? AND codigo = ? AND usado = 0 AND data_expiracao > NOW()
            ORDER BY data_criacao DESC LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $id_usuario, $codigo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $codigo_id = $row['id'];

        // Marca como usado
        $sql_update = "UPDATE codigos_2fa SET usado = 1 WHERE id = ?";
        $stmt_update = $con->prepare($sql_update);
        $stmt_update->bind_param("i", $codigo_id);
        $stmt_update->execute();

        return true;
    }

    // Incrementa tentativas
    $sql_tentativa = "UPDATE codigos_2fa SET tentativas = tentativas + 1 
                      WHERE id_usuario = ? AND codigo = ? AND usado = 0";
    $stmt_tentativa = $con->prepare($sql_tentativa);
    $stmt_tentativa->bind_param("ss", $id_usuario, $codigo);
    $stmt_tentativa->execute();

    return false;
}

// Envia código 2FA por email usando PHPMailer
function enviarCodigo2FAEmail($email, $codigo, $usuario)
{
    require_once __DIR__ . '/email_phpmailer.php';

    $assunto = "RepArte - Código de Verificação 2FA";
    $mensagem = "
    <!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
            .header { text-align: center; color: #ff6600; margin-bottom: 30px; }
            .codigo { background: #f0f0f0; border: 2px dashed #ff6600; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #ff6600; margin: 20px 0; border-radius: 10px; }
            .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
            ul { line-height: 1.8; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1 class='header'>RepArte</h1>
            <h2>Autenticação de Dois Fatores</h2>
            <p>Olá, <strong>" . htmlspecialchars($usuario) . "</strong>!</p>
            <p>Você solicitou acesso à sua conta com autenticação de dois fatores.</p>
            <p>Use o código abaixo para completar seu login:</p>
            <div class='codigo'>" . htmlspecialchars($codigo) . "</div>
            <p><strong>⚠️ Importante:</strong></p>
            <ul>
                <li>Este código expira em <strong>10 minutos</strong></li>
                <li>Nunca compartilhe este código com ninguém</li>
                <li>Se você não solicitou este código, ignore este email</li>
            </ul>
            <div class='footer'>
                <p>Este é um email automático, por favor não responda.</p>
                <p>&copy; 2024 RepArte - Todos os direitos reservados</p>
            </div>
        </div>
    </body>
    </html>
    ";

    return enviarEmailPHPMailer($email, $assunto, $mensagem, true);
}

// Ativa o 2FA para um usuário
function ativar2FA($con, $id_usuario)
{
    $sql = "UPDATE login SET 2fa_ativo = 1 WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $id_usuario);
    return $stmt->execute();
}

// Desativa o 2FA para um usuário
function desativar2FA($con, $id_usuario)
{
    $sql = "UPDATE login SET 2fa_ativo = 0, 2fa_secret = NULL WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $id_usuario);
    return $stmt->execute();
}

// Verifica se o usuário tem 2FA ativo
function verificar2FAAtivo($con, $id_usuario)
{
    $sql = "SELECT 2fa_ativo FROM login WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return (bool) $row['2fa_ativo'];
    }

    return false;
}
?>
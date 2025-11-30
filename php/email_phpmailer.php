<?php


// CONFIGURAÇÕES DE EMAIL - SENDGRID


// SendGrid SMTP (Recomendado para InfinityFree)
define('EMAIL_HOST', 'smtp.sendgrid.net');
define('EMAIL_PORT', 587);
define('EMAIL_SMTP_SECURE', 'tls');

define('EMAIL_USERNAME', 'apikey'); // NÃO MUDE! Deve ser LITERAL "apikey"
define('EMAIL_PASSWORD', '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'); // Sua API Key
define('EMAIL_FROM', 'testereparte@gmail.com'); // Pode ser qualquer email
define('EMAIL_FROM_NAME', 'RepArte Teste');


// FUNÇÃO DE ENVIO DE EMAIL

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Envia email usando PHPMailer
 * 
 * @param string $destinatario Email do destinatário
 * @param string $assunto Assunto do email
 * @param string $mensagem Corpo do email
 * @param bool $isHTML Define se mensagem é HTML 
 * @return bool True se enviado, False se falhou
 */
function enviarEmailPHPMailer($destinatario, $assunto, $mensagem, $isHTML = true)
{
    // Tentar carregar PHPMailer
    $paths = [
        __DIR__ . '/../vendor/PHPMailer/src/Exception.php',
        __DIR__ . '/../vendor/PHPMailer/Exception.php',
        __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php',
        __DIR__ . '/../vendor/PHPMailer/PHPMailer/src/Exception.php',
        __DIR__ . '/../vendor/phpmailer/src/Exception.php',
        __DIR__ . '/../vendor/phpmailer/Exception.php',
        $_SERVER['DOCUMENT_ROOT'] . '/vendor/PHPMailer/src/Exception.php',
        $_SERVER['DOCUMENT_ROOT'] . '/vendor/phpmailer/phpmailer/src/Exception.php'
    ];

    $found = false;
    foreach ($paths as $path) {
        if (file_exists($path)) {
            $baseDir = dirname($path);
            require_once $baseDir . '/Exception.php';
            require_once $baseDir . '/PHPMailer.php';
            require_once $baseDir . '/SMTP.php';
            $found = true;
            break;
        }
    }

    if (!$found) {
        error_log("PHPMailer não encontrado.");
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        // Configurações SMTP
        $mail->isSMTP();
        $mail->Host = EMAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->SMTPSecure = EMAIL_SMTP_SECURE;
        $mail->Port = EMAIL_PORT;
        $mail->CharSet = 'UTF-8';

        // Desabilitar verificação SSL
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Remetente
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);

        // Destinatário
        $mail->addAddress($destinatario);

        // Conteúdo
        $mail->isHTML($isHTML);
        $mail->Subject = $assunto;
        $mail->Body = $mensagem;

        // Versão texto
        if ($isHTML) {
            $mail->AltBody = strip_tags($mensagem);
        }

        // Enviar
        $enviado = $mail->send();

        if ($enviado) {
            error_log("Email enviado com sucesso para: " . $destinatario);
        }

        return $enviado;

    } catch (Exception $e) {
        error_log("Erro ao enviar email: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Template HTML para código de verificação de cadastro
 */
function templateCodigoVerificacao($codigo)
{
    return '
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
            .header { text-align: center; color: #667eea; margin-bottom: 30px; }
            .codigo { background: #f0f0f0; border: 2px dashed #667eea; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #667eea; margin: 20px 0; border-radius: 10px; }
            .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
            ul { line-height: 1.8; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1 class="header">RepArte</h1>
            <h2>Código de Verificação</h2>
            <p>Olá!</p>
            <p>Use o código abaixo para completar seu cadastro no RepArte:</p>
            <div class="codigo">' . htmlspecialchars($codigo) . '</div>
            <p><strong>⚠️ Importante:</strong></p>
            <ul>
                <li>Este código expira em <strong>10 minutos</strong></li>
                <li>Você tem <strong>5 tentativas</strong> para inserir o código</li>
                <li>Nunca compartilhe este código com ninguém</li>
            </ul>
            <div class="footer">
                <p>Este é um email automático, por favor não responda.</p>
                <p>&copy; 2025 RepArte - Todos os direitos reservados</p>
            </div>
        </div>
    </body>
    </html>
    ';
}

/**
 * Template HTML para código de recuperação de senha
 */
function templateRecuperacaoSenha($codigo, $nomeUsuario = '')
{
    return '
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
            .header { text-align: center; color: #667eea; margin-bottom: 30px; }
            .codigo { background: #f0f0f0; border: 2px dashed #667eea; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #667eea; margin: 20px 0; border-radius: 10px; }
            .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
            ul { line-height: 1.8; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1 class="header">RepArte</h1>
            <h2>Recuperação de Senha</h2>
            <p>Olá' . ($nomeUsuario ? ', <strong>' . htmlspecialchars($nomeUsuario) . '</strong>' : '') . '!</p>
            <p>Recebemos uma solicitação para redefinir a senha da sua conta no RepArte.</p>
            <p>Use o código abaixo para criar uma nova senha:</p>
            <div class="codigo">' . htmlspecialchars($codigo) . '</div>
            <p><strong>⚠️ Importante:</strong></p>
            <ul>
                <li>Este código expira em <strong>1 hora</strong></li>
                <li>Você tem <strong>5 tentativas</strong> para inserir o código</li>
                <li>Se você não solicitou esta recuperação, ignore este email</li>
                <li>Nunca compartilhe este código com ninguém</li>
            </ul>
            <div class="footer">
                <p>Este é um email automático, por favor não responda.</p>
                <p>&copy; 2025 RepArte - Todos os direitos reservados</p>
            </div>
        </div>
    </body>
    </html>
    ';
}
?>
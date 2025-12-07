<?php
// Configuração de email para o sistema RepArte
// Arquivo de envio de emails genérico

function enviarEmail($destinatario, $assunto, $mensagem, $emailRemetente = 'noreply@reparte.com', $nomeRemetente = 'RepArte')
{

    // Headers do email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: {$nomeRemetente} <{$emailRemetente}>" . "\r\n";
    $headers .= "Reply-To: {$emailRemetente}" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Enviar email
    $enviado = mail($destinatario, $assunto, $mensagem, $headers);

    // Log para debugging
    if ($enviado) {
        error_log("Email enviado com sucesso para: {$destinatario}");
    } else {
        error_log("Erro ao enviar email para: {$destinatario}");
    }

    return $enviado;
}

/**
 * Gera HTML para email de recuperação de senha
 */
function emailRecuperacaoSenha($nomeUsuario, $linkRecuperacao)
{
    $html = '
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recuperação de Senha - RepArte</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
            }
            .header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px;
                text-align: center;
                border-radius: 10px 10px 0 0;
            }
            .content {
                background: #f9f9f9;
                padding: 30px;
                border-radius: 0 0 10px 10px;
            }
            .button {
                display: inline-block;
                padding: 12px 30px;
                background: #667eea;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                margin: 20px 0;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
                font-size: 12px;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>🔐 Recuperação de Senha</h1>
        </div>
        <div class="content">
            <p>Olá, <strong>' . htmlspecialchars($nomeUsuario) . '</strong>!</p>
            
            <p>Recebemos uma solicitação para redefinir a senha da sua conta no <strong>RepArte</strong>.</p>
            
            <p>Para criar uma nova senha, clique no botão abaixo:</p>
            
            <p style="text-align: center;">
                <a href="' . htmlspecialchars($linkRecuperacao) . '" class="button">Redefinir Senha</a>
            </p>
            
            <p><strong>Ou copie e cole este link no seu navegador:</strong><br>
            <a href="' . htmlspecialchars($linkRecuperacao) . '">' . htmlspecialchars($linkRecuperacao) . '</a></p>
            
            <p><strong>⚠️ Importante:</strong></p>
            <ul>
                <li>Este link expira em <strong>1 hora</strong></li>
                <li>Se você não solicitou esta recuperação, ignore este email</li>
                <li>Nunca compartilhe este link com ninguém</li>
            </ul>
        </div>
        <div class="footer">
            <p>Este é um email automático, por favor não responda.</p>
            <p>&copy; 2025 RepArte - Todos os direitos reservados</p>
        </div>
    </body>
    </html>
    ';

    return $html;
}

/**
 * Gera HTML para email com código de verificação
 */
function emailCodigoVerificacao($nomeUsuario, $codigo)
{
    $html = '
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Código de Verificação - RepArte</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
            }
            .header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px;
                text-align: center;
                border-radius: 10px 10px 0 0;
            }
            .content {
                background: #f9f9f9;
                padding: 30px;
                border-radius: 0 0 10px 10px;
            }
            .codigo {
                background: white;
                border: 2px dashed #1a0637;
                padding: 20px;
                text-align: center;
                font-size: 32px;
                font-weight: bold;
                letter-spacing: 8px;
                color: #613b9aff;
                margin: 20px 0;
                border-radius: 10px;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
                font-size: 12px;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>✉️ Código de Verificação</h1>
        </div>
        <div class="content">
            <p>Olá' . (!empty($nomeUsuario) ? ', <strong>' . htmlspecialchars($nomeUsuario) . '</strong>' : '') . '!</p>
            
            <p>Bem-vindo ao <strong>RepArte</strong>! Para completar seu cadastro, use o código abaixo:</p>
            
            <div class="codigo">' . htmlspecialchars($codigo) . '</div>
            
            <p style="text-align: center; color: #666;">Digite este código na página de verificação</p>
            
            <p><strong>⚠️ Importante:</strong></p>
            <ul>
                <li>Este código expira em <strong>10 minutos</strong></li>
                <li>Você tem <strong>5 tentativas</strong> para inserir o código correto</li>
                <li>Se você não solicitou este cadastro, ignore este email</li>
                <li>Nunca compartilhe este código com ninguém</li>
            </ul>
        </div>
        <div class="footer">
            <p>Este é um email automático, por favor não responda.</p>
            <p>&copy; 2025 RepArte - Todos os direitos reservados</p>
        </div>
    </body>
    </html>
    ';

    return $html;
}
?>
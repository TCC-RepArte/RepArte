<?php
// Teste de envio de email - Diagnóstico completo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Importar classes no início
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$debug_log = [];
$checks = [];

// 1. Verificar PHPMailer
$debug_log[] = "=== VERIFICANDO PHPMAILER ===";
$paths = [
    __DIR__ . '/../vendor/PHPMailer/src/Exception.php',
    __DIR__ . '/../vendor/PHPMailer/Exception.php',
    __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php',
];

$found = false;
$phpmailer_path = '';
foreach ($paths as $path) {
    if (file_exists($path)) {
        $phpmailer_path = dirname($path);
        $found = true;
        $debug_log[] = "✅ PHPMailer encontrado em: " . $phpmailer_path;
        $checks['phpmailer'] = true;

        // Carregar PHPMailer aqui
        require_once $phpmailer_path . '/Exception.php';
        require_once $phpmailer_path . '/PHPMailer.php';
        require_once $phpmailer_path . '/SMTP.php';
        break;
    }
}

if (!$found) {
    $debug_log[] = "❌ PHPMailer NÃO encontrado!";
    $checks['phpmailer'] = false;
}

// 2. Verificar arquivo de configuração
$debug_log[] = "\n=== VERIFICANDO CONFIGURAÇÕES ===";
$config_file = __DIR__ . '/email_phpmailer.php';
if (file_exists($config_file)) {
    $debug_log[] = "✅ Arquivo de configuração encontrado";
    $checks['config'] = true;

    require_once $config_file;

    $debug_log[] = "Host: " . EMAIL_HOST;
    $debug_log[] = "Port: " . EMAIL_PORT;
    $debug_log[] = "Username: " . EMAIL_USERNAME;
    $debug_log[] = "From: " . EMAIL_FROM;
    $debug_log[] = "API Key: " . substr(EMAIL_PASSWORD, 0, 20) . "...";
} else {
    $debug_log[] = "❌ Arquivo de configuração NÃO encontrado!";
    $checks['config'] = false;
}

// 3. Testar envio se formulário foi submetido
if (isset($_POST['email']) && !empty($_POST['email'])) {
    $debug_log[] = "\n=== TESTANDO ENVIO ===";
    $email_destino = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

    if (!$email_destino) {
        $debug_log[] = "❌ Email inválido: " . $_POST['email'];
    } else {
        $debug_log[] = "📧 Destinatário: " . $email_destino;

        if ($found && file_exists($config_file)) {
            try {
                $mail = new PHPMailer(true);

                // Capturar debug SMTP
                $smtp_debug = [];
                $mail->SMTPDebug = 2;
                $mail->Debugoutput = function ($str, $level) use (&$smtp_debug) {
                    $smtp_debug[] = trim($str);
                };

                // Configurar SMTP
                $mail->isSMTP();
                $mail->Host = EMAIL_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = EMAIL_USERNAME;
                $mail->Password = EMAIL_PASSWORD;
                $mail->SMTPSecure = EMAIL_SMTP_SECURE;
                $mail->Port = EMAIL_PORT;
                $mail->CharSet = 'UTF-8';

                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                // Configurar email
                $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
                $mail->addAddress($email_destino);

                $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

                $mail->isHTML(true);
                $mail->Subject = "Teste de Email - RepArte";
                $mail->Body = templateCodigoVerificacao($codigo);
                $mail->AltBody = "Seu código de teste é: " . $codigo;

                // Tentar enviar
                $debug_log[] = "⏳ Enviando email...";
                $enviado = $mail->send();

                if ($enviado) {
                    $debug_log[] = "✅ EMAIL ENVIADO COM SUCESSO!";
                    $debug_log[] = "Código gerado: " . $codigo;
                    $checks['envio'] = true;
                } else {
                    $debug_log[] = "❌ Falha ao enviar";
                    $checks['envio'] = false;
                }

                // Adicionar debug SMTP
                if (!empty($smtp_debug)) {
                    $debug_log[] = "\n=== DEBUG SMTP ===";
                    foreach ($smtp_debug as $line) {
                        $debug_log[] = $line;
                    }
                }

            } catch (Exception $e) {
                $debug_log[] = "❌ ERRO: " . $e->getMessage();
                $checks['envio'] = false;
            }
        }
    }
}

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Email - RepArte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        h2 {
            color: #667eea;
            margin-top: 30px;
        }

        .status {
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
        }

        form {
            margin: 20px 0;
        }

        input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            padding: 12px 30px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #5568d3;
        }

        .check-item {
            padding: 8px;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🧪 Diagnóstico de Email - RepArte</h1>

        <h2>📊 Status dos Componentes</h2>
        <div
            class="check-item status <?= isset($checks['phpmailer']) ? ($checks['phpmailer'] ? 'success' : 'error') : 'info' ?>">
            <?= isset($checks['phpmailer']) ? ($checks['phpmailer'] ? '✅' : '❌') : '⏸️' ?> PHPMailer
        </div>
        <div
            class="check-item status <?= isset($checks['config']) ? ($checks['config'] ? 'success' : 'error') : 'info' ?>">
            <?= isset($checks['config']) ? ($checks['config'] ? '✅' : '❌') : '⏸️' ?> Configurações
        </div>
        <div
            class="check-item status <?= isset($checks['envio']) ? ($checks['envio'] ? 'success' : 'error') : 'info' ?>">
            <?= isset($checks['envio']) ? ($checks['envio'] ? '✅' : '❌') : '⏸️' ?> Envio de Email
        </div>

        <h2>📧 Testar Envio</h2>
        <form method="POST">
            <label for="email">Digite seu email para receber um código de teste:</label>
            <input type="email" id="email" name="email" placeholder="seu@email.com" required>
            <button type="submit">Enviar Email de Teste</button>
        </form>

        <h2>📝 Log Detalhado</h2>
        <pre><?= htmlspecialchars(implode("\n", $debug_log)) ?></pre>
    </div>
</body>

</html>
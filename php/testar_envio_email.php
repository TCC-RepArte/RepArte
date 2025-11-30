<?php
// Teste de envio de email - Com captura de erro do PHPMailer
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

$debug_log = [];

try {
    $debug_log[] = "1. Iniciando teste de envio de email...";

    if (!isset($_POST['email']) || empty($_POST['email'])) {
        throw new Exception('Email não fornecido');
    }

    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

    if (!$email) {
        throw new Exception('Email inválido');
    }

    $debug_log[] = "2. Email de destino validado: " . $email;

    // Verificar se PHPMailer existe
    $paths = [
        __DIR__ . '/../vendor/PHPMailer/src/Exception.php',
        __DIR__ . '/../vendor/PHPMailer/Exception.php',
        __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php',
    ];

    $found = false;
    $baseDir = '';
    foreach ($paths as $path) {
        if (file_exists($path)) {
            $baseDir = dirname($path);
            $found = true;
            $debug_log[] = "3. PHPMailer encontrado em: " . $baseDir;
            break;
        }
    }

    if (!$found) {
        throw new Exception('PHPMailer não encontrado');
    }

    // Carregar classes
    require_once $baseDir . '/Exception.php';
    require_once $baseDir . '/PHPMailer.php';
    require_once $baseDir . '/SMTP.php';

    $debug_log[] = "4. Classes PHPMailer carregadas";

    // Carregar configurações
    require_once __DIR__ . '/email_phpmailer.php';

    $debug_log[] = "5. Configurações carregadas:";
    $debug_log[] = "   - Host: " . EMAIL_HOST;
    $debug_log[] = "   - Port: " . EMAIL_PORT;
    $debug_log[] = "   - Username: " . EMAIL_USERNAME;
    $debug_log[] = "   - From: " . EMAIL_FROM;

    // Criar instância diretamente
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    $debug_log[] = "6. Instância PHPMailer criada";

    // Ativar DEBUG do SMTP
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function ($str, $level) use (&$debug_log) {
        $debug_log[] = "   [SMTP] " . trim($str);
    };

    // Configurações SMTP
    $mail->isSMTP();
    $mail->Host = EMAIL_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->SMTPSecure = EMAIL_SMTP_SECURE;
    $mail->Port = EMAIL_PORT;
    $mail->CharSet = 'UTF-8';

    $debug_log[] = "7. Configurações SMTP aplicadas";

    // Desabilitar verificação SSL
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Configurar remetente e destinatário
    $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
    $mail->addAddress($email);

    $debug_log[] = "8. Remetente e destinatário configurados";

    // Gerar código
    $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Conteúdo
    $mail->isHTML(true);
    $mail->Subject = "Teste de Email - RepArte";
    $mail->Body = templateCodigoVerificacao($codigo);
    $mail->AltBody = "Seu código de verificação é: " . $codigo;

    $debug_log[] = "9. Email preparado com código: " . $codigo;
    $debug_log[] = "10. Tentando enviar...";

    // Enviar
    $mail->send();

    $debug_log[] = "11. ✅ Email enviado com sucesso!";

    echo json_encode([
        'success' => true,
        'message' => 'Email enviado com sucesso!',
        'codigo' => $codigo,
        'debug' => implode("\n", $debug_log)
    ]);

} catch (Exception $e) {
    $debug_log[] = "❌ ERRO CAPTURADO: " . $e->getMessage();

    echo json_encode([
        'success' => false,
        'message' => 'Erro ao enviar email',
        'error' => $e->getMessage(),
        'debug' => implode("\n", $debug_log)
    ]);
}
?>
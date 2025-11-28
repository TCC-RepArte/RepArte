<?php
// Arquivo de teste para verificar se PHPMailer está funcionando
// COM DEBUG ATIVADO
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Envio de Email - PHPMailer (Debug)</h1>";

// Verificar se arquivo de configuração existe
if (!file_exists('email_phpmailer.php')) {
    die("<p style='color: red'>Erro: Arquivo email_phpmailer.php não encontrado!</p>");
}

require_once 'email_phpmailer.php';

// Tentar carregar PHPMailer manualmente para verificar caminhos
$paths = [
    // Padrão recomendado
    __DIR__ . '/../vendor/PHPMailer/src/Exception.php',
    __DIR__ . '/../vendor/PHPMailer/Exception.php',

    // Caso pasta duplicada (comum ao extrair zip)
    __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php',
    __DIR__ . '/../vendor/PHPMailer/PHPMailer/src/Exception.php',

    // Caso tudo minúsculo (comum em composer)
    __DIR__ . '/../vendor/phpmailer/src/Exception.php',
    __DIR__ . '/../vendor/phpmailer/Exception.php',

    // Caminho absoluto document root
    $_SERVER['DOCUMENT_ROOT'] . '/vendor/PHPMailer/src/Exception.php',
    $_SERVER['DOCUMENT_ROOT'] . '/vendor/phpmailer/phpmailer/src/Exception.php'
];

$found = false;
$foundPath = "";
foreach ($paths as $path) {
    if (file_exists($path)) {
        $found = true;
        $foundPath = $path;
        break;
    }
}

if (!$found) {
    echo "<p style='color: red; font-weight: bold;'>❌ ERRO CRÍTICO: PHPMailer não encontrado!</p>";
    echo "<p>O sistema procurou nos seguintes locais e não achou:</p><ul>";
    foreach ($paths as $path) {
        echo "<li>" . htmlspecialchars($path) . "</li>";
    }
    echo "</ul>";
    echo "<p>Certifique-se de que extraiu a pasta do PHPMailer dentro de <code>htdocs/vendor/PHPMailer/</code></p>";
    exit;
} else {
    echo "<p style='color: green'>✅ PHPMailer encontrado em: " . htmlspecialchars($foundPath) . "</p>";
}

// Email de teste - Usando o mesmo do remetente para evitar erro de relay
$emailTeste = EMAIL_FROM;

echo "<p>Tentando enviar de: <strong>" . EMAIL_USERNAME . "</strong></p>";
echo "<p>Para: <strong>{$emailTeste}</strong></p>";
echo "<hr>";
echo "<h3>Log de Tentativa:</h3>";
echo "<pre style='background: #f0f0f0; padding: 10px; border: 1px solid #ccc; overflow: auto;'>";

// Tentar enviar com DEBUG ativado
try {
    // Carregar classes
    $baseDir = dirname($foundPath);
    require_once $baseDir . '/Exception.php';
    require_once $baseDir . '/PHPMailer.php';
    require_once $baseDir . '/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    // Ativar Debug
    $mail->SMTPDebug = 2; // Mostra conversa completa cliente-servidor
    $mail->Debugoutput = 'html';

    // Configurações
    $mail->isSMTP();
    $mail->Host = EMAIL_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->SMTPSecure = EMAIL_SMTP_SECURE;
    $mail->Port = EMAIL_PORT;
    $mail->CharSet = 'UTF-8';

    // Desabilitar SSL (para teste)
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
    $mail->addAddress($emailTeste);

    $mail->isHTML(true);
    $mail->Subject = "Teste de Debug - RepArte";
    $mail->Body = "<h1>Teste de Email</h1><p>Se você recebeu isso, o PHPMailer está funcionando!</p>";

    $mail->send();
    echo "</pre>";
    echo "<h2 style='color: green'>✅ SUCESSO! Email enviado.</h2>";

} catch (Exception $e) {
    echo "</pre>";
    echo "<h2 style='color: red'>❌ ERRO NO ENVIO</h2>";
    echo "<p><strong>Mensagem de erro:</strong> " . htmlspecialchars($mail->ErrorInfo) . "</p>";

    if (strpos($mail->ErrorInfo, 'SMTP connect() failed') !== false) {
        echo "<div style='background: #ffebee; padding: 10px; border-left: 5px solid red;'>";
        echo "<h3>Possíveis causas:</h3>";
        echo "<ul>";
        echo "<li>O servidor InfinityFree pode estar bloqueando a porta 587. Tente mudar para 465 e 'ssl' no arquivo de configuração.</li>";
        echo "<li>O host 'smtp.gmail.com' pode estar bloqueado.</li>";
        echo "</ul></div>";
    } elseif (strpos($mail->ErrorInfo, 'Username and Password not accepted') !== false) {
        echo "<div style='background: #ffebee; padding: 10px; border-left: 5px solid red;'>";
        echo "<h3>Erro de Autenticação:</h3>";
        echo "<ul>";
        echo "<li>Sua senha de app está incorreta.</li>";
        echo "<li>Seu email está incorreto.</li>";
        echo "<li>Você usou a senha normal do Gmail (precisa ser Senha de App).</li>";
        echo "</ul></div>";
    }
}
?>
<?php
// Enviar código de verificação no cadastro usando PHPMailer
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
require_once 'config.php';
require_once 'limpar_dados_temporarios.php';

header('Content-Type: application/json');

try {
    unset($_SESSION['erros_verificacao']);

    global $con;

    if ($con->connect_error) {
        throw new Exception('Não foi possível conectar ao banco de dados');
    }

    if (!isset($_POST['email']) || empty($_POST['email'])) {
        throw new Exception('Email não fornecido');
    }

    $email = $con->real_escape_string(trim($_POST['email']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    // Verificar se email já cadastrado
    $stmt = $con->prepare("SELECT id FROM login WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Este email já está cadastrado'
        ]);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Verificar se já existe código válido
    $stmt = $con->prepare("SELECT id, codigo FROM codigos_verificacao WHERE email = ? AND usado = 0 AND tentativas < 5 AND data_expiracao > NOW() ORDER BY data_criacao DESC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $codigo_existente = $resultado->fetch_assoc();

        echo json_encode([
            'success' => true,
            'message' => 'Código já foi enviado. Verifique seu email.',
            'codigo' => $codigo_existente['codigo'],
            'modo_dev' => true
        ]);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Gerar código de 6 dígitos
    $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Definir expiração
    $data_criacao = date('Y-m-d H:i:s');
    $data_expiracao = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    // Inserir no banco
    $stmt = $con->prepare("INSERT INTO codigos_verificacao (email, codigo, data_criacao, data_expiracao, usado, tentativas) VALUES (?, ?, ?, ?, 0, 0)");
    $stmt->bind_param("ssss", $email, $codigo, $data_criacao, $data_expiracao);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao gerar código: ' . $stmt->error);
    }
    $stmt->close();

    // Tentar enviar email com PHPMailer
    $emailEnviado = false;

    try {
        require_once 'email_phpmailer.php';

        $assunto = "Codigo de Verificacao - RepArte";
        $mensagem = templateCodigoVerificacao($codigo);

        $emailEnviado = enviarEmailPHPMailer($email, $assunto, $mensagem, true);

    } catch (Exception $e) {
        error_log("Erro ao enviar email: " . $e->getMessage());
    }

    // Retornar resposta
    echo json_encode([
        'success' => true,
        'message' => $emailEnviado ? 'Código enviado para seu email!' : 'Código gerado (verifique abaixo)',
        'codigo' => $codigo,
        'modo_dev' => true,
        'email_enviado' => $emailEnviado
    ]);

    $con->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    error_log("Erro em enviar_codigo_cadastro.php: " . $e->getMessage());
}
?>
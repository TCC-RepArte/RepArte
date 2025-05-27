<?php
ob_start();

// Prevenir cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

session_start();

global $con;
global $id;
$con = new mysqli("localhost", "root", '', "reparte");

$erros = [];

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

    if (strpos($contentType, 'application/json') !== false || $isAjax) {
        // Requisição JSON (JavaScript)
        header('Content-Type: application/json');
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        processarRequisicaoJson($data);
    } else {
        // Requisição via formulário tradicional
        error_log("Requisição recebida: " . print_r($_POST, true));
        processarFormulario($_POST);
    }
}

// Processa requisições JSON (tipo a do fetch do JavaScript)
function processarRequisicaoJson($data)
{
    header('Content-Type: application/json');
    try {
        if (isset($data['id'])) {
            global $con;
            $id = $data['id'];
            
            // Verifica se o ID já existe no banco
            $stmt = $con->prepare("SELECT id FROM login WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Erro ao preparar consulta: " . $con->error);
            }
            
            $stmt->bind_param("s", $id);
            if (!$stmt->execute()) {
                throw new Exception("Erro ao executar consulta: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID já existe',
                    'duplicate' => true
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'ID disponível',
                    'id' => $id
                ]);
            }
            $stmt->close();
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'ID não recebido'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao processar requisição: ' . $e->getMessage()
        ]);
    }
}


function processarFormulario($post)
{ 
    global $erros;
    global $con;

    // Obtém o ID do formulário
    $id = $_POST['id'] ?? null;
    $email_bd = '';

    // Se o ID estiver ausente, exibe mensagem de erro
    if (empty($id)) {
        $erros[] = "ID ausente. Tente novamente.";
        return;
    }

    // Validação e processamento do formulário
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $usuario = $_POST["usuario"];
    $senha_conf = $_POST["confsenha"];

    // Validando a entrada de dados
    if (empty($email) || empty($senha) || empty($usuario)) {
        $erros[] = "Estão faltando dados!";
        return;
    }

    // Validando o email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido";
        return;
    }

    // Verificando se o email já existe
    $stmt = $con->prepare("SELECT email FROM login WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($email_bd);
    $stmt->fetch();

    if ($email_bd == $email) {
        $erros[] = "O e-mail já existe!";
        return;
    }

    $stmt->close();

    // Verificando se o usuário já existe
    $usuario_def = '@' . $usuario;
    $usuario_bd = '';
    $stmt = $con->prepare("SELECT `usuario` FROM `login` WHERE usuario = ?");
    $stmt->bind_param("s", $usuario_def);
    $stmt->execute();
    $stmt->bind_result($usuario_bd);
    $stmt->fetch();


    if ($usuario_bd == $usuario_def) {
        $erros[] = "Usuário já existe";
        return;
    }

    $stmt->close();

    // Validando as senhas
    if ($senha_conf !== $senha) {
        $erros[] = "Senha não correspondente!";
        return;
    }

    if (empty($erros)) {
        // Criptografando a senha e inserindo dados no banco de dados
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $con->prepare("INSERT INTO login (usuario, email, senha, id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $usuario_def, $email, $senha_hash, $id);
        $novo_id = $con->insert_id; // pega o último id inserido
        $_SESSION['id'] = $novo_id;

        if ($stmt->execute()) {
            $_SESSION['id'] = $con->insert_id;
            // redireciona para o perfil ou signup2.php
        } else {
            $_SESSION['erro_cadastro'] = "Erro ao cadastrar usuário: " . $stmt->error;
            header("Location: ../../html/signup.php");
            exit();
        }

        if ($stmt->execute()) {
            $_SESSION['id'] = $id;
            $_SESSION['user_id'] = $id; // Para compatibilidade com o sistema de login
            $_SESSION['usuario'] = $usuario_def;
            $_SESSION['email'] = $email;
            $_SESSION['mensagem_sucesso'] = "Cadastro realizado com sucesso!";
            
            // Forçar limpeza do buffer de saída
            if (ob_get_length()) ob_clean();
            // Forçar redirecionamento
            header("HTTP/1.1 302 Found");
            header("Location: ../../html/perfil.php");
            exit();
        } else {
            $erros[] = "Erro ao enviar formulário: " . $stmt->error;
        }

        $dados_login = [$usuario_def, $email, $senha_hash, $id];
        $_SESSION['login'] = $dados_login;
        $stmt->close();
    }

    // Se houver erros, redireciona de volta para o formulário
    if (!empty($erros)) {
        $_SESSION['erros'] = $erros;
        // Forçar limpeza do buffer de saída
        if (ob_get_length()) ob_clean();
        // Forçar redirecionamento
        header("HTTP/1.1 302 Found");
        header("Location: ../../html/cadastro.php");
        exit();
    }
}

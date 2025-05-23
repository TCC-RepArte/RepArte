<?php

session_start();
global $con;
global $id;
$con = new mysqli("localhost", "root", '', "reparte");

$erros = [];
// Executa a limpeza de registros temporários
limparRegistrosTemporarios();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

    if (strpos($contentType, 'application/json') !== false) {
        // Requisição JSON (JavaScript)
        header('Content-Type: application/json');
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        processarRequisicaoJson($data);
    } else {
        // Requisição via formulário tradicional
        processarFormulario($_POST);
    }
}

// Processa requisições JSON (como a do fetch do JavaScript)
function processarRequisicaoJson($data)
{
    if (isset($data['id'])) {
        global $con;
        $id = $data['id'];
        
        // Verifica se o ID já existe no banco
        $stmt = $con->prepare("SELECT id FROM login WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
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
}

// Função para limpar registros temporários antigos
function limparRegistrosTemporarios() {
    global $con;
    // Remove registros temporários com mais de 1 hora e não completados
    $stmt = $con->prepare("DELETE FROM temp_signup WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR) AND completed = 0");
    $stmt->execute();
    $stmt->close();
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
        // Criptografando a senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        // Iniciando transação
        $con->begin_transaction();
        
        try {
            // Inserindo dados na tabela temporária
            $stmt = $con->prepare("INSERT INTO temp_signup (id, usuario, email, senha) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $id, $usuario_def, $email, $senha_hash);
            
            if (!$stmt->execute()) {
                throw new Exception("Erro ao salvar dados temporários");
            }
            
            $stmt->close();
            
            // Armazenando ID na sessão
            $_SESSION['id'] = $id;
            $_SESSION['signup_in_progress'] = true;
            
            // Commit da transação
            $con->commit();
            
            header("Location: signup2.php");
            exit;
        } catch (Exception $e) {
            // Rollback em caso de erro
            $con->rollback();
            $erros[] = "Erro ao processar cadastro: " . $e->getMessage();
        }
    }

    // Se houver erros, redireciona de volta para o formulário
    if (!empty($erros)) {
        $_SESSION['erros'] = $erros;
        header("Location: ../../web/html/cadastro.php");
        exit;
    }
}

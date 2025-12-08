<?php
session_start();

// Prevenir cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Configurar tratamento de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'config.php';
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao carregar configuração: ' . $e->getMessage()
    ]);
    exit;
}

$erros = [];

// Verificar se veio da verificação de código (sem POST mas com sessão)
if (isset($_SESSION['codigo_verificado']) && $_SESSION['codigo_verificado'] === true && isset($_SESSION['dados_cadastro'])) {
    // Processar cadastro com dados da sessão
    processarFormulario([]);
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

    if (strpos($contentType, 'application/json') !== false || $isAjax) {
        // Requisição JSON (JavaScript)
        header('Content-Type: application/json');

        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON inválido: ' . json_last_error_msg());
            }

            processarRequisicaoJson($data);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao processar JSON: ' . $e->getMessage()
            ]);
        }
    } else {
        // Requisição via formulário tradicional  
        error_log("Requisição recebida: " . print_r($_POST, true));
        processarFormulario($_POST);
    }
} else {
    // Sem POST e sem sessão válida - redirecionar para cadastro
    $_SESSION['erros'] = ['Acesso inválido. Por favor, preencha o cadastro.'];
    header("Location: ../cadastro.php");
    exit();
}

// Processa requisições JSON
function processarRequisicaoJson($data)
{
    try {
        if (isset($data['idCriado'])) {
            global $con;

            // Verificar conexão
            if ($con->connect_error) {
                throw new Exception("Erro de conexão com banco: " . $con->connect_error);
            }

            $id = $data['idCriado'];

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
        error_log("Erro em processarRequisicaoJson: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao processar requisição: ' . $e->getMessage()
        ]);
    }
}

// Processa formulário tradicional
function processarFormulario($dados)
{
    global $con, $erros;

    try {
        // Verificar conexão
        if ($con->connect_error) {
            throw new Exception("Erro de conexão com banco: " . $con->connect_error);
        }

        // Verificar se veio da verificação de código (dados na sessão)
        if (isset($_SESSION['codigo_verificado']) && $_SESSION['codigo_verificado'] === true && isset($_SESSION['dados_cadastro'])) {
            // Pegar dados da sessão
            $dadosCadastro = $_SESSION['dados_cadastro'];
            $usuario = strtolower(trim($dadosCadastro['usuario'])); // Converter para minúsculas
            $email = trim($dadosCadastro['email']);
            $senha = trim($dadosCadastro['senha']);
            $id = trim($dadosCadastro['id']);
            $confsenha = $senha; // Já foi validado antes

            // Limpar dados da sessão
            unset($_SESSION['codigo_verificado']);
            unset($_SESSION['dados_cadastro']);
            unset($_SESSION['email_verificado']);
        } else {
            // Cadastro direto sem verificação (não permitir)
            $_SESSION['erros'] = ['É necessário verificar o código de cadastro'];
            header("Location: ../cadastro.php");
            exit();
        }


        // Validações básicas
        if (empty($usuario)) {
            $erros[] = "Nome de usuário é obrigatório!";
        }
        if (empty($email)) {
            $erros[] = "Email é obrigatório!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = "Email inválido!";
        }
        if (empty($senha)) {
            $erros[] = "Senha é obrigatória!";
        } elseif (strlen($senha) < 6) {
            $erros[] = "Senha deve ter pelo menos 6 caracteres!";
        }
        if ($senha !== $confsenha) {
            $erros[] = "Senhas não coincidem!";
        }
        if (empty($id)) {
            $erros[] = "ID é obrigatório!";
        }

        // Verificar se email já existe
        if (empty($erros)) {
            $stmt = $con->prepare("SELECT id FROM login WHERE email = ?");
            if (!$stmt) {
                throw new Exception("Erro ao preparar consulta: " . $con->error);
            }

            $stmt->bind_param("s", $email);
            if (!$stmt->execute()) {
                throw new Exception("Erro ao executar consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $erros[] = "Este email já está cadastrado!";
            }
            $stmt->close();
        }

        // Verificar se nome de usuário já existe
        if (empty($erros)) {
            $stmt = $con->prepare("SELECT id FROM login WHERE usuario = ?");
            if (!$stmt) {
                throw new Exception("Erro ao preparar consulta: " . $con->error);
            }

            $stmt->bind_param("s", $usuario);
            if (!$stmt->execute()) {
                throw new Exception("Erro ao executar consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $erros[] = "Este nome de usuário já está em uso!";
            }
            $stmt->close();
        }

        // Se não há erros, inserir no banco
        if (empty($erros)) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $con->prepare("INSERT INTO login (id, usuario, email, senha) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Erro ao preparar inserção: " . $con->error);
            }

            $stmt->bind_param("ssss", $id, $usuario, $email, $senhaHash);

            if ($stmt->execute()) {
                $_SESSION['id'] = $id;
                $_SESSION['usuario'] = $usuario;
                $_SESSION['email'] = $email;

                header("Location: ../perfil.php");
                exit();
            } else {
                throw new Exception("Erro ao inserir usuário: " . $stmt->error);
            }
            $stmt->close();
        }

        // Se há erros, mostrar na sessão
        if (!empty($erros)) {
            $_SESSION['erros'] = $erros;
            header("Location: ../cadastro.php");
            exit();
        }

    } catch (Exception $e) {
        error_log("Erro em processarFormulario: " . $e->getMessage());
        $_SESSION['erros'] = ['Erro interno do servidor: ' . $e->getMessage()];
        header("Location: ../cadastro.php");
        exit();
    }
}
?>
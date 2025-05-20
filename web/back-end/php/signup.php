<?php

require_once '../../../confidencial/php/conexao.php';

$erros = [];
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : '';

    // Processar baseado no tipo de conteúdo
    if (strpos($contentType, 'application/json') !== false) {

        // Requisição JSON (do JavaScript)
        header('Content-Type: application/json');
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Executar lógica específica para JSON
        processarRequisicaoJson($data);
    } else {
        //Executar lógica específica para formulário
        processarFormulario($_POST);
    }
}

// Funções para processar cada tipo
function processarRequisicaoJson($data)
{

    global $con;
    global $erros; 

    if (isset($data['id'])) {

        $id = $data['id'];
        $id_bd = '';

        // Verificar se ID já existe
        $stmt = $con->prepare("SELECT id FROM `login` WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->bind_result($id_bd);
        $stmt->fetch();

        // Se ID já existe, envia aviso pro JS refazer função
        if ($id_bd == $id){

            echo json_encode([
                'success' => false,
                'message' => 'ID já existe',
                'duplicate' => true 
            ]);
            return;
        }

        try {

            $_SESSION['user_id'] = $id;

            echo json_encode(['success' => true, 'message' => 'ID inserido com sucesso']);

        } catch (Exception $e) {

            echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }

    } 
    
    else {
        echo json_encode(['success' => false, 'message' => 'ID não recebido']);
        return;
    }

}

function processarFormulario($post)
{

    global $con;
    $idc = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    global $erros;

    // Validação e processamento do formulário
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $usuario = $_POST["usuario"];
    $senha_conf = $_POST["confsenha"];

    //Validando a entrada de dados
    if (empty($email) || empty($senha) || empty($usuario)) {
        $erros[] = "Estão faltando dados!";
        return;
        
    }

    //Validando o email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido";
        return;
    }

    //Verificando se o email já existe
    $email_bd = '';
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

    //trazendo usuario para padrão de referência
    $usuario_def = '@' . $usuario;
    $usuario_bd= '';

    //Verificando se o usúario já existe
    $stmt = $con->prepare("SELECT `usuario` FROM `login` WHERE usuario = ? ");
    $stmt->bind_param("s", $usuario_def);
    $stmt->execute();
    $stmt->bind_result($usuario_bd);
    $stmt->fetch();

    $stmt->close();

    if ($usuario_bd == $usuario_def) {
        $erros[] = "Usúario já existe";
        return;
    } 

    if($senha_conf !== $senha){
        $erros[] = "Senha não correspondente!";
        return;
    }

    if(empty($erros)){

        //Criptografando a senha e inserindo dados ao banco de dados
        $senha_has = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $con->prepare("INSERT INTO `login`(`usuario`, `email`, `senha`, `id`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $usuario_def, $email, $senha_has, $idc);

        //Validando entrada de dados
        if ($stmt->execute()){
            header("Location: fotono.php");
        } else {
            $erros[] = "Erro ao enviar formulário!";
        }

        $stmt->close();

    }

}
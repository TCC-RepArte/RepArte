<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/json'); 

session_start();

// Verificar se já está logado e foi uma requisição AJAX
if(isset($_SESSION['user_id']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
    echo json_encode([
        'sucesso' => true, 
        'mensagem' => 'Usuário já logado',
        'redirect' => '../../web/html/telainicial.php'
    ]);
    exit();
}

// Verificar se já está logado e foi uma requisição direta (não AJAX)
if(isset($_SESSION['user_id']) && (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')){
    header('Location: ../../web/html/telainicial.php');
    exit();
}

// Conexão com o banco
$con = new mysqli("localhost", "root", '', "reparte");

// Verificar conexão
if ($con->connect_error) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro na conexão com o banco: ' . $con->connect_error]);
    exit();
}

if(isset($_POST['usuario']) && isset($_POST['senha'])){
    // Limpar os dados recebidos
    $usuario = $con->real_escape_string($_POST['usuario']);
    $senha = $_POST['senha']; 

    // Verificar se é email ou nome de usuário
    if(filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
        $stmt = $con->prepare("SELECT id, usuario, email, senha FROM login WHERE email = ?");
    } else {
        $stmt = $con->prepare("SELECT id, usuario, email, senha FROM login WHERE usuario = ?");
    }

    if (!$stmt) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro na preparação da query: ' . $con->error]);
        exit();
    }

    $stmt->bind_param("s", $usuario);
    
    if (!$stmt->execute()) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro na execução da query: ' . $stmt->error]);
        exit();
    }

    $resultado = $stmt->get_result();

    if($resultado->num_rows > 0){
        $usuario = $resultado->fetch_assoc();
        
        // Verificar a senha
        if(password_verify($senha, $usuario['senha'])) {
            // Armazenar informações na sessão
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['usuario'] = $usuario['usuario'];
            $_SESSION['email'] = $usuario['email'];
            
            echo json_encode([
                'sucesso' => true, 
                'mensagem' => 'Login realizado com sucesso',
                'redirect' => '../../web/html/telainicial.php'
            ]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Senha incorreta']);
            
            // Se não for uma requisição AJAX, usar mensagem de sessão
            if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                $_SESSION['mensagem_erro'] = 'Senha incorreta';
                header('Location: ../../html/login1.php');
                exit();
            }
        }
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado']);
        
        // Se não for uma requisição AJAX, usar mensagem de sessão
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $_SESSION['mensagem_erro'] = 'Usuário não encontrado';
            header('Location: ../../html/login1.php');
            exit();
        }
    }

    $stmt->close();
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
    
    // Se não for uma requisição AJAX, usar mensagem de sessão
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        $_SESSION['mensagem_erro'] = 'Dados incompletos';
        header('Location: ../../html/login1.php');
        exit();
    }
}

$con->close();
?>
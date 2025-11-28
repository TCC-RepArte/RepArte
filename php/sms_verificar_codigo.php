<?php
// Arquivo para verificar código SMS e fazer login
session_start();
require 'config.php';

unset($_SESSION['erros_verificacao']);
unset($_SESSION['val_verificacao']);

global $con;

if ($con->connect_error) {
    $_SESSION['erros_verificacao'] = ['Não foi possível conectar ao banco'];
    header('Location: ../verificar_sms.php');
    exit();
}

if(!isset($_SESSION['telefone_sms'])) {
    $_SESSION['erros_sms'] = ['Sessão expirada. Tente novamente'];
    header('Location: ../login_sms.php');
    exit();
}

if(isset($_POST['codigo'])){
    $codigo = $con->real_escape_string($_POST['codigo']);
    $telefone = $_SESSION['telefone_sms'];
    
    // Buscar código no banco
    $stmt = $con->prepare("SELECT id, usado, data_expiracao, tentativas FROM codigos_sms WHERE telefone = ? AND codigo = ? ORDER BY data_criacao DESC LIMIT 1");
    $stmt->bind_param("ss", $telefone, $codigo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if($resultado->num_rows > 0) {
        $registro = $resultado->fetch_assoc();
        
        // Verificar se já foi usado
        if($registro['usado'] == 1) {
            $_SESSION['erros_verificacao'] = ['Código já foi utilizado'];
            header('Location: ../verificar_sms.php');
            exit();
        }
        
        // Verificar se expirou
        $agora = date('Y-m-d H:i:s');
        if($agora > $registro['data_expiracao']) {
            $_SESSION['erros_verificacao'] = ['Código expirado. Solicite um novo'];
            header('Location: ../login_sms.php');
            exit();
        }
        
        // Código válido! Marcar como usado
        $stmt = $con->prepare("UPDATE codigos_sms SET usado = 1 WHERE id = ?");
        $stmt->bind_param("i", $registro['id']);
        $stmt->execute();
        
        // Verificar se usuário já existe
        $stmt = $con->prepare("SELECT id, usuario, email FROM login WHERE telefone = ?");
        $stmt->bind_param("s", $telefone);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if($resultado->num_rows > 0) {
            // Usuário existe, fazer login
            $usuario = $resultado->fetch_assoc();
            
            $_SESSION['usuario'] = $usuario['usuario'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['val'] = ['Login com SMS realizado com sucesso!'];
            
            // Limpar dados SMS da sessão
            unset($_SESSION['telefone_sms']);
            unset($_SESSION['codigo_enviado']);
            
            header('Location: ../telainicial.php');
            exit();
        } else {
            // Novo usuário, criar conta
            $id = bin2hex(random_bytes(18)); // ID de 36 caracteres
            $usuario = '@user' . substr($telefone, -4); // Usuário baseado nos últimos 4 dígitos
            $email = 'sms_' . $telefone . '@reparte.local'; // Email temporário
            $senhaAleatoria = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            
            $stmt = $con->prepare("INSERT INTO login (id, usuario, email, senha, telefone, metodo_login) VALUES (?, ?, ?, ?, ?, 'telefone')");
            $stmt->bind_param("sssss", $id, $usuario, $email, $senhaAleatoria, $telefone);
            
            if($stmt->execute()) {
                $_SESSION['usuario'] = $usuario;
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $id;
                $_SESSION['val'] = ['Conta criada com sucesso!'];
                
                // Limpar dados SMS da sessão
                unset($_SESSION['telefone_sms']);
                unset($_SESSION['codigo_enviado']);
                
                header('Location: ../perfil.php'); // Redirecionar para completar perfil
                exit();
            } else {
                $_SESSION['erros_verificacao'] = ['Erro ao criar conta'];
                header('Location: ../verificar_sms.php');
                exit();
            }
        }
        
    } else {
        // Código inválido, incrementar tentativas
        $stmt = $con->prepare("UPDATE codigos_sms SET tentativas = tentativas + 1 WHERE telefone = ? AND usado = 0 ORDER BY data_criacao DESC LIMIT 1");
        $stmt->bind_param("s", $telefone);
        $stmt->execute();
        
        $_SESSION['erros_verificacao'] = ['Código inválido'];
        header('Location: ../verificar_sms.php');
        exit();
    }
    
    $stmt->close();
} else {
    $_SESSION['erros_verificacao'] = ['Código não informado'];
    header('Location: ../verificar_sms.php');
    exit();
}

$con->close();
?>


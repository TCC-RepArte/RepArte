<?php
// Callback do Google OAuth
session_start();
require 'config.php';
require 'google_config.php';

global $con;

// Verificar se recebeu o código
if(!isset($_GET['code'])) {
    $_SESSION['erros'] = ['Erro ao autenticar com Google'];
    header('Location: ../login1.php');
    exit();
}

try {
    // Trocar código por token de acesso
    $tokenData = getGoogleAccessToken($_GET['code']);
    
    if(!isset($tokenData['access_token'])) {
        throw new Exception('Erro ao obter token de acesso');
    }
    
    // Obter informações do usuário
    $userInfo = getGoogleUserInfo($tokenData['access_token']);
    
    if(!isset($userInfo['id']) || !isset($userInfo['email'])) {
        throw new Exception('Erro ao obter informações do usuário');
    }
    
    $googleId = $userInfo['id'];
    $email = $userInfo['email'];
    $nome = $userInfo['name'] ?? 'Usuário Google';
    
    // Verificar se usuário já existe pelo Google ID
    $stmt = $con->prepare("SELECT id, usuario, email FROM login WHERE google_id = ?");
    $stmt->bind_param("s", $googleId);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if($resultado->num_rows > 0) {
        // Usuário já existe, fazer login
        $usuario = $resultado->fetch_assoc();
        
        $_SESSION['usuario'] = $usuario['usuario'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['val'] = ['Login com Google realizado com sucesso!'];
        
        header('Location: ../telainicial.php');
        exit();
    } else {
        // Verificar se já existe usuário com este email
        $stmt = $con->prepare("SELECT id FROM login WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if($resultado->num_rows > 0) {
            // Email já cadastrado, vincular Google ID
            $usuario = $resultado->fetch_assoc();
            
            $stmt = $con->prepare("UPDATE login SET google_id = ?, metodo_login = 'google' WHERE id = ?");
            $stmt->bind_param("ss", $googleId, $usuario['id']);
            $stmt->execute();
            
            // Buscar dados atualizados
            $stmt = $con->prepare("SELECT id, usuario, email FROM login WHERE id = ?");
            $stmt->bind_param("s", $usuario['id']);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $usuario = $resultado->fetch_assoc();
            
            $_SESSION['usuario'] = $usuario['usuario'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['val'] = ['Conta vinculada ao Google com sucesso!'];
            
            header('Location: ../telainicial.php');
            exit();
        } else {
            // Novo usuário, criar conta
            $id = bin2hex(random_bytes(18)); // ID de 36 caracteres
            $usuario = '@' . explode('@', $email)[0]; // Usuário baseado no email
            $senhaAleatoria = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            
            $stmt = $con->prepare("INSERT INTO login (id, usuario, email, senha, google_id, metodo_login) VALUES (?, ?, ?, ?, ?, 'google')");
            $stmt->bind_param("sssss", $id, $usuario, $email, $senhaAleatoria, $googleId);
            
            if($stmt->execute()) {
                $_SESSION['usuario'] = $usuario;
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $id;
                $_SESSION['val'] = ['Conta criada e login realizado com sucesso!'];
                
                header('Location: ../perfil.php'); // Redirecionar para completar perfil
                exit();
            } else {
                throw new Exception('Erro ao criar conta');
            }
        }
    }
    
} catch (Exception $e) {
    error_log('Erro no login Google: ' . $e->getMessage());
    $_SESSION['erros'] = ['Erro ao fazer login com Google: ' . $e->getMessage()];
    header('Location: ../login1.php');
    exit();
}
?>


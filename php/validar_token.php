<?php
// Arquivo para validar token de recuperação
require 'config.php';

global $con;

// Função para validar token
function validarToken($token) {
    global $con;
    
    // Verificar se o token existe e não expirou
    $stmt = $con->prepare("SELECT id, id_usuario, usado, data_expiracao FROM recuperacao_senha WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if($resultado->num_rows > 0) {
        $dados = $resultado->fetch_assoc();
        
        // Verificar se o token já foi usado
        if($dados['usado'] == 1) {
            return ['valido' => false, 'erro' => 'Token já foi utilizado'];
        }
        
        // Verificar se o token expirou
        $agora = date('Y-m-d H:i:s');
        if($agora > $dados['data_expiracao']) {
            return ['valido' => false, 'erro' => 'Token expirado'];
        }
        
        return ['valido' => true, 'id_usuario' => $dados['id_usuario'], 'id_token' => $dados['id']];
    }
    
    return ['valido' => false, 'erro' => 'Token inválido'];
}
?>

